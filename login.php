<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloomNest - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #fdf2f8, #f0fdf4);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: #ffffff;
            width: 100%;
            max-width: 380px;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .logo {
            font-size: 28px;
            margin-bottom: 5px;
        }

        h1 {
            color: #3f6212;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #888;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        form {
            text-align: left;
        }

        label {
            display: block;
            font-size: 14px;
            color: #444;
            margin-bottom: 5px;
            margin-top: 15px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #ec4899;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-top: 12px;
            color: #555;
        }

        .options a {
            color: #db2777;
            text-decoration: none;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 22px;
            background: #db2777;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: #be185d;
        }

        .signup-text {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .signup-text a {
            color: #db2777;
            text-decoration: none;
            font-weight: bold;
        }

        .back-home {
            margin-top: 15px;
        }

        .back-home button {
            background: #f3f4f6;
            color: #444;
        }

        .back-home button:hover {
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🌸</div>
        <h1>BloomNest</h1>
        <p class="subtitle">Welcome back! Please log in to your account.</p>

        <?php
            // Display an error message if login failed (set by PHP handler)
            if (isset($_GET['error'])) {
                echo '<div class="error-message">Invalid email or password. Please try again.</div>';
            }
        ?>

        <form action="login_process.php" method="POST">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <div class="options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="forgot_password.html">Forgot password?</a>
            </div>

            <button type="submit">Log In</button>
        </form>

        <p class="signup-text">Don't have an account? <a href="signup.html">Sign up</a></p>

        <div class="back-home">
            <button onclick="window.location.href='../index.html'">Back to Home</button>
        </div>
    </div>
</body>
</html>
