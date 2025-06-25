<?php
  session_start();
  require_once 'db.php';
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require 'vendor/autoload.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));
    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $name_pattern = "/^[a-zA-Z\s]{3,50}$/";
    $password_pattern = "/^(?=.*[A-Za-z])(?=.*\d).{8,}$/";


    if (!preg_match($name_pattern, $name)) {
      $_SESSION['error'] = "Name must be letters and spaces only, 3-50 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $_SESSION['error'] = "Invalid email format.";
    } elseif (!preg_match($password_pattern, $password)) {
      $_SESSION['error'] = "Password must be minimum 8 characters, with at least one letter and one number.";
    } elseif ($password !== $confirm_password) {
      $_SESSION['error'] = "Passwords do not match.";
    } else {
      try {
        $sql = "INSERT INTO users (username, email, password, verification_code, is_verified, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if ($stmt->execute([$name, $email, $hashed_password, $code, 0, 'customer'])) {
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
            $mail->addAddress($email, $name);
            $mail->Subject = 'Account Confirmation - Clothify';
            $mail->isHTML(true);
            $mail->Body = "
              <div style='font-family: Arial, sans-serif; color: #333;'>
                <h1 style='font-size: 48px; color: #222; margin-bottom: 0;'>CLOTHIFY</h1>
                <h2 style='color: #555;'>Thank you for registering with us!</h2>
                <p>Your account has been created successfully.</p>
                <p><strong>Your confirmation code is:</strong></p>
                <p style='font-size: 28px; font-weight: bold; background-color: #f0f0f0; padding: 10px; display: inline-block;'>$code</p>
                <p>Please enter this code on the verification page to activate your account.</p>
                <br>
              <p>Best regards,<br>The Clothify Team</p>
            </div>
          ";
          $mail->AltBody = "Thank you for registering with Clothify!\nYour confirmation code is: $code\nPlease enter this code on the verification page to activate your account.";
      
          if($mail->send()) {
              $sql = "SELECT id FROM users WHERE email = ?";
              $stmt = $pdo->prepare($sql);
              $stmt->execute([$email]);
              $user = $stmt->fetch(PDO::FETCH_ASSOC);
              if ($user) {
                $_SESSION['code'] = $code;
                $_SESSION['user_id'] = $user['id'];
                header("Location: verify.php");
                exit();
              }
            }
          } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
          }
          $_SESSION['success'] = "Registration successful! You can now log in.";
          header("Location: login.php");
          exit();
        }
      } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clothify - Register</title>
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
    .register-form {
      max-width: 500px;
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
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link active" href="register.php">Register</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="register-form">
      <h3 class="text-center fw-bold mb-4">Create Account</h3>

      <?php
        if (!empty($_SESSION['error'])) {
          echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error']).'</div>';
          unset($_SESSION['error']);
        }
        if (!empty($_SESSION['success'])) {
          echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['success']).'</div>';
          unset($_SESSION['success']);
        }
      ?>

      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="name" name="name" required />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required />
        </div>
        <div class="mb-3">
          <label for="confirm-password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm-password" name="confirm_password" required />
        </div>
        <button type="submit" class="btn btn-dark w-100">Register</button>
        <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
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