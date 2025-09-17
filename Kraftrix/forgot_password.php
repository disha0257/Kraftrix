<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("admin/db.php");
include("header.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
        if (!$stmt) {
            die("Query prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $token = bin2hex(random_bytes(50));
            $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Save token
            $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_expire=? WHERE email=?");
            if (!$stmt) {
                die("Update prepare failed: " . $conn->error);
            }
            $stmt->bind_param("sss", $token, $expire, $email);
            $stmt->execute();

            // Reset link
            $reset_link = "http://localhost/kraftrix/reset_password.php?token=" . $token;

            // Dev mode output
            $dev_link = "Reset Link (dev only): <a href='$reset_link'>$reset_link</a>";
        }

        $success = "If this email is registered, a reset link has been sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | Kraftrix</title>
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
    <h3 class="text-center mb-4">Forgot Password 🔑</h3>

    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if(isset($dev_link)) echo "<div class='alert alert-info'>$dev_link</div>"; ?>

    <form method="POST">
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
      </div>
      <button type="submit" class="btn btn-warning w-100">Send Reset Link</button>
    </form>

    <div class="text-center mt-3">
      <a href="login.php">⬅ Back to Login</a>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
