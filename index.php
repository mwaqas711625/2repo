<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #080f25, #101d3b, #07152e);
    padding: 20px;
}

.login-card {
    width: 100%;
    max-width: 400px;
    padding: 40px 35px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 18px;
    box-shadow: 0 15px 45px rgba(0,0,0,0.4);
    backdrop-filter: blur(15px);
    color: white;
}

.login-card h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 8px;
}

.subtitle {
    text-align: center;
    color: #aab6d3;
    font-size: 14px;
    margin-bottom: 30px;
}

.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    color: #dce5ff;
}

.input-group input {
    width: 100%;
    padding: 14px;
    border-radius: 9px;
    border: 1px solid #34456c;
    outline: none;
    background: #111e3b;
    color: white;
    font-size: 15px;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: #168bff;
    box-shadow: 0 0 10px rgba(22,139,255,0.25);
}

.password-box {
    position: relative;
}

.password-box input {
    padding-right: 65px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #49a6ff;
    cursor: pointer;
}

.forgot {
    display: block;
    text-align: right;
    color: #49a6ff;
    text-decoration: none;
    font-size: 13px;
    margin-top: -8px;
    margin-bottom: 22px;
}

.forgot:hover,
.email-link:hover {
    color: white;
}

.submit-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(90deg, #087cf5, #19a5ff);
    border: none;
    border-radius: 9px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,130,255,0.35);
}

.email-section {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #34456c;
    font-size: 13px;
    color: #aab6d3;
}

.email-link {
    display: inline-block;
    margin-top: 8px;
    color: #49a6ff;
    text-decoration: none;
    font-size: 14px;
}

#message {
    text-align: center;
    font-size: 13px;
    margin-top: 15px;
    color: #49a6ff;
}

@media(max-width: 480px) {
    .login-card {
        padding: 30px 22px;
    }
}
</style>
</head>

<body>

<div class="login-card">

    <h2>Welcome Back!</h2>
    <p class="subtitle">Please login to your account</p>

    <form id="loginForm" action="./dist/controller/login.php" method="POST"> 

        <div class="input-group">
            <label for="email">Email Address</label>
            <input name="email"
                type="email"
                id="email"
                placeholder="Enter your email"
                required
            >
        </div>

        <div class="input-group">
            <label for="password">Password</label>

            <div class="password-box">
                <input name="password"
                    type="password"
                    id="password"
                    placeholder="Enter your password"
                    required
                >

                <button
                    type="button"
                    class="toggle-password"
                    id="togglePassword"
                >
                    Show
                </button>
            </div>
        </div>

        <a href="mailto:support@example.com?subject=Password%20Recovery"
           class="forgot">
            Forgot Password?
        </a>

        <button type="submit" class="submit-btn">
            Sign In
        </button>

        <p id="message"></p>

    </form>

    <div class="email-section">
        <p>Need help? Contact us directly</p>

        <a href="mailto:support@example.com"
           class="email-link">
            support@example.com
        </a>
    </div>

</div>

<script>
const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");
const loginForm = document.getElementById("loginForm");
const message = document.getElementById("message");

if (new URLSearchParams(window.location.search).has("error")) {
    message.textContent = "Invalid email or password, or account is inactive.";
}

togglePassword.addEventListener("click", function() {
    if (password.type === "password") {
        password.type = "text";
        this.textContent = "Hide";
    } else {
        password.type = "password";
        this.textContent = "Show";
    }
});

// loginForm.addEventListener("submit", function(e) {
//     e.preventDefault();

//     message.textContent = "Login form submitted (Demo Only).";
// });
</script>

</body>
</html>
