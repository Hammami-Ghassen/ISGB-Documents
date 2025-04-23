<!-- attestation_inscription.php -->
<?php
include 'functions.php';
session_start();

if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$demande_succes = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier que l’utilisateur est connecté
    if (empty($_SESSION['user']['id_utilisateur'])) {
        header('Location: login.php');
        exit;
    }

    $id_utilisateur = $_SESSION['user']['id_utilisateur'];
    $identifiant    = soumettreDemandeInscription($id_utilisateur, $_POST);

    if ($identifiant !== false) {
        $demande_succes = true;
        // Vous pouvez ensuite afficher :
        // "Votre demande a été enregistrée, numéro de suivi : $identifiant"
    } else {
        $error_message = "Erreur lors de l’envoi de votre demande. Merci de réessayer plus tard.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation d'Inscription</title>
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
        <h2>Demande d'Attestation d'Inscription</h2>
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
