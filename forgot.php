<?php
session_start();
require_once 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->execute([$token, $expires, $email]);

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'badr.liongames@gmail.com';
                $mail->Password = 'rhtm aahv sfaf xnfn';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('badr.liongames@gmail.com', 'Clothify Store');
                $mail->addAddress($email, $user['username']);

                $mail->Subject = 'Password Reset - Clothify';
                $resetLink = "http://localhost/CLOTHIFY/reset.php?token=$token";

                $mail->isHTML(true);
                $mail->Body = "
                    <h2>Reset Your Password</h2>
                    <p>Click the button below to reset your password:</p>
                    <a href='$resetLink' style='padding:10px 20px;background:#000;color:#fff;text-decoration:none;border-radius:5px;'>Reset Password</a>
                    <p>If you did not request this, please ignore this email.</p>
                ";
                $mail->AltBody = "Reset your password using the link: $resetLink";

                $mail->send();
                $success = "A reset link has been sent to your email.";
            } catch (Exception $e) {
                $error = "Email could not be sent. Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "No account found with that email.";
        }
    } else {
        $error = "Email is required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - Forgot Password</title>
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
    .forgot-form {
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
  <div class="forgot-form">
    <h3 class="text-center fw-bold mb-4">Forgot Password</h3>
    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="email" class="form-label">Enter your email address</label>
        <input type="email" class="form-control" id="email" name="email" required />
      </div>
      <button type="submit" class="btn btn-dark w-100">Send Reset Link</button>
      <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
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
