<?php
  session_start();
  require_once 'db.php';
  
  if (!isset($_SESSION['userId'])) {
      header('Location: login.php');
      exit();
  }
  
  $stmt = $pdo->prepare("SELECT id FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
  $stmt->execute([$_SESSION['userId']]);
  $order = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if ($order) {
      $update = $pdo->prepare("UPDATE orders SET status = 'success' WHERE id = ?");
      $update->execute([$order['id']]);
  
      unset($_SESSION['cart']);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Success</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container text-center my-5">
    <h1 class="text-success">Payment Successful!</h1>
    <p class="lead">Thank you for your purchase. Your order has been placed successfully.</p>
    <a href="shop.php" class="btn btn-primary mt-3">Continue Shopping</a>
  </div>
</body>
</html>
