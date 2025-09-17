<?php
// admin/edit_user.php
session_start();
require_once "db.php";

// --- BASIC GUARDS ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die("Access denied. Please log in.");
}
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("Access denied. Admins only.");
}

// Validate ID
$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    header("Location: manage_users.php?err=Invalid+user+ID");
    exit;
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: manage_users.php?err=User+not+found");
    exit;
}
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];

    if ($name === "" || $email === "" || $role === "") {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE user_id=?");
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: manage_users.php?msg=updated");
            exit;
        } else {
            $error = "Error updating user: " . $conn->error;
        }
    }
}
?>

<?php include("includes/header.php"); ?>

<h2 class="fw-bold mb-4">Edit User</h2>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" class="card p-4 shadow-sm" style="max-width: 500px;">
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" class="form-select" required>
      <option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>User</option>
      <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
    </select>
  </div>
  <div class="d-flex justify-content-between">
    <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">Update User</button>
  </div>
</form>

<?php include("includes/footer.php"); ?>
