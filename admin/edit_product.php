<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['userId'])) {
    header("Location: ../login.php");
    exit();
}

// Check product ID
if (!isset($_GET['id'])) {
    header('Location: all_product.php');
    exit();
}

$id = (int)$_GET['id'];

// Fetch product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit();
}

// Fetch categories
$categoriesStmt = $pdo->query("SELECT * FROM categories");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock_quantity'];
    $categoryId = (int)$_POST['category_id'];
    $description = trim($_POST['description']);

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $imagePath = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $product['image_url'];
    }

    $update = $pdo->prepare("UPDATE products SET name = ?, price = ?, stock_quantity = ?, category_id = ?, description = ?, image_url = ? WHERE id = ?");
    $update->execute([$name, $price, $stock, $categoryId, $description, $imagePath, $id]);

    header("Location: all_product.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background-color: #f8f9fa;
    }

    .navbar {
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .footer {
      background: #212529;
      color: #fff;
      padding: 2rem 0;
      text-align: center;
    }
  </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="../index.php">Clothify</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link active" href="#">Admin</a></li>
          <li class="nav-item"><a class="nav-link" href="add_product.php">Add Product</a></li>
          <li class="nav-item"><a class="nav-link" href="all_product.php">All Products</a></li>
          <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container my-5">
    <h2 class="mb-4">Edit Product</h2>
    <form action="" method="POST" enctype="multipart/form-data" class="border p-4 rounded bg-light">
      <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Price ($)</label>
        <input type="number" name="price" step="0.01" class="form-control" required value="<?= $product['price'] ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Stock Quantity</label>
        <input type="number" name="stock_quantity" class="form-control" required value="<?= $product['stock_quantity'] ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          <option value="">Select a category</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
        <?php if ($product['image_url']): ?>
          <p class="mt-2">Current Image:</p>
          <img src="<?= $product['image_url'] ?>" alt="" style="width:100px; border-radius:5px;">
        <?php endif; ?>
      </div>
      <button type="submit" class="btn btn-primary">Update Product</button>
      <a href="all_product.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
  </div>
    <footer class="footer mt-5">
        <div class="container text-center">
        <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
        </div>
    </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
