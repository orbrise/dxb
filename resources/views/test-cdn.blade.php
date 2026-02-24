<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDN Asset Test</title>
    
    {{-- Test overridden asset() function --}}
    <link rel="stylesheet" href="{{ asset('css/app3.css') }}">
    
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px; 
            background: #f5f5f5;
        }
        .test-container { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
        }
        .test-section { 
            margin: 20px 0; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
        }
        .url-display { 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 3px; 
            font-family: monospace; 
            word-break: break-all;
        }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        h2 { color: #333; margin-top: 0; }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-online { background-color: #28a745; }
        .status-offline { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🌐 CDN Asset System Test</h1>
        
        <div class="test-section">
            <h2>📊 Environment Configuration</h2>
            <p><strong>Environment:</strong> {{ config('app.env') }}</p>
            <p><strong>Current Asset Disk:</strong> <span class="url-display">{{ \App\Services\AssetService::disk() }}</span></p>
            <p><strong>Auto-detect Enabled:</strong> 
                <span class="{{ config('assets.auto_detect') ? 'success' : 'warning' }}">
                    {{ config('assets.auto_detect') ? '✅ Yes' : '❌ No' }}
                </span>
            </p>
            <p><strong>Versioning Enabled:</strong> 
                <span class="{{ config('assets.versioning.enabled') ? 'success' : 'warning' }}">
                    {{ config('assets.versioning.enabled') ? '✅ Yes' : '❌ No' }}
                </span>
            </p>
            <p><strong>Asset Version:</strong> <span class="info">{{ config('assets.versioning.version', 'Not set') }}</span></p>
            <p><strong>Fallback Enabled:</strong> 
                <span class="{{ config('assets.fallback.enabled') ? 'success' : 'warning' }}">
                    {{ config('assets.fallback.enabled') ? '✅ Yes' : '❌ No' }}
                </span>
            </p>
        </div>

        <div class="test-section">
            <h2>🔧 Helper Function Tests</h2>
            
            <h3>CSS Assets</h3>
            <p><strong>app3.css (Using standard asset()):</strong></p>
            <div class="url-display">{{ asset('css/app3.css') }}</div>
            
            <h3>JavaScript Assets</h3>
            <p><strong>app.js (Using standard asset()):</strong></p>
            <div class="url-display">{{ asset('js/app.js') }}</div>
            
            <h3>Image Assets</h3>
            <p><strong>Logo (Using standard asset()):</strong></p>
            <div class="url-display">{{ asset('images/logo.png') }}</div>
            
            <h3>Versioned Assets</h3>
            <p><strong>Versioned CSS (Using asset_url helper):</strong></p>
            <div class="url-display">{{ versioned_asset('css/app3.css') }}</div>
        </div>

        <div class="test-section">
            <h2>🚀 Asset Disk Status</h2>
            <div id="server-status">
                <p><strong>Active Disk:</strong> <span class="url-display">{{ asset_disk() }}</span></p>
                <p><strong>Disk Mapping:</strong></p>
                <ul>
                    @foreach(config('assets.disk_mapping', []) as $env => $disk)
                        <li><strong>{{ $env }}:</strong> {{ $disk }} 
                            @if($env === config('app.env'))
                                <span class="success">← Current</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <h3>Test Asset URLs</h3>
            <div id="asset-test">
                <p><strong>CSS URL:</strong></p>
                <div class="url-display">{{ asset_url('css/app3.css') }}</div>
                <p><strong>JS URL:</strong></p> 
                <div class="url-display">{{ asset_url('js/app.js') }}</div>
            </div>
        </div>

        <div class="test-section">
            <h2>🖼️ Image Fallback System</h2>
            <h3>Storage Image (with fallback)</h3>
            <img src="{{ proxy_image('https://ae.massagerepublic.com.co/storage/userimages/8e2cc8c7c5/thumb_91_Th5c1VJQy2.png') }}" 
                 alt="Test storage image" style="max-width: 200px; border: 1px solid #ddd;" 
                 onerror="this.style.border='2px solid red'; this.alt='❌ Failed to load';">
            
            <h3>Public Asset Image (direct)</h3>
            <img src="{{ asset_url('images/web/logo.png') }}" 
                 alt="Logo" style="max-width: 200px; border: 1px solid #ddd;"
                 onerror="this.style.border='2px solid red'; this.alt='❌ Logo not found';">
        </div>

        <div class="test-section">
            <h2>⚙️ Runtime Information</h2>
            <p><strong>Current URL:</strong> <span class="url-display">{{ request()->url() }}</span></p>
            <p><strong>User Agent:</strong> <span class="url-display">{{ request()->userAgent() }}</span></p>
            <p><strong>Server Time:</strong> {{ now()->format('Y-m-d H:i:s T') }}</p>
        </div>
    </div>

    {{-- Test overridden asset() function for JS --}}
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <script>
        // Test asset server connectivity
        async function checkAssetServer() {
            const statusDiv = document.getElementById('server-status');
            const testDiv = document.getElementById('asset-test');
            
            try {
                const response = await fetch('{{ rtrim(config('app.asset_url', 'http://localhost:8080'), '/') }}/health');
                
                if (response.ok) {
                    const data = await response.text();
                    statusDiv.innerHTML = `
                        <p><span class="status-indicator status-online"></span>
                        <span class="success">✅ Asset server is online!</span></p>
                        <div class="url-display">${data}</div>
                    `;
                } else {
                    throw new Error('Server responded with error');
                }
            } catch (error) {
                statusDiv.innerHTML = `
                    <p><span class="status-indicator status-offline"></span>
                    <span class="warning">⚠️ Asset server is offline or unreachable</span></p>
                    <p><small>Error: ${error.message}</small></p>
                `;
            }

            // Test actual asset loading
            const testUrl = '{{ local_or_cdn_asset('assets/css/app3.css') }}';
            try {
                const assetResponse = await fetch(testUrl, { method: 'HEAD' });
                testDiv.innerHTML += assetResponse.ok ? 
                    '<p class="success">✅ CSS asset loads successfully</p>' :
                    '<p class="warning">⚠️ CSS asset returned: ' + assetResponse.status + '</p>';
            } catch (error) {
                testDiv.innerHTML += '<p class="warning">⚠️ CSS asset failed to load: ' + error.message + '</p>';
            }
        }

        // Run tests when page loads
        document.addEventListener('DOMContentLoaded', checkAssetServer);
    </script>
</body>
</html>
