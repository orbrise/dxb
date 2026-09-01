<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 400px;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .success-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        h1 {
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 24px;
        }
        p {
            color: #6b7280;
            margin-bottom: 20px;
        }
        .closing-text {
            font-size: 14px;
            color: #9ca3af;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1>Payment Successful!</h1>
        <p>Your payment has been processed successfully.</p>
        <div class="spinner"></div>
        <p class="closing-text">This window will close automatically...</p>
    </div>

    <script>
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const referenceId = urlParams.get('reference_id') || urlParams.get('ref');
            const status = urlParams.get('status') || 'success';
            const errorCode = urlParams.get('error_code');
            const declineCode = urlParams.get('decline_code');
            const errorMessage = urlParams.get('error_message');

            function relay(payload) {
                if (window.opener) {
                    try { window.opener.postMessage(payload, '*'); } catch (e) {}
                }
                if (window.parent !== window) {
                    try { window.parent.postMessage(payload, '*'); } catch (e) {}
                }
            }

            if (status === 'success' && referenceId) {
                localStorage.setItem('primary_payment_status', 'success');
                localStorage.setItem('primary_payment_reference', referenceId);
                relay({ type: 'payment_success', reference_id: referenceId });
            } else if (referenceId) {
                localStorage.setItem('primary_payment_status', status);
                localStorage.setItem('primary_payment_reference', referenceId);

                const heading = document.querySelector('h1');
                const subtitle = document.querySelector('p');
                const icon = document.querySelector('.success-icon');
                if (icon) icon.style.background = '#ef4444';
                if (heading) heading.textContent = status === 'cancelled' ? 'Payment Cancelled' : 'Payment Failed';
                if (subtitle) subtitle.textContent = errorMessage || 'Your payment could not be processed.';

                relay({
                    type: 'payment_failed',
                    reference_id: referenceId,
                    error_code: errorCode,
                    decline_code: declineCode,
                    error_message: errorMessage || status
                });
            }

            setTimeout(function() {
                window.close();
                setTimeout(function() {
                    document.querySelector('.closing-text').textContent = 'Please close this window and return to the previous page.';
                    document.querySelector('.spinner').style.display = 'none';
                }, 1000);
            }, 2000);
        })();
    </script>
</body>
</html>
