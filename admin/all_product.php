<?php
  session_start();
  require_once '../db.php';

  if (!isset($_SESSION['userId'])) {
      header("Location: ../login.php");
      exit();
  }
  if (isset($_GET['delete_id'])) {
      $deleteId = (int)$_GET['delete_id'];
      $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
      $stmt->execute([$deleteId]);
      header('Location: all_product.php?deleted=1');
      exit();
  }

  $stmt = $pdo->query("
      SELECT p.id, p.name, p.price, p.image_url, p.stock_quantity, c.name AS category_name
      FROM products p
      LEFT JOIN categories c ON p.category_id = c.id
      ORDER BY p.id DESC
  ");
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - All Products</title>
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
    <h1 class="mb-4">All Products</h1>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-success">Product deleted successfully.</div>
    <?php endif; ?>

    <a href="add_product.php" class="btn btn-primary mb-3">Add New Product</a>

    <?php if (count($products) === 0): ?>
      <p>No products found.</p>
    <?php else: ?>
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['id']) ?></td>
              <td>
                <img src="<?= htmlspecialchars($p['image_url'] ?: 'https://via.placeholder.com/80?text=No+Image') ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:80px; height:80px; object-fit:cover; border-radius:5px;">
              </td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
              <td>$<?= number_format($p['price'], 2) ?></td>
              <td><?= (int)$p['stock_quantity'] ?></td>
              <td>
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="all_product.php?delete_id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <a href="admin.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>
  <footer class="footer">
    <div class="container">
      <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
