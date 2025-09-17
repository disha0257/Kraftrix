<?php
session_start();
include("db.php");
include("includes/header.php");

// Fetch categories safely
$categories = [];
$result = $conn->query("SELECT category_id, category_name FROM categories");

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    } else {
        echo "<div class='alert alert-warning mt-3'>No categories found. Please add categories first.</div>";
    }
} else {
    echo "<div class='alert alert-danger mt-3'>Database error: " . $conn->error . "</div>";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name']);
    $price      = (float) $_POST['price'];
    $stock      = (int) $_POST['stock'];
    $categoryId = (int) $_POST['category'];

    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $imageName = time() . '_' . $_FILES['image']['name'];
       // move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $imageName);
    } else {
        $imageName = null;
    }

    // Insert into products table
    $stmt = $conn->prepare("INSERT INTO products (name, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdiss", $name, $price, $stock, $categoryId, $imageName);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success mt-3'>Product added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error adding product: " . $stmt->error . "</div>";
    }
}
?>

<div class="container py-5">
    <div class="card p-4 shadow-sm mx-auto" style="max-width:600px;">
        <h3 class="mb-4 text-center">Add New Product</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Price (₹)</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select" required>
                    <option value="" disabled selected>Select Category</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(event)">
                <div class="mt-2">
                    <img id="imagePreview" src="#" alt="Image Preview" style="max-width:200px; display:none; border:1px solid #ddd; padding:5px; border-radius:5px;">
                </div>
                <button type="button" class="btn btn-sm btn-warning mt-2" onclick="resetImage()">Reset Image</button>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">Add Product</button>
                <button type="reset" class="btn btn-secondary" onclick="resetImage()">Clear Form</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('imagePreview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}

function resetImage() {
    document.getElementById('imagePreview').src = '#';
    document.getElementById('imagePreview').style.display = 'none';
}
</script>
