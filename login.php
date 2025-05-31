<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {
        // Fetch user by email
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password correct, set session and redirect
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            header("Location: index.php");
            exit;
            
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please enter email and password.";
    }
        
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />

    
    <style>
.bg-image {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  object-fit: cover;  /* ensures image covers without distortion */
  z-index: -1;        /* send it behind other content */
}
.bg-overlay {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4); /* black with 40% opacity */
  pointer-events: none; /* so clicks go through */
  z-index: 1;
}

.login-container {
  position: relative;
  z-index: 1;         /* above the background image */
}
        body, html {
  height: 100%;
  margin: 0;
  overflow: hidden;  /* disable scroll */
}

/* Fixed, centered container */
.login-container {
  position: fixed;       /* fixed in viewport */
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);  /* center horizontally & vertically */
  width: 350px;         /* fixed width, adjust as needed */
  background: white;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  box-sizing: border-box;
}

/* Logo image */
.login-container img {
  display: block;
  margin: 0 auto 20px auto;
  max-width: 150px;
  height: auto;
  object-fit: contain;
}
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 60px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            width: 450px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.5);
        }
        button {
            width: 105%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error-msg {
            color: #b00020;
            background-color: #f9d6d5;
            border: 1px solid #b00020;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 600;
            text-align: center;
        }
        
    </style>
</head>
<body>
<img src="images/lebhist.jpg" alt="Background" class="bg-image" />
  <div class="bg-overlay"></div
<div class="login-container">
  <!-- Your login form here -->
</div>
<div class="login-container">

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
  <img src="images/loginleb.png" alt="Login Banner" style="display: block; margin: 0 auto 20px auto; max-width: 300px; height: auto;" />

    <form method="post" novalidate>
        <label for="email">Username</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" required autofocus />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required />

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
