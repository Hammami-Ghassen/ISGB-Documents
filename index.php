<?php
include 'functions.php';
session_start();
if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$timeout_duration = 900; // 900 seconds = 15 minutes

if (
    isset($_SESSION['LAST_ACTIVITY']) &&
    (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration
) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();


?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Accueil - Faculté</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <img style="height: 100px; border-radius: 20px;" src="isg.png" alt="logo" id="logo-isg">
            </div>
            <ul class="nav-links" id="nav-links">
                <li><a href="#services">Services</a></li>
                <li><a href="#procedures">Procédures</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="logout.php">Se Déconnecter</a></li>
            </ul>
            <div class="menu-icon" id="menu-icon">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2>Bienvenue sur le portail des procédures administratives</h2>
            <p>Accédez rapidement aux services administratifs de votre faculté.</p>
            <a href="#services" class="btn">Explorer les Services</a>
        </div>
    </section>

    <section id="services" class="section">
        <h2>Nos Services</h2>
        <div class="service-list">
            <div class="service-item">
                <h3>Inscription administrative</h3>
                <p>Accédez à votre inscription pour l'année académique en cours.</p>
                <br>
                <a href="#">Commencer</a>
            </div>
            <div class="service-item">
                <h3>Demande de bourses</h3>
                <p>Faites votre demande de bourse en quelques étapes simples.</p>
                <br>
                <a href="#">Faire une demande</a>
            </div>
            <div class="service-item">
                <h3>Attestations et certificats</h3>
                <p>Demandez vos attestations de scolarité et autres certificats.</p>
                <br>
                <a href="#">Demander</a>
            </div>
        </div>
    </section>

    <section id="procedures" class="section">
        <h2>Procédures Courantes</h2>
        <ul class="procedure-list">
            <li><a href="#">Procédure de réinscription</a></li>
            <li><a href="#">Procédure de demande de diplômes</a></li>
            <li><a href="#">Procédure de demande de transfert</a></li>
            <li><a href="#">Procédure d'obtention de stage</a></li>
        </ul>
    </section>

    <section id="contact" class="section">
        <h2>Contactez-nous</h2>
        <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
        <p>Email: support@faculte.com</p>
        <p>Téléphone: +33 1 23 45 67 89</p>
    </section>

    <footer>
        <p>&copy; 2025 Faculté - Tous droits réservés</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>