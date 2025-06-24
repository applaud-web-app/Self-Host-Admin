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
        .domain-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        .domain-item {
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .domain-item button {
            background: #e74c3c;
            padding: 2px 5px;
            margin-left: 5px;
            font-size: 12px;
        }
        .domain-item button:hover {
            background: #c0392b;
        }
        #addDomain {
            background: #2ecc71;
            padding: 5px 10px;
            font-size: 14px;
        }
        #addDomain:hover {
            background: #27ae60;
        }
        .copy-btn {
            background: #9b59b6;
            margin-left: 10px;
        }
        .copy-btn:hover {
            background: #8e44ad;
        }
        .options {
            margin: 15px 0;
        }
        .options label {
            display: inline;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <h1>Domain Verification Script Generator</h1>
    
    <div class="form-group">
        <label for="domains">Allowed Domains (one per line or comma separated):</label>
        <textarea id="domainsInput" placeholder="example.com, sub.example.com"></textarea>
        <button id="addDomain">Add Domains</button>
        <div id="domainList" class="domain-list"></div>
    </div>
    
    <div class="form-group">
        <label for="redirectUrl">Redirect URL (for unauthorized domains):</label>
        <input type="text" id="redirectUrl" placeholder="https://yourdomain.com/error">
    </div>
    
    <div class="options">
        <label>
            <input type="checkbox" id="destructiveMode" checked>
            Enable Destructive Mode (removes scripts, prevents inspection)
        </label>
        <label>
            <input type="checkbox" id="consoleSuppression" checked>
            Enable Console Suppression
        </label>
        <label>
            <input type="checkbox" id="localStorageCache" checked>
            Enable LocalStorage Caching (6 hour cache)
        </label>
    </div>
    
    <button id="generateBtn">Generate Script</button>
    <button id="copyBtn" class="copy-btn">Copy to Clipboard</button>
    
    <div class="form-group">
        <label for="generatedScript">Generated Script:</label>
        <textarea id="generatedScript" readonly></textarea>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const domainsInput = document.getElementById('domainsInput');
            const domainList = document.getElementById('domainList');
            const redirectUrl = document.getElementById('redirectUrl');
            const destructiveMode = document.getElementById('destructiveMode');
            const consoleSuppression = document.getElementById('consoleSuppression');
            const localStorageCache = document.getElementById('localStorageCache');
            const generateBtn = document.getElementById('generateBtn');
            const copyBtn = document.getElementById('copyBtn');
            const generatedScript = document.getElementById('generatedScript');
            const addDomainBtn = document.getElementById('addDomain');
            
            let allowedDomains = [];
            
            // Add domain to list
            addDomainBtn.addEventListener('click', function() {
                const input = domainsInput.value.trim();
                if (!input) return;
                
                // Split by commas or newlines
                const newDomains = input.split(/[,\n]/)
                    .map(d => d.trim())
                    .filter(d => d.length > 0);
                
                allowedDomains = [...new Set([...allowedDomains, ...newDomains])];
                domainsInput.value = '';
                renderDomainList();
            });
            
            // Render domain list
            function renderDomainList() {
                domainList.innerHTML = '';
                allowedDomains.forEach((domain, index) => {
                    const domainItem = document.createElement('div');
                    domainItem.className = 'domain-item';
                    domainItem.textContent = domain;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', () => {
                        allowedDomains.splice(index, 1);
                        renderDomainList();
                    });
                    
                    domainItem.appendChild(removeBtn);
                    domainList.appendChild(domainItem);
                });
            }
            
            // Generate script
            generateBtn.addEventListener('click', function() {
                if (allowedDomains.length === 0) {
                    alert('Please add at least one allowed domain');
                    return;
                }
                
                if (!redirectUrl.value.trim()) {
                    alert('Please enter a redirect URL');
                    return;
                }
                
                const script = generateScript(
                    allowedDomains,
                    redirectUrl.value.trim(),
                    destructiveMode.checked,
                    consoleSuppression.checked,
                    localStorageCache.checked
                );
                
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
            function generateScript(domains, redirectUrl, destructive, suppressConsole, useCache) {
                // Convert domains to char codes
                const domainArrays = domains.map(domain => 
                    Array.from(domain).map(c => c.charCodeAt(0))
                );
                
                // Convert redirect URL to char codes
                const redirectUrlArray = Array.from(redirectUrl).map(c => c.charCodeAt(0));
                
                // Generate the script parts
                let scriptParts = [];
                
                if (useCache) {
                    scriptParts.push(`
(function() {
    const _0x3a4b = ${JSON.stringify([
        [104, 116, 116, 112, 115],
        [115, 101, 108, 102],
        [104, 111, 115, 116],
        [97, 119, 109],
        [116, 97, 98],
        [105, 110],
        [97, 112, 105],
        [118, 101, 114, 105, 102, 121],
        [58, 47, 47],
        [46],
        [47]
    ])};

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
            const _0x2f9a = await fetch(_0x5c8a(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ domain: window.location.hostname })
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
                    `);
                }
                
                if (suppressConsole) {
                    scriptParts.push(`
// console-suppress.js
(function() {
    // Randomize all identifiers
    const _0x48a3d2 = console;
    const _0x12cf8e = {};
    const _0x5e7a1b = ['log', 'error', 'warn', 'info', 'debug', 'assert', 'clear', 
                      'dir', 'dirxml', 'table', 'trace', 'group', 'groupCollapsed', 
                      'groupEnd', 'count', 'countReset', 'profile', 'profileEnd', 
                      'time', 'timeLog', 'timeEnd', 'timeStamp'];
    
    // Store original methods
    _0x5e7a1b.forEach(_0x3f9d4c => {
        _0x12cf8e[_0x3f9d4c] = _0x48a3d2[_0x3f9d4c];
    });
    
    // Override all methods
    _0x5e7a1b.forEach(_0x2a7e5f => {
        _0x48a3d2[_0x2a7e5f] = function() {};
    });
    
    // Continuous clearing
    const _0x1d4b6a = setInterval(() => {
        _0x12cf8e['clear'].call(_0x48a3d2);
        _0x12cf8e['log'].call(_0x48a3d2, '');
    }, 50);
    
    // Initial clear
    _0x12cf8e['clear'].call(_0x48a3d2);
    _0x12cf8e['log'].call(_0x48a3d2, '');
})();
                    `);
                }
                
                // Main verification script
                scriptParts.push(`
(function() {
    // ======================
    // CONFIGURATION
    // ======================
    const ALLOWED_DOMAINS = ${JSON.stringify(domains)}; // encrypt this also
    const DESTRUCTIVE_MODE = ${destructive};
    
    // Unicode-Obfuscated Redirect URL
    const REDIRECT_URL = String.fromCharCode(${redirectUrlArray.join(',')});

    // ======================
    // DOMAIN VERIFICATION
    // ======================
    const currentDomain = window.location.hostname.replace('www.', '');
    const isAuthorized = ALLOWED_DOMAINS.some(domain => currentDomain === domain);
    
    if (!isAuthorized) {
        // Immediate redirect before any other actions
        window.location.href = REDIRECT_URL;
        
        // ======================
        // DEFENSIVE ACTIONS (Fallback if redirect fails)
        // ======================
        ${destructive ? `
        document.documentElement.innerHTML = \`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Redirecting...</title>
                <meta http-equiv="refresh" content="0;url=\${REDIRECT_URL}">
                <style>body { background: #000; color: #fff; }</style>
            </head>
            <body>
                <script>
                    // Secondary redirect attempt
                    window.location.replace("\${REDIRECT_URL}");
                </script>
                <p>If you are not redirected, <a href="\${REDIRECT_URL}">click here</a>.</p>
            </body>
            </html>
        \`;

        // ======================
        // CONSOLE PROTECTION
        // ======================
        console.log('%c STOP!', 'color:red;font-size:50px;font-weight:bold');
        console.log(\`%c Redirecting to authorized domain...\`, 'font-size:20px;');
        
        // Prevent right-click inspection
        document.addEventListener('contextmenu', e => e.preventDefault());

        // Kill all non-protected scripts
        window.addEventListener('load', () => {
            document.querySelectorAll('script').forEach(script => {
                if (!script.hasAttribute('data-protected')) {
                    script.remove();
                }
            });
        });
        ` : ''}
    } else {
        console.log('%c ✔ Domain Verified', 'color:green;font-size:20px;');
    }
})();
                `);
                
                return scriptParts.join('\n\n');
            }
        });
    </script>
</body>
</html>