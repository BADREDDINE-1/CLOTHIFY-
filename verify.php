<?php
    session_start();
    require 'db.php';

    $error = $_SESSION['error'] ?? '';
    $message = $_SESSION['message'] ?? '';
    unset($_SESSION['error'], $_SESSION['message']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = trim($_POST['verification_code'] ?? '');

        if (empty($code)) {
            $_SESSION['error'] = "Please enter the verification code.";
            header('Location: verify.php');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Session expired. Please login again.";
            header('Location: login.php');
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT verification_code, is_verified FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header('Location: login.php');
            exit;
        }

        if ($user['is_verified']) {
            $_SESSION['message'] = "Your account is already verified.";
            header('Location: login.php');
            exit;
        }

        if ($code === $user['verification_code']) {
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
            $stmt->execute([$user_id]);

            $_SESSION['message'] = "Your email has been verified successfully.";
            header('Location: login.php');
            exit;
        } else {
            $_SESSION['error'] = "Invalid verification code.";
            header('Location: verify.php');
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - Email Verification</title>
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

    .verification-form {
      max-width: 400px;
      margin: 5rem auto;
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
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="verification-form">
      <h3 class="text-center fw-bold mb-4">Email Verification</h3>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>
      <form method="POST" action="verify.php">
        <div class="mb-3">
          <label for="verification-code" class="form-label">Enter Verification Code</label>
          <input type="text" class="form-control" id="verification-code" name="verification_code" required maxlength="10" />
        </div>
        <button type="submit" class="btn btn-dark w-100">Verify Email</button>
      </form>
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
