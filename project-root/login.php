<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login - Logistics Co.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-card {
            background-color: white;
            border: 1px solid #dee2e6;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        #reglink {
            text-decoration: none;
            color: #333;
        }

        #reglink:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            
            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                
                <div class="login-card">
                    <h2 class="text-center mb-4">Logistics Co.</h2>

                    <div id="alertBox" class="alert alert-danger d-none" role="alert"></div>

                    <form method="POST" action="" id="ajaxLoginForm">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control border" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control border" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-md" id="loginBtn">Login</button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <a id="reglink" href="register.php">Register Account</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#ajaxLoginForm').submit(function(e) {
            e.preventDefault(); 

            var formData = $(this).serialize();

            $('#loginBtn').prop('disabled', true).text('Authenticating...');

            $.ajax({
                type: 'POST',
                url: 'api/auth_login.php',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.href = 'index.php';
                    } else {
                        $('#alertBox').removeClass('d-none').text(response.message);
                        $('#loginBtn').prop('disabled', false).text('Login');
                    }
                },
                error: function() {
                    $('#alertBox').removeClass('d-none').text('System Error. Cannot connect to server.');
                    $('#loginBtn').prop('disabled', false).text('Login');
                }
            });
        });
    });
    </script>

</body>
</html>