<?php
session_start();
require_once 'db.php';

// Fetch max 3 featured products (latest 3 products)
$stmt = $pdo->prepare("SELECT id, name, price, image_url FROM products ORDER BY id DESC LIMIT 3");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background-color: #f8f9fa;
    }

    .navbar {
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .hero {
      background: url('https://images.unsplash.com/photo-1521335629791-ce4aec67dd47?auto=format&fit=crop&w=1600&q=80') no-repeat center center/cover;
      height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
    }

    .hero h1 {
      font-size: 4rem;
      font-weight: 700;
    }

    .hero p {
      font-size: 1.3rem;
    }

    .product-card {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.2s ease;
    }

    .product-card:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .product-card img.card-img-top {
      width: 100%;
      height: 500px;
      object-fit: cover;
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
      <a class="navbar-brand fw-bold" href="#">Clothify</a>
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

  <section class="hero">
    <div class="text-center">
      <h1>Upgrade Your Style</h1>
      <p>Discover our latest fashion collection</p>
      <a href="shop.php" class="btn btn-dark btn-lg mt-4">Shop Now</a>
    </div>
  </section>

  <section class="container my-5">
    <div class="row text-center mb-4">
      <h2 class="fw-bold">Featured Products</h2>
    </div>
    <div class="row g-4">
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
          <div class="col-md-4">
            <div class="card product-card">
              <img src="<?= htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/800x600?text=No+Image') ?>"
                class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-outline-dark">View</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center">No products found.</p>
      <?php endif; ?>
    </div>
    <div class="text-center mt-4">
      <a href="shop.php" class="btn btn-primary btn-lg">See More Products</a>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
