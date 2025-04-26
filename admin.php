<?php
include 'functions.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user'])||$_SESSION['user']['role']!="administrateur") {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_demande'], $_POST['action'])) {

    $id_demande = (int) $_POST['id_demande'];
    $action = $_POST['action'];
    $commentaire = $_POST['commentaire'] ?? null;
    $id_admin = $_SESSION['user']['id_utilisateur'] ?? null;

    if ($id_admin) {
        $result = traiterDemande($id_demande, $action, $id_admin, $commentaire);
        if ($result) {
            // Redirige pour éviter la resoumission
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            // Gérer l'erreur (optionnel : ajouter un message d'erreur dans $_SESSION)
        }
    }
}





?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Administrateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }

        header {
            background-color: #004080;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        .logout-button {
            background: #dc3545;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .dashboard {
            padding: 20px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            flex: 1 1 200px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #004080;
            color: white;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        .btn-approve {
            background-color: #28a745;
        }

        .btn-reject {
            background-color: #dc3545;
        }

        /* Modale de confirmation */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }

        .modal-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .modal-buttons {
            margin-top: 20px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
        }

        .modal-btn-approve {
            background-color: #28a745;
            color: white;
        }

        .modal-btn-cancel {
            background-color: #dc3545;
            color: white;
        }

        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
            }
        }
    </style>
    <script>
        function openModal(action, form) {
            // Met à jour le message de confirmation
            let message = (action === 'approuver')
                ? "Êtes-vous sûr de vouloir approuver cette demande ?"
                : "Êtes-vous sûr de vouloir rejeter cette demande ?";
            document.getElementById('modalMessage').textContent = message;

            // Ajoute ou met à jour un champ caché pour transmettre l'action au backend
            let actionInput = form.querySelector('input[name="action"]');
            if (!actionInput) {
                actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                form.appendChild(actionInput);
            }
            actionInput.value = action;

            // Affiche la modale
            const modal = document.getElementById('confirmationModal');
            modal.style.display = 'block';

            // Nettoyage ancien gestionnaire et ajout du nouveau
            const confirmBtn = document.getElementById('confirmAction');
            const cancelBtn = document.getElementById('cancelAction');

            // Cloner le bouton pour supprimer les anciens gestionnaires
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

            const newCancelBtn = cancelBtn.cloneNode(true);
            cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

            // Nouveau gestionnaire : soumettre le formulaire
            newConfirmBtn.onclick = function () {
                modal.style.display = 'none';
                form.submit();
            };

            // Fermer la modale
            newCancelBtn.onclick = function () {
                modal.style.display = 'none';
            };
        }
    </script>


</head>

<body>

    <header>
        <h1>Tableau de Bord Admin</h1>
        <form action="logout.php" method="post">
            <button class="logout-button">Déconnexion</button>
        </form>
    </header>

    <div class="dashboard">
        <?php afficherStats(); ?>


        <h2>Demandes en attente</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom Étudiant</th>
                    <th>Type de Demande</th>
                    <th>Date</th>
                    <th>Pièces jointes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php afficherDemandes(); ?>
                <!-- autres demandes -->
            </tbody>
        </table>
        <h2>Documents Approuvés</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom Étudiant</th>
                    <th>Type de Demande</th>
                    <th>Date</th>
                    <th>Pièces jointes</th>
                </tr>
            </thead>
            <tbody>
                <?php afficherAccepte(); ?>

                <!-- autres documents approuvés -->
            </tbody>
        </table>

        <h2>Documents Rejetés</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom Étudiant</th>
                    <th>Type de Demande</th>
                    <th>Date</th>
                    <th>Motif du Rejet</th>
                </tr>
            </thead>
            <tbody>
                <?php afficherRejete(); ?>
                <!-- autres documents rejetés -->
            </tbody>
        </table>
    </div>

    <!-- Modale de confirmation -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" id="modalMessage">
                <!-- Message dynamique -->
            </div>
            <div class="modal-buttons">
                <button id="confirmAction" class="modal-btn modal-btn-approve">Confirmer</button>
                <button id="cancelAction" class="modal-btn modal-btn-cancel">Annuler</button>
            </div>
        </div>
    </div>

</body>

</html>