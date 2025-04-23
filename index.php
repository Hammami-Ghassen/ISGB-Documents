<?php
include 'functions.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Gérer le timeout de session
$timeout_duration = 900; // 15 minutes

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time(); // Mise à jour de l'activité
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Portail des Procédures Administratives</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="logo">Faculté ISGB</div>
            <nav>
                <div class="menu-icon" onclick="toggleMenu()">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue sur le portail administratif</h1>
            <p>Accédez facilement à vos documents officiels</p>
            <a href="#services" class="hero-btn">Voir les services</a>
        </div>
    </section>

    <section id="services" class="section">
        <h2>Nos Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <h3>Attestation de Réussite & Relevé</h3>
                <p>Obtenez vos attestations de réussite et relevés de notes.</p>
                <a href="attestation_reussite.php">Commencer</a>
            </div>
            <div class="service-card">
                <h3>Attestation d'Inscription</h3>
                <p>Demandez votre attestation d'inscription.</p>
                <a href="attestation_inscription.php">Faire une demande</a>
            </div>
            <div class="service-card">
                <h3>Attestation de Présence</h3>
                <p>Demandez une attestation de présence.</p>
                <a href="attestation_presence.php" id="corriger">Demander</a>
            </div>
            <div class="service-card">
                <h3>Demande de Stage</h3>
                <p>Effectuez une demande de stage pour votre cursus.</p>
                <a href="demande_stage.php" id="corriger" >Faire une demande</a>
            </div>
        </div>
    </section>

    <section id="contact" class="section contact">
        <h2>Contactez-nous</h2> 
        <p>📧 Email : isgb@isgb.rnu.tn</p>
        <p>📞 Téléphone : +216 72 570 780</p>
    </section>

    <footer>
        <p>&copy; 2025 ISGB - Tous droits réservés</p>
    </footer>

    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }
    </script>
</body>
</html>
