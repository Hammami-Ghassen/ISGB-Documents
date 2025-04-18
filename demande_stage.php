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
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $domaine = $_POST['domaine'];
    
    // Sauvegarder ou envoyer la demande (exemple simple)
    echo "<p>Demande de stage envoyée avec succès. Nous vous contacterons bientôt.</p>";
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
    <div class="container">
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
</body>
</html>
