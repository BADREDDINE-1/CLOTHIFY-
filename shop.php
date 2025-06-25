<?php
session_start();
require_once 'db.php';

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - Shop</title>
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
      height: 250px;
      object-fit: cover;
    }

    .filter-bar {
      background-color: #fff;
      padding: 1rem;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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

  <div class="container my-5">
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="filter-bar">
          <h5 class="fw-bold">Filters</h5>
          <form method="GET" action="shop.php">
            <div class="mb-3">
              <label for="category" class="form-label">Category</label>
              <select id="category" name="category" class="form-select">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= htmlspecialchars($cat['id']) ?>"
                    <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="price" class="form-label">Price</label>
              <select id="price" name="price" class="form-select">
                <option value="">All</option>
                <option value="under_25" <?= (isset($_GET['price']) && $_GET['price'] == 'under_25') ? 'selected' : '' ?>>Under $25</option>
                <option value="25_50" <?= (isset($_GET['price']) && $_GET['price'] == '25_50') ? 'selected' : '' ?>>$25 - $50</option>
                <option value="above_50" <?= (isset($_GET['price']) && $_GET['price'] == 'above_50') ? 'selected' : '' ?>>$50+</option>
              </select>
            </div>
            <button type="submit" class="btn btn-dark w-100">Apply Filters</button>
          </form>
        </div>
      </div>

      <div class="col-md-9">
        <div class="row g-4">
          <?php
          // Apply filters if set
          $where = [];
          $params = [];

          if (!empty($_GET['category'])) {
            $where[] = "p.category_id = ?";
            $params[] = $_GET['category'];
          }

          if (!empty($_GET['price'])) {
            switch ($_GET['price']) {
              case 'under_25':
                $where[] = "p.price < 25";
                break;
              case '25_50':
                $where[] = "p.price BETWEEN 25 AND 50";
                break;
              case 'above_50':
                $where[] = "p.price > 50";
                break;
            }
          }

          $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
          if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
          }
          $sql .= " ORDER BY p.id DESC";

          $stmt = $pdo->prepare($sql);
          $stmt->execute($params);
          $filteredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (!$filteredProducts) {
            echo '<p class="text-center">No products found.</p>';
          } else {
            foreach ($filteredProducts as $product) {
              ?>
              <div class="col-md-4">
                <div class="card product-card">
                  <img src="<?= htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/400x250?text=No+Image') ?>"
                    class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                    <a href="product.php?id=<?= (int)$product['id'] ?>" class="btn btn-outline-dark w-100">View</a>
                  </div>
                </div>
              </div>
              <?php
            }
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="container">
      <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
