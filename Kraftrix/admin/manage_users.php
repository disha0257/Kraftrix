<?php
session_start();
include("db.php");
include("includes/header.php");

// Show success messages
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    if ($msg == 'deleted') echo "<div class='alert alert-success'>User deleted successfully.</div>";
    if ($msg == 'updated') echo "<div class='alert alert-success'>User updated successfully.</div>";
}
?>

<h2 class="fw-bold mb-4">Manage Users</h2>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
      if ($users->num_rows === 0) {
          echo "<tr><td colspan='6'>No users found.</td></tr>";
      } else {
          while ($row = $users->fetch_assoc()) {
      ?>
        <tr>
          <td>#<?php echo $row['user_id']; ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td><?php echo htmlspecialchars($row['email']); ?></td>
          <td><?php echo ucfirst($row['role']); ?></td>
          <td><?php echo $row['created_at']; ?></td>
          <td>
            <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
            <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i> Delete</a>
          </td>
        </tr>
      <?php 
          }
      }
      ?>
    </tbody>
  </table>
</div>

<?php include("includes/footer.php"); ?>
