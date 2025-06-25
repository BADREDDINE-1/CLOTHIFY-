<?php
    session_start();
    require_once 'db.php';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: shop.php');
        exit();
    }

    $productId = (int)$_GET['id'];

    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: shop.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - <?= htmlspecialchars($product['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background-color: #f8f9fa;
    }
    .product-image {
      width: 100%;
      object-fit: cover;
      border-radius: 15px;
    }
    .footer {
      background: #212529;
      color: #fff;
      padding: 2rem 0;
      text-align: center;
      margin-top: 4rem;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light sticky-top bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">Clothify</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <?php if (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] === true): ?>
            <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
          
            <?php if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === "admin"): ?>
              <li class="nav-item"><a class="nav-link" href="admin/admin.php">Admin</a></li>
            <?php endif; ?>
            
            <li class="nav-item"><a class="nav-link" style="color: red;" href="logout.php">Logout</a></li>
            <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fa fa-shopping-cart"></i></a></li>
            
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container my-5">
    <div class="row g-4">
      <div class="col-md-6">
        <img 
          src="<?= htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/600x400?text=No+Image') ?>" 
          alt="<?= htmlspecialchars($product['name']) ?>" 
          class="product-image" 
        />
      </div>
      <div class="col-md-6">
        <h1 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h1>
        <p class="text-muted">Category: <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></p>
        <h3 class="text-primary">$<?= number_format($product['price'], 2) ?></h3>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p><strong>Stock Quantity:</strong> <?= (int)$product['stock_quantity'] ?></p>

        <form action="cart_add.php" method="POST" class="mt-4">
          <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" min="1" max="<?= (int)$product['stock_quantity'] ?>" name="quantity" id="quantity" value="1" class="form-control" required>
          </div>
          <?php if ((int)$product['stock_quantity'] > 0): ?>
            <button type="submit" class="btn btn-primary btn-lg">Add to Cart</button>
          <?php else: ?>
            <button type="button" class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="container">
      <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
