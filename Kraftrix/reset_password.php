<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("admin/db.php");
include("header.php");

$token = $_GET['token'] ?? '';
$valid = false;

if ($token) {
    $stmt = $conn->prepare("SELECT user_id, reset_expire FROM users WHERE reset_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (strtotime($row['reset_expire']) > time()) {
            $valid = true;
            $user_id = $row['user_id'];
        } else {
            $error = "This reset link has expired.";
        }
    } else {
        $error = "Invalid reset link.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && $valid) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expire=NULL WHERE user_id=?");
        $stmt->bind_param("si", $hash, $user_id);
        $stmt->execute();

        $success = "Password reset successfully. <a href='login.php'>Login</a>";
        $valid = false; // hide form
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | Kraftrix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: url('images/login_img.jpg') no-repeat center center;
      background-size: cover;
    }
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.4);
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
    .card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 12px 35px rgba(0,0,0,0.2);
      padding: 40px 35px;
      max-width: 430px;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="card">
    <h3 class="text-center mb-4">Reset Password 🔒</h3>

    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <?php if($valid): ?>
      <form method="POST">
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="New Password" required>
        </div>
        <div class="mb-3">
          <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
