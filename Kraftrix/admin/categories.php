<?php
session_start();
include("db.php");
include("includes/header.php");

// Handle Add Category
if(isset($_POST['add_category'])){
    $name = trim($_POST['category_name']);
    $desc = trim($_POST['description']);

    if($name != ""){
        $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $desc);
        $stmt->execute();
    }
}

// Handle Edit Category
if(isset($_POST['edit_category'])){
    $id = (int)$_POST['category_id'];
    $name = trim($_POST['category_name']);
    $desc = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE categories SET category_name=?, description=? WHERE category_id=?");
    $stmt->bind_param("ssi", $name, $desc, $id);
    $stmt->execute();
}

// Handle Delete Category
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Fetch all categories
$result = $conn->query("SELECT * FROM categories");
$categories = [];
if($result){
    while($row = $result->fetch_assoc()){
        $categories[] = $row;
    }
}
?>

<div class="container py-5">
    <h3 class="mb-4 text-center">Manage Categories</h3>

    <!-- Add Category Form -->
    <div class="card mb-4 p-3 shadow-sm">
        <form method="POST">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="description" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_category" class="btn btn-success w-100">Add</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="card shadow-sm p-3">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $cat): ?>
                <tr>
                    <td><?= $cat['category_id'] ?></td>
                    <td><?= htmlspecialchars($cat['category_name']) ?></td>
                    <td><?= htmlspecialchars($cat['description']) ?></td>
                    <td><?= $cat['created_at'] ?></td>
                    <td>
                        <!-- Edit Button triggers modal -->
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['category_id'] ?>">Edit</button>
                        <a href="?delete=<?= $cat['category_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $cat['category_id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $cat['category_id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST">
                          <div class="modal-header">
                              <h5 class="modal-title" id="editModalLabel<?= $cat['category_id'] ?>">Edit Category</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                              <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                              <div class="mb-3">
                                  <label>Category Name</label>
                                  <input type="text" name="category_name" class="form-control" value="<?= htmlspecialchars($cat['category_name']) ?>" required>
                              </div>
                              <div class="mb-3">
                                  <label>Description</label>
                                  <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($cat['description']) ?>">
                              </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" name="edit_category" class="btn btn-primary">Save Changes</button>
                          </div>
                      </form>
                    </div>
                  </div>
                </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS (make sure you have it included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
