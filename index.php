<?php
  session_start();
  require_once 'db.php';


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
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      font-family: 'Outfit', sans-serif;
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
    }

    .navbar {
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      z-index: 1000;
    }

    .hero {
      background: url('https://images.unsplash.com/photo-1521335629791-ce4aec67dd47?auto=format&fit=crop&w=1600&q=80') no-repeat center center/cover;
      height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-shadow: 0 2px 6px rgba(0,0,0,0.5);
      flex-shrink: 0;
    }

    .hero h1 {
      font-size: 4rem;
      font-weight: 700;
    }

    .hero p {
      font-size: 1.3rem;
    }

    .container.my-5 {
      flex: 1 0 auto;
    }

    .product-card {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.2s ease;
    }

    .product-card:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
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
      flex-shrink: 0;
    }

    @media (max-width: 768px) {
      .hero {
        height: 50vh;
      }
    
      .hero h1 {
        font-size: 2.5rem;
      }
    
      .hero p {
        font-size: 1rem;
      }
    
      .product-card img.card-img-top {
        height: 300px;
      }
    }

    @media (max-width: 480px) {
      .hero {
        height: 40vh;
      }
    
      .hero h1 {
        font-size: 2rem;
      }
    
      .hero p {
        font-size: 0.9rem;
      }
    
      .product-card img.card-img-top {
        height: 200px;
      }
    }
    .hero {
      position: relative;
      height: 80vh;
      color: #fff;
      text-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      overflow: hidden;
    }
    .hero .text-center {
      position: relative;
      z-index: 2;
    }

    .hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background-size: cover;
      background-position: left center;
      animation: slideBackground 15s infinite ease-in-out;
      z-index: 1;
      opacity: 0.85;
    }

    @keyframes slideBackground {
      0% {
        background-image: url('cl1.jpg');
        background-position: left center;
      }
      25% {
        background-position: center center;
      }
      33% {
        background-image: url('cl2.jpg');
        background-position: right center;
      }
      58% {
        background-position: left center;
      }
      66% {
        background-image: url('cl3.jpg');
        background-position: center center;
      }
      91% {
        background-position: right center;
      }
      100% {
        background-image: url('cl1.jpg');
        background-position: left center;
      }
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
