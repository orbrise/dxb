<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #1e1e1e 0%, #2d2d2d 100%);
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

        .error-code {
            font-size: 150px;
            font-weight: 900;
            background: linear-gradient(45deg, #f4b827, #d3980b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
            animation: fadeInDown 0.6s ease-out;
        }

        .error-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #ffffff;
            animation: fadeInUp 0.8s ease-out;
        }

        .error-message {
            font-size: 18px;
            color: #aaaaaa;
            margin-bottom: 40px;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1.2s ease-out;
        }

        .btn {
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f4b827, #d3980b);
            color: #333;
            box-shadow: 0 4px 15px rgba(244, 184, 39, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 184, 39, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #aaaaaa;
            border: 2px solid #4e4e4e;
        }

        .btn-secondary:hover {
            border-color: #f4b827;
            color: #f4b827;
            transform: translateY(-2px);
        }

        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .error-icon img {
            opacity: 1;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 100px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-message {
                font-size: 16px;
            }

            .btn {
                padding: 12px 24px;
                font-size: 14px;
            }

            .error-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <img src="https://assets.massagerepublic.com.co/assets/images/web/mlogo.png" alt="Logo" style="max-width: 150px; height: auto;">
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

    <script>
        // Add Font Awesome for icons
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
        document.head.appendChild(fontAwesome);
    </script>
</body>
</html>
