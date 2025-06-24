<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Verification Script Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            min-height: 150px;
            font-family: monospace;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        #generateBtn {
            margin-top: 20px;
        }
        .copy-btn {
            background: #9b59b6;
            margin-left: 10px;
        }
        .copy-btn:hover {
            background: #8e44ad;
        }
    </style>
</head>
<body>
    <h1>Domain Verification Script Generator</h1>

    <div class="form-group">
        <label for="apiEndpoint">API Endpoint:</label>
        <input type="text" id="apiEndpoint" placeholder="https://yourdomain.com/verify">
    </div>
    
    <div class="form-group">
        <label for="staticCheckEndpoint">Static Check Endpoint:</label>
        <input type="text" id="staticCheckEndpoint" placeholder="https://yourdomain.com/status">
    </div>

    <div class="form-group">
        <label for="domain">Allowed Domain (example.com):</label>
        <input type="text" id="domain" placeholder="example.com">
    </div>
    
    <div class="form-group">
        <label for="licenseKey">License Key:</label>
        <input type="text" id="licenseKey" placeholder="Your License Key">
    </div>
    
    <button id="generateBtn">Generate Script</button>
    <button id="copyBtn" class="copy-btn">Copy to Clipboard</button>
    
    <div class="form-group">
        <label for="generatedScript">Generated Script:</label>
        <textarea id="generatedScript" readonly></textarea>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiEndpoint = document.getElementById('apiEndpoint');
            const staticCheckEndpoint = document.getElementById('staticCheckEndpoint');
            const domain = document.getElementById('domain');
            const licenseKey = document.getElementById('licenseKey');
            const generateBtn = document.getElementById('generateBtn');
            const copyBtn = document.getElementById('copyBtn');
            const generatedScript = document.getElementById('generatedScript');

            // Generate script
            generateBtn.addEventListener('click', function() {
                const apiUrl = apiEndpoint.value.trim();
                const checkUrl = staticCheckEndpoint.value.trim();
                const domainValue = domain.value.trim();
                const key = licenseKey.value.trim();

                if (!apiUrl || !checkUrl || !domainValue || !key) {
                    alert('Please fill out all fields');
                    return;
                }

                const script = generateScript(apiUrl, checkUrl, domainValue, key);
                generatedScript.value = script;
            });

            // Copy to clipboard
            copyBtn.addEventListener('click', function() {
                if (!generatedScript.value) {
                    alert('Nothing to copy. Generate the script first.');
                    return;
                }

                generatedScript.select();
                document.execCommand('copy');

                // Visual feedback
                const originalText = copyBtn.textContent;
                copyBtn.textContent = 'Copied!';
                setTimeout(() => {
                    copyBtn.textContent = originalText;
                }, 2000);
            });

            // Script generation function
            function generateScript(apiUrl, checkUrl, domain, key) {
                const scriptParts = [];

                scriptParts.push(`
(function() {
    const API_URL = "${apiUrl}";
    const CHECK_URL = "${checkUrl}";
    const ALLOWED_DOMAIN = "${domain}";
    const LICENSE_KEY = "${key}";
    
    // Verify domain via API
    async function verifyDomain() {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ domain: window.location.hostname, licenseKey: LICENSE_KEY })
            });
            const result = await response.json();
            if (result.status !== 1) {
                // Unauthorized domain
                window.location.href = CHECK_URL;
            }
        } catch (error) {
            console.error('Domain verification failed:', error);
        }
    }

    // Perform static domain check
    async function staticCheck() {
        try {
            const response = await fetch(CHECK_URL, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if (result.status !== 1) {
                // Unauthorized access
                window.location.href = CHECK_URL;
            }
        } catch (error) {
            console.error('Static check failed:', error);
        }
    }

    // Run the verification
    if (window.location.hostname !== ALLOWED_DOMAIN) {
        verifyDomain();
    } else {
        staticCheck();
    }
})();
                `);

                return scriptParts.join('\n\n');
            }
        });
    </script>
</body>
</html>