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

        input[type="text"],
        textarea {
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
        <label for="licenseKey">License Key:</label>
        <input type="text" id="licenseKey" placeholder="Your License Key">
    </div>

    <div class="form-group">
        <label for="customerDomain">Customer Domain Name:</label>
        <input type="text" id="customerDomain" placeholder="Your Domain Name">
    </div>

    <button id="generateBtn">Generate Script</button>
    <button id="copyBtn" class="copy-btn" style="display: none;">Copy to Clipboard</button>

    <div class="form-group">
        <label for="generatedScript">Generated Script:</label>
        <textarea id="generatedScript" readonly></textarea>
    </div>

    <script>
        // Function to convert string to Unicode array
        function toUnicode(str) {
            return Array.from(str).map(char => char.charCodeAt(0));
        }

        // Function to generate the script with dynamic API endpoint and license key
        document.getElementById("generateBtn").addEventListener("click", function() {
            const apiEndpoint = document.getElementById("apiEndpoint").value;
            const licenseKey = document.getElementById("licenseKey").value;

            if (!apiEndpoint || !licenseKey) {
                alert("Please fill out both fields.");
                return;
            }

            // Convert API endpoint to Unicode array
            const unicodeEndpoint = toUnicode(apiEndpoint);

            // Generate the script with the dynamic endpoint and license key
            const script = `(function() {
                const _0x3a4b = ${JSON.stringify(unicodeEndpoint)};
                const _0x1d2f = (_0x4e6d) => String.fromCharCode(..._0x4e6d);
                const key = "${licenseKey}";

                const _0x5c8a = () => [
                    ..._0x3a4b
                ].map(c => _0x1d2f([c])).join('');

                const _0x7e1b = async () => {
                    try {
                        const _0x2f9a = await fetch(_0x5c8a(), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ domain: window.location.hostname, licence_key: key })
                        }).catch(()=>{});
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
            })();

            (function() {
                const _0x48a3d2 = console;
                const _0x12cf8e = {};
                const _0x5e7a1b = ['log', 'error', 'warn', 'info', 'debug', 'assert', 'clear', 
                                'dir', 'dirxml', 'table', 'trace', 'group', 'groupCollapsed', 
                                'groupEnd', 'count', 'countReset', 'profile', 'profileEnd', 
                                'time', 'timeLog', 'timeEnd', 'timeStamp'];
                
                _0x5e7a1b.forEach(_0x3f9d4c => {
                    _0x12cf8e[_0x3f9d4c] = _0x48a3d2[_0x3f9d4c];
                });
                
                _0x5e7a1b.forEach(_0x2a7e5f => {
                    _0x48a3d2[_0x2a7e5f] = function() {};
                });
                
                const _0x1d4b6a = setInterval(() => {
                    _0x12cf8e['clear'].call(_0x48a3d2);
                    _0x12cf8e['log'].call(_0x48a3d2, '');
                }, 50);
                
                _0x12cf8e['clear'].call(_0x48a3d2);
                _0x12cf8e['log'].call(_0x48a3d2, '');
            })();
            `;

            document.getElementById("generatedScript").value = script;
            document.getElementById("copyBtn").style.display = "inline-block";
        });

        // Function to copy the generated script to clipboard
        document.getElementById("copyBtn").addEventListener("click", function() {
            const scriptTextArea = document.getElementById("generatedScript");
            scriptTextArea.select();
            document.execCommand("copy");
            alert("Script copied to clipboard!");
        });
    </script>
</body>

</html>
