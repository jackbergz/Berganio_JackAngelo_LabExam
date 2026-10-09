<?php
require 'db.php';

$errors = [];
$email = '';
$success = '';

if (isset($_GET['registered'])) {
    $success = 'Account created! You can log in now.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Required + email format
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    // Required password
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    // Check the account
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $success = 'Login successful! Welcome back, ' . $user['name'] . '.';
            $email = '';
        } else {
            $errors['form'] = 'Incorrect email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="frame">

    <aside class="panel">
        <h2 class="panel-title">YOUR CAR<br><span>DESERVES THE BEST</span><br>CARE!</h2>
        <p class="panel-text">Log in to unlock faster booking, track your appointments and rentals, and pick up right where you left off. Whether it's a quick check-up, a full repair, or your next rental, your car's next stop starts here.</p>
    </aside>

    <section class="form-side">
        <form class="form" method="post" novalidate>
            <h1>WELCOME BACK!</h1>
            <p class="sub">Please enter your details.</p>

            <?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
            <?php if (isset($errors['form'])): ?><div class="alert error"><?= e($errors['form']) ?></div><?php endif; ?>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['email'])): ?><span class="error-msg"><?= e($errors['email']) ?></span><?php endif; ?>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="<?= isset($errors['password']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['password'])): ?><span class="error-msg"><?= e($errors['password']) ?></span><?php endif; ?>

            <div class="row">
                <label class="check"><input type="checkbox" name="remember"> Remember me</label>
                <a href="#" class="muted">Forgot password</a>
            </div>

            <button type="submit">Login</button>
            <p class="switch">Don't have an account? <a href="register.php">Sign up</a></p>
        </form>
    </section>

</main>
</body>
</html>