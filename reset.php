<?php
  session_start();
  require_once 'db.php';
  
  $error = '';
  $success = '';
  $showForm = false;
  
  $token = $_GET['token'] ?? '';
  
  if (empty($token)) {
      $error = "Invalid or missing reset token.";
  } else {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
      $stmt->execute([$token]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
      if (!$user) {
          $error = "Reset token is invalid or has expired.";
      } else {
          $showForm = true;
      
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $password = $_POST['password'] ?? '';
              $confirm_password = $_POST['confirm_password'] ?? '';
          
              if (empty($password) || empty($confirm_password)) {
                  $error = "Both password fields are required.";
              } elseif ($password !== $confirm_password) {
                  $error = "Passwords do not match.";
              } else {
                  $hashed = password_hash($password, PASSWORD_DEFAULT);
                  $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                  $stmt->execute([$hashed, $user['id']]);
              
                  $success = "Your password has been reset successfully. You can now <a href='login.php'>login</a>.";
                  $showForm = false;
              }
          }
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
    .reset-form {
      max-width: 450px;
      margin: 4rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
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
  </div>
</nav>

<div class="container">
  <div class="reset-form">
    <h3 class="text-center fw-bold mb-4">Reset Password</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($showForm): ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="password" name="password" required minlength="6" />
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6" />
      </div>
      <button type="submit" class="btn btn-dark w-100">Reset Password</button>
    </form>
    <?php else: ?>
      <p class="text-center"><a href="login.php">Back to Login</a></p>
    <?php endif; ?>
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
