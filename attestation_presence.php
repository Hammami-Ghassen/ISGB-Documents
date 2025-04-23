<?php
session_start();
include 'functions.php';

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

//form attestation_presence
$demande_succes = false;

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification que l'utilisateur est bien connecté
    if (empty($_SESSION['user']['id_utilisateur'])) {
        // Par exemple : redirection vers la page de login
        header('Location: login.php');
        exit;
    }

    $id_utilisateur = $_SESSION['user']['id_utilisateur'];
    $identifiant    = soumettreDemandePresence($id_utilisateur, $_POST);

    if ($identifiant !== false) {
        $demande_succes = true;
        // Vous pouvez afficher ce message à l'utilisateur :
        // "Votre demande a bien été prise en compte. Votre numéro de suivi est : $identifiant"
    } else {
        $error_message = "Une erreur est survenue lors de l'envoi de votre demande. Veuillez réessayer plus tard.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande d'Attestation de Présence</title>
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


    <section class="form">
        <h2>Formulaire de Demande</h2>
        <form action="" method="POST">
            <label for="nom">Nom Complet :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="cin">Numéro CIN :</label>
            <input type="text" id="cin" name="cin" required>

            <label for="classe">Classe :</label>
            <input type="text" id="classe" name="classe" required>


            <label for="motif">Motif de la demande :</label>
            <textarea id="motif" name="motif" rows="4" required></textarea>

            <button type="submit">Soumettre la Demande</button>
        </form>
    </section>

    <?php if ($demande_succes): ?>
        <section class="section">
            <div class="success-message">
                <p>Votre demande d'attestation de présence a été ajoutée avec succès !</p>
            </div>
        </section>
    <?php endif; ?>

    <footer>
        <p>&copy; 2025 ISGB - Tous droits réservés</p>
    </footer>
</body>
</html>
