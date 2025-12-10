<!DOCTYPE html>
<html>
<head>
    <title>Subdomain Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>🧪 Subdomain Multi-Tenant Test</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Current Request Info:</h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Subdomain:</strong></td>
                                <td>{{ $subdomain ?? 'None' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Host:</strong></td>
                                <td>{{ $host }}</td>
                            </tr>
                            <tr>
                                <td><strong>Full URL:</strong></td>
                                <td><small>{{ $url }}</small></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Available Stores:</h5>
                        <div class="list-group">
                            @foreach($stores as $store)
                                <a href="http://{{ $store->code }}.samsae.test" class="list-group-item list-group-item-action">
                                    <strong>{{ $store->name }}</strong> ({{ $store->code }})
                                    <br>
                                    <small class="text-muted">{{ $store->code }}.samsae.test</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Test Links:</h5>
                    <div class="btn-group">
                        <a href="http://abc.samsae.test" class="btn btn-primary">abc.samsae.test</a>
                        <a href="http://xyz.samsae.test" class="btn btn-success">xyz.samsae.test</a>
                        <a href="http://admin.samsae.test" class="btn btn-warning">admin.samsae.test</a>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>API Test:</h5>
                    <button class="btn btn-info" onclick="testAPI()">Test Current Subdomain API</button>
                    <div id="api-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testAPI() {
            const subdomain = window.location.hostname.split('.')[0];
            fetch(`/api/test/${subdomain}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('api-result').innerHTML = `
                        <div class="alert alert-info">
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                })
                .catch(error => {
                    document.getElementById('api-result').innerHTML = `
                        <div class="alert alert-danger">
                            Error: ${error.message}
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>
