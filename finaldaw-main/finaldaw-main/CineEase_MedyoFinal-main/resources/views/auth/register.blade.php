<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/register.css">
    <title>Register</title>

    <style>
        /* Add your custom CSS styles here */
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form id="registerForm" method="POST" action="{{ route('register') }}">
        <h1>CineEase Application</h1>
        <h4>Register</h4>
            @csrf

            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Name">
            </div>

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Email">
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Password">
                <span id="passwordError" class="error-message"></span>
            </div>

            <div>
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">
                <span id="passwordConfirmationError" class="error-message"></span>
            </div>

            <input type="submit" value="Register">
            <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </form>

    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            var password = document.getElementById('password').value;
            var passwordConfirmation = document.getElementById('password_confirmation').value;
            var passwordError = document.getElementById('passwordError');
            var passwordConfirmationError = document.getElementById('passwordConfirmationError');

            // Clear previous error messages
            passwordError.textContent = '';
            passwordConfirmationError.textContent = '';

            // Password validation
            if (password.length < 8) {
                passwordError.textContent = 'Password must be at least 8 characters long.';
                event.preventDefault();
            } else if (!/[a-z]/.test(password)) {
                passwordError.textContent = 'Password must contain at least one lowercase letter.';
                event.preventDefault();
            } else if (!/[A-Z]/.test(password)) {
                passwordError.textContent = 'Password must contain at least one uppercase letter.';
                event.preventDefault();
            } else if (!/\d/.test(password)) {
                passwordError.textContent = 'Password must contain at least one digit.';
                event.preventDefault();
            } else if (!/[@$!%*?&#]/.test(password)) {
                passwordError.textContent = 'Password must contain at least one special character.';
                event.preventDefault();
            }

            // Password confirmation validation
            if (password !== passwordConfirmation) {
                passwordConfirmationError.textContent = 'Passwords do not match.';
                event.preventDefault();
            }
        });
    </script>
</body>
</html>