<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0D1011;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .error-brand {
            margin-bottom: 28px;
            animation: fadeInDown 0.5s ease-out;
        }

        .error-brand-logo {
            color: #C1F11D;
            font-size: 32px;
            font-weight: 700;
            font-style: italic;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .error-brand-logo img {
            max-height: 44px;
            width: auto;
        }

        .error-code {
            font-size: 150px;
            font-weight: 900;
            color: #C1F11D;
            line-height: 1;
            margin-bottom: 16px;
            letter-spacing: -4px;
            animation: fadeInDown 0.6s ease-out;
        }

        .error-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 14px;
            color: #ffffff;
            animation: fadeInUp 0.8s ease-out;
        }

        .error-message {
            font-size: 16px;
            color: #8f97a6;
            margin-bottom: 36px;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out;
        }

        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1.2s ease-out;
        }

        .btn {
            padding: 12px 28px;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #C1F11D;
            color: #111;
        }

        .btn-primary:hover {
            background: #d4f84d;
        }

        .btn-secondary {
            background: transparent;
            color: #d8deea;
            border: 1px solid #31384a;
        }

        .btn-secondary:hover {
            border-color: #C1F11D;
            color: #C1F11D;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .error-code { font-size: 100px; letter-spacing: -2px; }
            .error-title { font-size: 24px; }
            .error-message { font-size: 15px; }
            .btn { padding: 12px 24px; font-size: 14px; }
            .error-actions { flex-direction: column; }
            .error-brand-logo { font-size: 26px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-brand">
            <a href="/" class="error-brand-logo">
                @if(isset($setting) && !empty($setting->app_logo))
                    <img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}">
                @else
                    {{ $setting->app_name ?? 'evoory' }}
                @endif
            </a>
        </div>
        <div class="error-code">404</div>
        <h1 class="error-title">Oops! Page Not Found</h1>
        <p class="error-message">
            The page you're looking for doesn't exist or has been moved.
            Don't worry, let's get you back on track.
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fa fa-home"></i> Go to Homepage
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Go Back
            </a>
        </div>
    </div>
</body>
</html>
