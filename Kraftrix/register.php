<?php
session_start();
include("admin/db.php");
include("header.php");

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $password         = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role             = "user"; // default role

    // Validate
    if ($password !== $confirm_password) {
        $error = "❌ Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "⚠️ Password must be at least 6 characters long!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? LIMIT 1");
        if (!$stmt) die("SQL Error in SELECT: " . $conn->error);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "⚠️ Email already registered!";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if (!$stmt) die("SQL Error in INSERT: " . $conn->error);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['role']    = $role;
                $success = "✅ Registration successful! Redirecting to your dashboard...";
                //header("refresh:2;url=user/dashboard.php");
            } else {
                $error = "❌ Something went wrong. Please try again!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | Kraftrix</title>
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
  background: rgba(0,0,0,0.45);
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
.register-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 12px 35px rgba(0,0,0,0.25);
  padding: 40px 35px;
  width: 100%;
  max-width: 430px;
  animation: fadeIn 0.9s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.register-card h3 {
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
.btn-register {
  background: linear-gradient(135deg, #ff9800, #ff5722);
  border: none;
  border-radius: 12px;
  padding: 12px;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
  transition: all 0.3s ease;
}
.btn-register:hover {
  background: linear-gradient(135deg, #e68900, #e64a19);
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}
.login-link {
  text-align: center;
  margin-top: 15px;
}
.login-link a {
  text-decoration: none;
  color: #ff9800;
  font-weight: 500;
  transition: 0.3s;
}
.login-link a:hover {
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
  <div class="register-card">
    <h3>Create Account ✨</h3>

    <?php if($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password (min 6 chars)" required>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" required>
        <label class="form-check-label">
          I agree to the <a href="terms.php">Terms & Conditions</a>
        </label>
      </div>
      <button type="submit" class="btn btn-register w-100">Register</button>
    </form>

    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
