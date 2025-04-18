<?php
session_start();
include 'functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = login($_POST['email'], $_POST['password']);
    if ($user) {
        $_SESSION['user'] = $user;

        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error_message = "Identifiants incorrects.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <?php if (!empty($error_message)) { ?>
        <div id="errorMessage">
            <p><?= $error_message ?></p>
        </div>
    <?php } ?>
    <h3 id="titre">Merci De Se Connecter !</h3>
    <div class="form">
        <form action="login.php" method="POST">
            <div class="mail-container">
                <label for="mail">Adresse mail : </label>
                <input type="email" name="email" id="mail" required>
            </div>
            <div class="password-container">
                <label for="password">Mot De Passe : </label>
                <input type="password" name="password" id="password" required>
            </div>


            <input type="submit" value="Se Connecter">
        </form>
    </div>
</body>

</html>