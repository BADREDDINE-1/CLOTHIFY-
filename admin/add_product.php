<?php
    session_start();
    require_once '../db.php';

    if (!isset($_SESSION['userId'])) {
        header("Location: ../login.php");
        exit();
    }
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['userId']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        exit('Access denied');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = trim($_POST['price']);
        $category_id = $_POST['category_id'] ?? null;
        $stock_quantity = intval($_POST['stock_quantity']);
        $image_url = trim($_POST['image_url']);

        $errors = [];
        if (empty($name)) $errors[] = "Product name is required.";
        if (!is_numeric($price) || $price < 0) $errors[] = "Price must be a valid positive number.";
        if (!empty($category_id) && !is_numeric($category_id)) $errors[] = "Invalid category.";
        if ($stock_quantity < 0) $errors[] = "Stock quantity cannot be negative.";

        if (empty($errors)) {
            $sql = "INSERT INTO products (category_id, name, description, price, image_url, stock_quantity)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id ?: null, $name, $description, $price, $image_url, $stock_quantity]);
            $success = "Product added successfully.";
        }
    }

    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Add Product</title>
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
    <div class="container mt-5" style="max-width: 600px;">
        <h2>Add New Product</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                <?php foreach($errors as $error): ?>
                    <li><?=htmlspecialchars($error)?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name *</label>
                <input type="text" name="name" id="name" class="form-control" required value="<?=htmlspecialchars($_POST['name'] ?? '')?>">
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                  <option value="">Select a category (optional)</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id']) ?>" 
                      <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cat['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>


            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"><?=htmlspecialchars($_POST['description'] ?? '')?></textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price ($) *</label>
                <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" required value="<?=htmlspecialchars($_POST['price'] ?? '')?>">
            </div>

            <div class="mb-3">
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                <input type="number" min="0" name="stock_quantity" id="stock_quantity" class="form-control" value="<?=htmlspecialchars($_POST['stock_quantity'] ?? '0')?>">
            </div>

            <div class="mb-3">
                <label for="image_url" class="form-label">Image URL</label>
                <input type="url" name="image_url" id="image_url" class="form-control" value="<?=htmlspecialchars($_POST['image_url'] ?? '')?>">
            </div>

            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
    <footer class="footer">
      <div class="container">
        <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
