<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeoDomain;

class ManageGeoDomains extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'geo:domains 
                            {action=list : Action to perform (list, activate, deactivate, add)}
                            {--country= : Country code (e.g., AE, PK)}
                            {--domain= : Domain name}
                            {--name= : Country name}';

    /**
     * The console command description.
     */
    protected $description = 'Manage geo-based domain redirection';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'list' => $this->listDomains(),
            'activate' => $this->activateDomain(),
            'deactivate' => $this->deactivateDomain(),
            'add' => $this->addDomain(),
            default => $this->invalidAction(),
        };
    }

    /**
     * List all geo domains
     */
    protected function listDomains(): int
    {
        $domains = GeoDomain::orderBy('priority', 'desc')
            ->orderBy('country_name')
            ->get();

        if ($domains->isEmpty()) {
            $this->warn('No geo domains configured.');
            $this->info('Run: php artisan db:seed --class=GeoDomainSeeder');
            return 0;
        }

        $this->table(
            ['Country Code', 'Country Name', 'Domain', 'Active', 'Priority'],
            $domains->map(fn($d) => [
                $d->country_code,
                $d->country_name,
                $d->domain,
                $d->is_active ? '✓ Yes' : '✗ No',
                $d->priority,
            ])
        );

        $activeCount = $domains->where('is_active', true)->count();
        $this->info("Total: {$domains->count()} domains ({$activeCount} active)");

        return 0;
    }

    /**
     * Activate a domain
     */
    protected function activateDomain(): int
    {
        $countryCode = $this->option('country');

        if (!$countryCode) {
            $this->error('Please provide a country code with --country=XX');
            return 1;
        }

        $domain = GeoDomain::where('country_code', strtoupper($countryCode))->first();

        if (!$domain) {
            $this->error("No domain found for country code: {$countryCode}");
            return 1;
        }

        $domain->update(['is_active' => true]);
        $this->info("Activated domain: {$domain->domain} ({$domain->country_name})");

        return 0;
    }

    /**
     * Deactivate a domain
     */
    protected function deactivateDomain(): int
    {
        $countryCode = $this->option('country');

        if (!$countryCode) {
            $this->error('Please provide a country code with --country=XX');
            return 1;
        }

        $domain = GeoDomain::where('country_code', strtoupper($countryCode))->first();

        if (!$domain) {
            $this->error("No domain found for country code: {$countryCode}");
            return 1;
        }

        $domain->update(['is_active' => false]);
        $this->warn("Deactivated domain: {$domain->domain} ({$domain->country_name})");

        return 0;
    }

    /**
     * Add a new domain
     */
    protected function addDomain(): int
    {
        $countryCode = $this->option('country');
        $domain = $this->option('domain');
        $name = $this->option('name');

        if (!$countryCode || !$domain || !$name) {
            $this->error('Please provide all required options:');
            $this->line('  --country=XX --domain=xx.massagerepublic.com.co --name="Country Name"');
            return 1;
        }

        $existing = GeoDomain::where('country_code', strtoupper($countryCode))->first();

        if ($existing) {
            if (!$this->confirm("Domain already exists for {$countryCode}. Update it?")) {
                return 0;
            }
        }

        GeoDomain::updateOrCreate(
            ['country_code' => strtoupper($countryCode)],
            [
                'country_name' => $name,
                'domain' => $domain,
                'is_active' => $this->confirm('Activate this domain now?'),
            ]
        );

        $this->info("Domain saved: {$domain} ({$name})");

        return 0;
    }

    /**
     * Invalid action
     */
    protected function invalidAction(): int
    {
        $this->error('Invalid action. Available actions: list, activate, deactivate, add');
        return 1;
    }
}
