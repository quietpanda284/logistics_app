<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div class="container min-vh-100 d-flex justify-content-center align-items-center py-4">
        <div class="col-12 col-md-6 col-lg-4">

            <div class="card border-secondary shadow-lg">
                <div class="card-body p-4">
                    <h3 class="text-center mb-3">Staff Register</h3>

                    <div id="alertBox" class="alert alert-danger d-none text-center"></div>

                    <div id="successBox" class="alert alert-success d-none text-center">
                        Account created! <br>
                        <a href="login.php" class="alert-link">Go to Login</a>
                    </div>

                    <form id="ajaxRegisterForm">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="passInput" class="form-control border-secondary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confPassInput" class="form-control border-secondary" required>
                        </div>

                        <hr class="border-secondary my-4">

                        <div class="mb-4">
                            <label class="form-label text-warning fw-bold">Company Access Code</label>
                            <div class="input-group">
                                <span class="input-group-text bg-warning border-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
                                    </svg>
                                </span>
                                <input type="password" name="secret_code" class="form-control border-warning" placeholder="Required" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="regBtn" class="btn btn-dark">Create Account</button>
                            <a href="login.php" class="btn btn-outline-dark">Back to Login</a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#ajaxRegisterForm').submit(function(e) {
            e.preventDefault();

            var pass = $('#passInput').val();
            var conf = $('#confPassInput').val();

            if (pass !== conf) {
                $('#alertBox').removeClass('d-none').text('Passwords do not match.');
                return;
            }
            
            var btn = $('#regBtn');
            btn.prop('disabled', true).text('Creating...');

            $.ajax({
                type: 'POST',
                url: 'api/auth_register.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#ajaxRegisterForm').slideUp();
                        $('#successBox').removeClass('d-none');
                    } else {
                        $('#alertBox').removeClass('d-none').text(response.message);
                        btn.prop('disabled', false).text('Create Account');
                    }
                },
                error: function() {
                    $('#alertBox').removeClass('d-none').text('System Error: Cannot connect to server.');
                    btn.prop('disabled', false).text('Create Account');
                }
            });
        });
    });
    </script>

</body>
</html>