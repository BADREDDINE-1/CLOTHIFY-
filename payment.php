<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['userId'];

$stmt = $pdo->prepare("
    SELECT products.id, products.name, products.price, products.image_url, cart_items.quantity
    FROM cart_items
    INNER JOIN products ON cart_items.product_id = products.id
    WHERE cart_items.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

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
  <title>Clothify - Payment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background-color: #f8f9fa;
    }
    .payment-form {
      background: #fff;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
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
  <div class="container my-5">
    <h2 class="mb-4 text-center">Checkout & Payment</h2>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="payment-form">
          <h4 class="mb-3">Billing Information</h4>
          <form action="payment_success.php" method="POST">
            <div class="mb-3">
              <label for="fullname" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Shipping Address</label>
              <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>

            <h4 class="my-4">Payment Method</h4>
            <div class="mb-3">
              <label for="card" class="form-label">Card Number</label>
              <input type="text" class="form-control" id="card" name="card" placeholder="XXXX-XXXX-XXXX-XXXX" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="expiry" class="form-label">Expiry Date</label>
                <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/YY" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="cvv" class="form-label">CVV</label>
                <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" required>
              </div>
            </div>

            <input type="hidden" name="total" value="<?= $total ?>">
            <button type="submit" class="btn btn-success w-100 mt-3">Pay $<?= number_format($total, 2) ?></button>
          </form>
        </div>
      </div>

      <div class="col-md-6">
        <div class="bg-white p-4 rounded shadow-sm">
          <h5 class="mb-3">Order Summary</h5>
          <ul class="list-group mb-3">
            <?php foreach ($cartItems as $item): ?>
              <li class="list-group-item d-flex justify-content-between">
                <?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?>
                <span>$<?= number_format($item['subtotal'], 2) ?></span>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between fw-bold">
              Total
              <span>$<?= number_format($total, 2) ?></span>
            </li>
          </ul>
          <a href="cart.php" class="btn btn-outline-secondary w-100">Back to Cart</a>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="container">
      <p class="mb-0">&copy; 2025 Clothify. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
