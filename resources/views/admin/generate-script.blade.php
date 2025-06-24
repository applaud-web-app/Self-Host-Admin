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
        <label for="apiEndpoint">Verify API Endpoint:</label>
        <input type="text" id="apiEndpoint" placeholder="https://yourdomain.com/verify">
    </div>
    
    <div class="form-group">
        <label for="staticCheckEndpoint">Customer SelfHost Endpoint:</label>
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
                // Function to generate the dynamic array based on the domain
                function generateDomainArray(domain) {
                    const domainParts = domain.split('.'); // Split the domain into parts
                    const domainArray = domainParts.map(part => {
                        return Array.from(part).map(char => char.charCodeAt(0)); // Convert each character to its charCode
                    });
                    return domainArray;
                }

                // Generate the script content
                const domainArray = generateDomainArray(domain);
                const script = `
                    (function() {
                        const _0x3a4b = ${JSON.stringify(domainArray)};

                        const _0x1d2f = (_0x4e6d) => String.fromCharCode(..._0x4e6d);

                        const _0x5c8a = () => [
                            _0x1d2f(_0x3a4b[0]), _0x1d2f(_0x3a4b[8]),
                            _0x1d2f(_0x3a4b[1]), _0x1d2f(_0x3a4b[2]), _0x1d2f(_0x3a4b[9]),
                            _0x1d2f(_0x3a4b[3]), _0x1d2f(_0x3a4b[4]), _0x1d2f(_0x3a4b[9]),
                            _0x1d2f(_0x3a4b[5]), _0x1d2f(_0x3a4b[10]),
                            _0x1d2f(_0x3a4b[6]), _0x1d2f(_0x3a4b[10]),
                            _0x1d2f(_0x3a4b[7])
                        ].join('');

                        const _0x7e1b = async () => {
                            try {
                                const _0x2f9a = await fetch('${checkUrl}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({ domain: window.location.hostname, licenseKey: '${key}' })
                                }).catch(() => {});
                                const _0x5d7c = await (_0x2f9a?.json?.() || Promise.resolve(null));
                                return _0x5d7c && (_0x5d7c.status === 0 || _0x5d7c.status === 1) ? _0x5d7c : null;
                            } catch {
                                return null;
                            }
                        };

                        const _0x4a6d = (_0x6f2c) => {
                            if (!_0x6f2c) return;
                            localStorage.setItem('dv', JSON.stringify({
                                h: window.location.hostname,
                                s: _0x6f2c.status,
                                t: Date.now(),
                                m: _0x6f2c.message || ''
                            }));
                        };

                        const _0x9b3c = () => {
                            try {
                                const _0x1a4e = localStorage.getItem('dv');
                                if (!_0x1a4e) return false;
                                const _0x5b2d = JSON.parse(_0x1a4e);
                                return _0x5b2d.h === window.location.hostname && (Date.now() - _0x5b2d.t) < 21600000;
                            } catch {
                                return false;
                            }
                        };

                        const _0x8d2e = async () => {
                            if (_0x9b3c()) return;
                            const _0x3f6a = await _0x7e1b();
                            _0x3f6a && _0x4a6d(_0x3f6a);
                        };

                        (function() {
                            try {
                                _0x8d2e();
                                setInterval(_0x8d2e, 21600000);
                            } catch {}
                        })();
                })();`;

                return script;
            }
        });
    </script>
</body>
</html>
