<!-- demande_stage.php -->
<?php
include 'functions.php';
session_start();

if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traitement de la demande de stage
    $id_utilisateur = $_SESSION['user']['id_utilisateur'];
    $identifiant    = soumettreDemandeStage($id_utilisateur, $_POST);
    if ($identifiant !== false) {
        $demande_succes = true;
        // Vous pouvez afficher un message de succès ici
    } else {
        $error_message = "Une erreur est survenue lors de l'envoi de votre demande. Veuillez réessayer plus tard.";
    }
    
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de Stage</title>
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
        <h2>Demande de Stage</h2>
        <form method="POST">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut" required>

            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin" required>

            <label for="domaine">Domaine du stage:</label>
            <input type="text" id="domaine" name="domaine" required>

            <button type="submit">Envoyer la demande</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 ISGB - Tous droits réservés</p>
    </footer>
</body>
</html>
