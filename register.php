<?php
require 'db.php';

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Name
    if ($name === '') {
        $errors['name'] = 'Name is required.';
    } elseif (!preg_match("/^[a-zA-Z\s.'-]{2,100}$/", $name)) {
        $errors['name'] = 'Name must be 2 to 100 letters.';
    }

    // Email
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'This email is already registered.';
        }
    }

    // Password
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Use an uppercase letter, a lowercase letter, and a number.';
    }

    // Confirm password
    if ($confirm === '') {
        $errors['confirm'] = 'Please confirm your password.';
    } elseif ($password !== $confirm) {
        $errors['confirm'] = 'Passwords do not match.';
    }

    // Save the user
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="frame">

    <aside class="panel">
        <h2 class="panel-title">YOUR CAR<br><span>DESERVES THE BEST</span><br>CARE!</h2>
        <p class="panel-text">Create your account to book services, manage your rentals, and keep track of your vehicle with ease. From your first appointment to your next rental. Everything you need for your car starts here.</p>
    </aside>

    <section class="form-side">
        <form class="form" method="post" novalidate>
            <h1>CREATE AN ACCOUNT</h1>
            <p class="sub">Enter your credentials to create your account</p>

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>" class="<?= isset($errors['name']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['name'])): ?><span class="error-msg"><?= e($errors['name']) ?></span><?php endif; ?>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['email'])): ?><span class="error-msg"><?= e($errors['email']) ?></span><?php endif; ?>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="<?= isset($errors['password']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['password'])): ?><span class="error-msg"><?= e($errors['password']) ?></span><?php endif; ?>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="<?= isset($errors['confirm']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['confirm'])): ?><span class="error-msg"><?= e($errors['confirm']) ?></span><?php endif; ?>

            <button type="submit">Sign Up</button>
            <p class="switch">Already have an account? <a href="login.php">Sign in</a></p>
        </form>
    </section>

</main>
</body>
</html>