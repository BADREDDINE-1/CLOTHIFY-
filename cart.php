<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];

if (isset($_POST['clear_cart'])) {
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    header('Location: cart.php');
    exit();
}


$stmt = $pdo->prepare("
    SELECT products.id, products.name, products.price, products.image_url, cart_items.quantity
    FROM cart_items
    INNER JOIN products ON cart_items.product_id = products.id
    WHERE cart_items.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cartItems as &$item) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
}
unset($item);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background-color: #f8f9fa;
    }
    .footer {
      background: #212529;
      color: #fff;
      padding: 2rem 0;
      text-align: center;
      margin-top: 4rem;
    }
    img.product-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <h2 class="mb-4">Your Shopping Cart</h2>

    <?php if (empty($cartItems)): ?>
      <p>Your cart is empty.</p>
      <a href="shop.php" class="btn btn-primary">Shop Now</a>
    <?php else: ?>
    <table class="table table-bordered align-middle">
      <thead>
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cartItems as $item): ?>
        <tr>
          <td>
            <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/80?text=No+Image') ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 10px; vertical-align: middle;">
            <?= htmlspecialchars($item['name']) ?>
          </td>
          <td>$<?= number_format($item['price'], 2) ?></td>
          <td><?= (int)$item['quantity'] ?></td>
          <td>$<?= number_format($item['subtotal'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <th colspan="3" class="text-end">Total:</th>
          <th>$<?= number_format($total, 2) ?></th>
        </tr>
      </tbody>
  </table>
  

      <form method="post" class="mb-3">
        <button name="clear_cart" class="btn btn-danger">Clear Cart</button>
      </form>
      <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
      <a href="payment.php" class="btn btn-success">Proceed to Checkout</a>
    <?php endif; ?>
  </div>

  <footer class="footer">
    <div class="container">
      <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
