<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$error = '';
$success = '';

$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name'] ?? '');
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!preg_match("/^[a-zA-Z\s]{3,50}$/", $newName)) {
        $error = "Name must be letters only (3-50 characters).";
    } elseif (!empty($newPassword) && (!preg_match("/^(?=.*[A-Za-z])(?=.*\d).{8,}$/", $newPassword) || $newPassword !== $confirmPassword)) {
        $error = "Password must be at least 8 characters with letters and numbers, and match confirmation.";
    } else {
        $updateSql = "UPDATE users SET username = ?";
        $params = [$newName];

        if (!empty($newPassword)) {
            $updateSql .= ", password = ?";
            $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $updateSql .= " WHERE id = ?";
        $params[] = $userId;

        $stmt = $pdo->prepare($updateSql);
        if ($stmt->execute($params)) {
            $success = "Profile updated successfully.";
            $user['username'] = $newName;
        } else {
            $error = "Something went wrong.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Clothify - Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background-color: #f8f9fa;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .navbar {
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .profile-card {
      background: #fff;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
      max-width: 600px;
      margin: 2rem auto;
    }
    .footer {
      background: #212529;
      color: #fff;
      padding: 2rem 0;
      text-align: center;
      margin-top: auto;
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
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
        <?php if (isset($_SESSION['userRole']) && $_SESSION['userRole'] === "admin"): ?>
          <li class="nav-item"><a class="nav-link" href="admin/admin.php">Admin</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" style="color: red;" href="logout.php">Logout</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fa fa-shopping-cart"></i></a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <div class="profile-card">
    <h3 class="fw-bold mb-3">Your Profile</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['username']) ?>" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled />
      </div>
      <div class="mb-3">
        <label class="form-label">New Password (optional)</label>
        <input type="password" class="form-control" name="password" />
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" name="confirm_password" />
      </div>
      <button type="submit" class="btn btn-dark w-100">Update Profile</button>
    </form>
    <p class="text-muted mt-3">Member since: <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
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
