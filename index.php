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
    <style>
    /* RESET & BASE */
    * {
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    body {
      margin: 0;
      padding: 0;
      color: #333;
      background-color: #f9f9f9;
      line-height: 1.6;
    }
    a {
      text-decoration: none;
    }

    /* LAYOUT */
    .container, .suivi-container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 0;
    }
    .suivi-title {
      text-align: center;
      color: #004080;
      margin-bottom: 30px;
      font-size: 2em;
    }

    /* TABLE */
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      margin-top: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #004080;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }

    /* STATUS BADGES */
    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.9em;
      font-weight: bold;
      display: inline-block;
    }
    .en-attente { background-color: #FFF3CD; color: #856404; }
    .approuve { background-color: #D4EDDA; color: #155724; }
    .refuse { background-color: #F8D7DA; color: #721C24; }

    /* BUTTONS */
    .btn {
      padding: 8px 15px;
      border-radius: 4px;
      font-weight: bold;
      color: white;
      cursor: pointer;
      margin-right: 5px;
    }
    .btn-download { background-color: #28a745; }
    .btn-details { background-color: #004080; }
    .btn:hover { opacity: 0.9; }

    /* ERROR MESSAGE */
    .error-message {
      background-color: #F8D7DA;
      color: #721C24;
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
      display: none;
      text-align: center;
    }
    .retry-btn {
      background-color: #dc3545;
      border: none;
      padding: 5px 10px;
      margin-top: 10px;
      border-radius: 4px;
      cursor: pointer;
      color: white;
    }

    /* MODAL */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      overflow-y: auto;
    }
    .modal-content {
      background: white;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 600px;
      position: relative;
    }
    .close {
      position: absolute;
      right: 20px;
      top: 10px;
      font-size: 28px;
      color: #aaa;
      cursor: pointer;
    }
    .close:hover { color: #333; }

    .detail-item {
      margin-bottom: 15px;
    }
    .detail-label {
      font-weight: bold;
      color: #004080;
      display: block;
      margin-bottom: 5px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="logo">Faculté ISGB</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
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

    <section class="section">
      <div class="suivi-container">
        <h2 class="suivi-title">Historique de vos demandes</h2>
        <table>
          <thead>
            <tr>
              <th>Type de demande</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php getDemandesByUser($_SESSION['user']['id_utilisateur']); ?>
          </tbody>
        </table>

        <!-- Modal pour le détail motif -->
        <div id="detailsModal" class="modal">
          <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Détails de la demande</h3>
            <div class="detail-item">
              <span class="detail-label">Type de demande :</span>
              <span id="detail-type">-</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Date de demande :</span>
              <span id="detail-date">-</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Statut :</span>
              <span id="detail-status" class="status">-</span>
            </div>
            <div class="detail-item" id="motif-section" style="display: none;">
              <span class="detail-label">Motif de refus :</span>
              <span id="detail-motif">-</span>
            </div>
          </div>
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

<script>
function showMotif(motif, type, date) {
    // Affiche le modal
    document.getElementById('detailsModal').style.display = 'block';

    // Remplit uniquement le motif, le type et la date
    document.getElementById('motif-section').style.display = 'block';
    document.getElementById('detail-motif').textContent   = motif;
    document.getElementById('detail-type').textContent    = type;
    document.getElementById('detail-date').textContent    = date;

    // Mets à jour le statut en "Refusé"
    const statusEl = document.getElementById('detail-status');
    statusEl.textContent = 'Refusé';
    statusEl.className   = 'status refuse';
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}
</script>
</body>
</html>
