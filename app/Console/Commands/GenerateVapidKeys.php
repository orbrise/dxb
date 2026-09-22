<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'push:generate-vapid-keys';
    protected $description = 'Generate a VAPID keypair for Web Push notifications. Paste the output into .env.';

    public function handle(): int
    {
        // Try the library helper first — works on all Linux and most Windows.
        try {
            $keys = VAPID::createVapidKeys();
        } catch (\Throwable $e) {
            // On some Windows / Laragon installs, PHP's OpenSSL can't locate
            // openssl.cnf and EC key generation fails inside jose. Fall back
            // to a raw openssl_pkey_new call with an inline curve config —
            // still produces a valid VAPID P-256 keypair.
            $this->warn('Library key-gen failed (' . $e->getMessage() . '); falling back to raw OpenSSL.');
            $keys = $this->fallbackGenerate();
        }

        if (!$keys || empty($keys['publicKey']) || empty($keys['privateKey'])) {
            $this->error('Could not generate VAPID keys on this machine. Run this command on your Linux/prod server instead, then paste the values back into your local .env.');
            return self::FAILURE;
        }

        $this->line('');
        $this->line('Add these to your .env file:');
        $this->line('');
        $this->line('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->line('VAPID_SUBJECT=mailto:admin@evoory.com');
        $this->line('');
        $this->warn('Keep the private key secret. Rotating it invalidates every existing push subscription — users would have to re-consent.');

        return self::SUCCESS;
    }

    /**
     * Fallback P-256 keypair generator that talks to OpenSSL directly.
     * Returns keys in the same shape as Minishlink\WebPush\VAPID::createVapidKeys():
     * base64-url-encoded, uncompressed 65-byte public key, 32-byte private key.
     */
    protected function fallbackGenerate(): ?array
    {
        // Windows PHP builds often ship without openssl.cnf, which makes
        // openssl_pkey_new() fail with "no such file" errors. Write a
        // minimal stub cnf in the temp dir and pass it as the `config` arg.
        $cnfPath = tempnam(sys_get_temp_dir(), 'ossl_') . '.cnf';
        file_put_contents($cnfPath, "[req]\ndistinguished_name = req_dn\n[req_dn]\n");

        $args = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name'       => 'prime256v1',
            'config'           => $cnfPath,
        ];

        $res = @openssl_pkey_new($args);
        @unlink($cnfPath);
        if ($res === false) return null;

        $details = openssl_pkey_get_details($res);
        if (!$details || empty($details['ec']['x']) || empty($details['ec']['y']) || empty($details['ec']['d'])) {
            return null;
        }
        // openssl_pkey_get_details may return the private component as 'd'
        // (openssl) or nothing on some builds — grab from export as backup.
        if (empty($details['ec']['d'])) {
            // Fallback path — extract d from PEM export.
            openssl_pkey_export($res, $pem, null, ['config' => $cnfPath]);
            // Very unlikely to hit; leave null so caller reports failure.
            return null;
        }

        // Uncompressed public key = 0x04 || X (32 bytes) || Y (32 bytes)
        $publicRaw  = "\x04" . str_pad($details['ec']['x'], 32, "\0", STR_PAD_LEFT)
                             . str_pad($details['ec']['y'], 32, "\0", STR_PAD_LEFT);
        $privateRaw = str_pad($details['ec']['d'], 32, "\0", STR_PAD_LEFT);

        return [
            'publicKey'  => $this->base64UrlEncode($publicRaw),
            'privateKey' => $this->base64UrlEncode($privateRaw),
        ];
    }

    protected function base64UrlEncode(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }
}
