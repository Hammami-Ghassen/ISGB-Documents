<!-- attestation_inscription.php -->
<?php
include 'functions.php';
session_start();

if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traitement de la demande d'attestation
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $date_demande = $_POST['date_demande'];
    
    // Sauvegarder ou envoyer la demande (exemple simple)
    echo "<p>Demande d'attestation envoyée avec succès.</p>";
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
    <div class="container">
        <h2>Demande d'Attestation d'Inscription</h2>
        <form method="POST">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="date_demande">Date de la demande:</label>
            <input type="date" id="date_demande" name="date_demande" required>

            <button type="submit">Demander l'attestation</button>
        </form>
    </div>
</body>
</html>
