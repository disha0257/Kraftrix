<?php
session_start();
include("admin/db.php");

// Handle login first (before including header/footer)
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user details
    $stmt = $conn->prepare("SELECT user_id, email, password, role FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // ✅ Save session variables
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}

// ✅ Include header after login check to avoid redirect issues
include("header.php"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Kraftrix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('images/login_img.jpg') no-repeat center center;
      background-size: cover;
      position: relative;
    }
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 0;
    }
    .main-content {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 120px 15px 40px;
      position: relative;
      z-index: 1;
    }
    .login-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 12px 35px rgba(0,0,0,0.2);
      padding: 40px 35px;
      width: 100%;
      max-width: 430px;
      animation: fadeIn 0.9s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .login-card h3 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 25px;
      color: #1a1a1a;
    }
    .form-control {
      border-radius: 12px;
      padding: 12px;
      font-size: 15px;
    }
    .btn-login {
      background: linear-gradient(135deg, #007bff, #0056d2);
      border: none;
      border-radius: 12px;
      padding: 12px;
      font-size: 16px;
      font-weight: 600;
      color: #fff;
      transition: all 0.3s ease;
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #0056d2, #003da8);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .extra-links {
      margin-top: 15px;
      text-align: center;
    }
    .extra-links a {
      text-decoration: none;
      color: #ff9800;
      font-weight: 500;
      transition: 0.3s;
    }
    .extra-links a:hover {
      color: #e67e00;
    }
    .alert {
      font-size: 14px;
      border-radius: 10px;
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="login-card">
    <h3>Welcome Back 👋</h3>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form method="POST">
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
      </div>
      <button type="submit" class="btn btn-login w-100">Login</button>
    </form>

    <div class="extra-links">
      <p><a href="forgot_password.php">Forgot Password?</a></p>
      <p>Don’t have an account? <a href="register.php">Create one</a></p>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
