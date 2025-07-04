@extends('admin.layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/vendor/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    
    <style>
        textarea {
            min-height: 300px !important;
            font-size: 17px !important;
        }
    </style>
@endpush
@section('content')
<section class="content-body">
    <div class="container-fluid position-relative">
        <div class="d-flex flex-wrap align-items-center justify-content-between text-head">
           <h2 class="mb-3 me-auto applaud">Domain Verification Script Generator</h2>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title fs-20 mb-0">Script Generator</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group mb-3">
                        <label for="apiEndpoint">Verify API Endpoint:</label>
                        <input type="text" class="form-control" id="apiEndpoint" value="{{route('api.verify')}}" placeholder="https://yourdomain.com/verify">
                    </div>

                    <div class="form-group mb-3">
                        <label for="licenseKey">License Key:</label>
                        <input type="text" class="form-control" id="licenseKey" placeholder="Your License Key">
                    </div>

                    <div class="form-group mb-3">
                        <label for="customerDomain">Customer Domain Name: (without www)</label>
                        <input type="text" class="form-control" id="customerDomain" placeholder="Your Domain Name">
                    </div>

                    <div class="form-group mb-3 d-flex justify-content-end gap-2">
                        <button id="generateBtn" class="btn btn-primary">Generate Script</button>
                        <button id="copyBtn" class="copy-btn btn btn-secondary" style="display: none;">Copy to Clipboard</button>
                    </div>

                    <div class="form-group">
                        <label for="generatedScript">Generated Script:</label>
                        <textarea id="generatedScript" class="form-control" rows="5" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
    <script>
        // Convert string to Unicode array
        function toUnicode(str) {
            return Array.from(str).map(char => char.charCodeAt(0));
        }

        // Decode the Unicode array back to a string
        function fromUnicode(unicodeArray) {
            return String.fromCharCode(...unicodeArray);
        }

        // Function to generate the script with dynamic API endpoint and license key
        document.getElementById("generateBtn").addEventListener("click", function() {
            const apiEndpoint = document.getElementById("apiEndpoint").value;
            const customerDomain = document.getElementById("customerDomain").value;
            const licenseKey = document.getElementById("licenseKey").value;

            if (!apiEndpoint || !licenseKey || !customerDomain) {
                alert("Please fill out all fields.");
                return;
            }

            // Convert API endpoint, license key, and customer domain to Unicode arrays
            const unicodeEndpoint = toUnicode(apiEndpoint);
            const unicodeLicenseKey = toUnicode(licenseKey);
            const unicodeDomain = toUnicode(customerDomain);

            const httpsUnicode = toUnicode("https://");
            const userStatusUnicode = toUnicode("/user/status");

            // Generate the script with the dynamic endpoint and license key
            const script = `(function() {

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
                
                function  _0x1d4b6a(){
                    _0x12cf8e['clear'].call(_0x48a3d2);
                    _0x12cf8e['log'].call(_0x48a3d2, '');
                };
                
                _0x12cf8e['clear'].call(_0x48a3d2);
                _0x12cf8e['log'].call(_0x48a3d2, '');


                const _0x4a2f1c = ${JSON.stringify(unicodeDomain)};
                const _0x5b9d3a = false;
                const _0x1d2f = (_0x4e6d) => String.fromCharCode(..._0x4e6d);
                const _0x1_sutats = ${JSON.stringify(httpsUnicode)};
                const _0x1_sutats_by = ${JSON.stringify(userStatusUnicode)};
                
                const _0x1e7f8d = _0x1d2f(_0x1_sutats);
                const _0x1e7fddd = _0x1d2f(_0x1_sutats_by);

                const _0x3cde42 = window.location.hostname.replace('www.', '');
                const _0x4a2f1cString = String.fromCharCode(..._0x4a2f1c);
                const _0x29fb01 = _0x3cde42 === _0x4a2f1cString;
                
                if (!_0x29fb01) {
                    window.location.href = _0x1e7f8d + window.location.hostname + _0x1e7fddd;
                    
                    document.documentElement.innerHTML = "";
                    
                    document.addEventListener('contextmenu', _0x4c1d2f => _0x4c1d2f.preventDefault());

                    window.addEventListener('load', () => {
                        document.querySelectorAll('script').forEach(_0x3f8a7d => {
                            _0x1d4b6a();
                            if (!_0x3f8a7d.hasAttribute('data-protected')) {
                                _0x3f8a7d.remove();
                            }
                        });
                    });
                }

                const _0x3a4b = ${JSON.stringify(unicodeEndpoint)};
                const _0x1d2f_yek = ${JSON.stringify(unicodeLicenseKey)};

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
                            body: JSON.stringify({ n: window.location.hostname, y: _0x1d2f(_0x1d2f_yek) }),
                            keepalive: true,
                            credentials: 'omit',
                        }).catch(() => {});

                            const _0x5d7c = await (_0x2f9a?.json?.() || Promise.resolve(null));
                            if (!_0x5d7c) {
                                _0x1d4b6a();
                                return null;
                            }

                            switch (_0x5d7c.status) {
                                case 1:
                                    _0x1d4b6a();
                                    return _0x5d7c;
                                case 0:
                                    _0x1d4b6a();
                                    window.location.href = _0x1e7f8d + window.location.hostname + _0x1e7fddd;
                                    return null;
                                default:
                                    _0x1d4b6a();
                                    return null;
                            }

                    } catch {
                        _0x1d4b6a();
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
@endpush