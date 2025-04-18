<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Initialiser une variable pour afficher un message de succès
$demande_succes = false;

// Simuler un envoi de formulaire réussi (vous pouvez modifier cette logique selon vos besoins)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ici, vous pouvez ajouter la logique pour enregistrer la demande dans la base de données
    $demande_succes = true; // Simuler que la demande a été ajoutée avec succès
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande d'Attestation de Présence</title>
    <link rel="stylesheet" href="Presence.css">
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

    <section class="hero">
        <div class="hero-content">
            <h1>Demande d'Attestation de Présence</h1>
            <p>Complétez le formulaire ci-dessous pour soumettre votre demande.</p>
        </div>
    </section>

    <section class="section">
        <h2>Formulaire de Demande</h2>
        <form action="" method="POST">
            <label for="nom">Nom Complet :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="cin">Numéro CIN :</label>
            <input type="text" id="cin" name="cin" required>

            <label for="classe">Classe :</label>
            <input type="text" id="classe" name="classe" required>

            <label for="date_presence">Date de Présence :</label>
            <input type="date" id="date_presence" name="date_presence" required>

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
