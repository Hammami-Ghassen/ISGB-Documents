<!-- attestation_reussite.php -->
<?php
include 'functions.php';
session_start();

if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$demande_succes = false;
$error_message  = '';
$identifiant    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user']['id_utilisateur'])) {
        header('Location: login.php');
        exit;
    }
    $id_utilisateur = $_SESSION['user']['id_utilisateur'];
    $identifiant    = soumettreDemandeInscription($id_utilisateur, $_POST);

    if ($identifiant !== false) {
        $demande_succes = true;
    } else {
        $error_message = "Une erreur est survenue, merci de réessayer plus tard.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Réussite & Relevé de Notes</title>
    <link rel="stylesheet" href="forms.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="logo">Faculté ISGB</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Acceuil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="form">
        <h2>Demande d'Attestation de Réussite & Relevé de Notes</h2>
            <form method="POST">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required>

                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <button type="submit">Demander l'attestation</button>
            </form>
    </div>
    <footer>
        <p>&copy; 2025 ISGB - Tous droits réservés</p>
    </footer>
</body>
</html>
