<?php
include 'database.php';
function login($email, $password){
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email AND mot_de_passe = :password");
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        return $user;
    }
    return false;
}
/**
 * Soumet une demande d'attestation de présence et renvoie l'identifiant de suivi,
 * ou false en cas d'échec.
 *
 * @param int   $id_utilisateur    Identifiant de l'utilisateur
 * @param array $donnees_formulaire Données issues du POST
 * @return string|false
 */
function soumettreDemandePresence($id_utilisateur, array $donnees_formulaire)
{
    global $pdo;

    try {
        // Démarrage de la transaction
        $pdo->beginTransaction();

        // 1. Insertion de la demande principale
        $type_document    = "attestation de présence";
        // uniqid avec plus d'entropie pour minimiser les collisions
        $identifiant_suivi = uniqid("dem_", true);

        $sqlDemande = "
            INSERT INTO Demande (id_utilisateur, type_document, identifiant_suivi)
            VALUES (:id_user, :type_doc, :identifiant)
        ";
        $stmt = $pdo->prepare($sqlDemande);
        $stmt->execute([
            ':id_user'     => $id_utilisateur,
            ':type_doc'    => $type_document,
            ':identifiant' => $identifiant_suivi
        ]);
        $id_demande = $pdo->lastInsertId();

        // 2. Préparation unique de l'insertion des détails
        $sqlDetails = "
            INSERT INTO DemandeDetails (id_demande, champ_nom, champ_valeur)
            VALUES (:id_demande, :nom, :valeur)
        ";
        $stmtDetails = $pdo->prepare($sqlDetails);

        $champs = ['nom', 'cin', 'classe', 'motif'];
        foreach ($champs as $champ) {
            if (!empty($donnees_formulaire[$champ])) {
                // Sanitize minimal
                $valeur = htmlspecialchars(trim($donnees_formulaire[$champ]), ENT_QUOTES, 'UTF-8');
                $stmtDetails->execute([
                    ':id_demande' => $id_demande,
                    ':nom'        => $champ,
                    ':valeur'     => $valeur
                ]);
            }
        }

        // Commit si tout s'est bien passé
        $pdo->commit();

        return $identifiant_suivi;

    } catch (PDOException $e) {
        // Annulation de la transaction et log de l'erreur
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Erreur PDO dans soumettreDemandePresence : " . $e->getMessage());
        return false;
    }
}
function soumettreDemandeInscription($id_utilisateur, array $donnees_formulaire)
{
    global $pdo;

    try {
        // Démarrage de la transaction
        $pdo->beginTransaction();

        // 1. Insertion de la demande principale
        $type_document     = "attestation d'inscription";
        // uniqid avec entropie pour éviter les collisions
        $identifiant_suivi = uniqid("dem_ins_", true);

        $sqlDemande = "
            INSERT INTO Demande (id_utilisateur, type_document, identifiant_suivi)
            VALUES (:id_user, :type_doc, :identifiant)
        ";
        $stmt = $pdo->prepare($sqlDemande);
        $stmt->execute([
            ':id_user'     => $id_utilisateur,
            ':type_doc'    => $type_document,
            ':identifiant' => $identifiant_suivi
        ]);
        $id_demande = $pdo->lastInsertId();

        // 2. Préparation unique de l'insertion des détails
        $sqlDetails = "
            INSERT INTO DemandeDetails (id_demande, champ_nom, champ_valeur)
            VALUES (:id_demande, :nom, :valeur)
        ";
        $stmtDetails = $pdo->prepare($sqlDetails);

        // Champs à enregistrer
        $champs = ['nom', 'prenom', 'email'];
        foreach ($champs as $champ) {
            if (!empty($donnees_formulaire[$champ])) {
                // Nettoyage minimal
                $valeur = htmlspecialchars(trim($donnees_formulaire[$champ]), ENT_QUOTES, 'UTF-8');
                $stmtDetails->execute([
                    ':id_demande' => $id_demande,
                    ':nom'        => $champ,
                    ':valeur'     => $valeur
                ]);
            }
        }

        // Tout est OK → commit
        $pdo->commit();

        return $identifiant_suivi;

    } catch (PDOException $e) {
        // En cas d’erreur, rollback et journalisation
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Erreur PDO dans soumettreDemandeInscription : " . $e->getMessage());
        return false;
    }
}

function soumettreDemandeReussite($id_utilisateur, array $donnees_formulaire)
{
    global $pdo;
    try {
        $pdo->beginTransaction();

        // 1. Création de la demande principale
        $type_document     = "attestation d'inscription";
        $identifiant_suivi = uniqid("dem_ins_", true);

        $stmt = $pdo->prepare("
            INSERT INTO Demande (id_utilisateur, type_document, identifiant_suivi)
            VALUES (:id_user, :type_doc, :identifiant)
        ");
        $stmt->execute([
            ':id_user'     => $id_utilisateur,
            ':type_doc'    => $type_document,
            ':identifiant' => $identifiant_suivi,
        ]);
        $id_demande = $pdo->lastInsertId();

        // 2. Insertion des détails (nom, prénom, email)
        $stmtD = $pdo->prepare("
            INSERT INTO DemandeDetails (id_demande, champ_nom, champ_valeur)
            VALUES (:id_demande, :nom, :valeur)
        ");
        foreach (['nom', 'prenom', 'email'] as $champ) {
            if (!empty($donnees_formulaire[$champ])) {
                $valeur = htmlspecialchars(trim($donnees_formulaire[$champ]), ENT_QUOTES, 'UTF-8');
                $stmtD->execute([
                    ':id_demande' => $id_demande,
                    ':nom'        => $champ,
                    ':valeur'     => $valeur,
                ]);
            }
        }

        $pdo->commit();
        return $identifiant_suivi;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Erreur PDO soumettreDemandeInscription : " . $e->getMessage());
        return false;
    }
}


function afficherDemandes()
{
    global $pdo;

    $sql = "
        SELECT d.id_demande, d.type_document, DATE(d.date_soumission) as date_soumission,
               d.identifiant_suivi, u.nom, u.prenom
        FROM demande d
        LEFT JOIN utilisateur u ON d.id_utilisateur = u.id_utilisateur
        WHERE d.id_demande NOT IN (
            SELECT id_demande FROM historiqueaction
        )
        ORDER BY d.date_soumission DESC
    ";

    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id_demande = htmlspecialchars($row['id_demande']);
        $nom_complet = htmlspecialchars($row['prenom'] . ' ' . $row['nom']);
        $type_document = htmlspecialchars($row['type_document']);
        $date_soumission = htmlspecialchars($row['date_soumission']);

        echo "<tr>
                <td>$nom_complet</td>
                <td>$type_document</td>
                <td>$date_soumission</td>
                <td><a href='#' target='_blank'>Voir</a></td>
                <td>
                    <form method='post' action='' style='display:inline-block'>
                        <input type='hidden' name='id_demande' value='$id_demande'>
                        <input type='hidden' name='action' value='approuver'>
                        <button type='button' onclick='openModal(\"approuver\", this.form)' class='btn btn-approve'>Approuver</button>
                    </form>
                    <form method='post' action='' style='display:inline-block'>
                        <input type='hidden' name='id_demande' value='$id_demande'>
                        <input type='hidden' name='action' value='rejeter'>
                        <input type='text' name='commentaire' placeholder='Motif du rejet' required>
                        <button type='button' onclick='openModal(\"rejeter\", this.form)' class='btn btn-reject'>Rejeter</button>
                    </form>
                </td>
            </tr>";
    }
}




function afficherAccepte()
{
    global $pdo;

    $sql = "
        SELECT u.prenom, u.nom, d.type_document, DATE(h.date_action) as date_action
        FROM historiqueaction h
        JOIN demande d ON h.id_demande = d.id_demande
        JOIN utilisateur u ON d.id_utilisateur = u.id_utilisateur
        WHERE h.action = 'acceptée'
        ORDER BY h.date_action DESC
    ";

    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nom_complet = htmlspecialchars($row['prenom'] . ' ' . $row['nom']);
        $type_document = htmlspecialchars($row['type_document']);
        $date_action = htmlspecialchars($row['date_action']);

        echo "<tr>
                <td>$nom_complet</td>
                <td>$type_document</td>
                <td>$date_action</td>
                <td><a href='#' target='_blank'>Voir</a></td>
              </tr>";
    }
}



function afficherRejete()
{
    global $pdo;

    $sql = "
        SELECT u.prenom, u.nom, d.type_document, DATE(h.date_action) as date_action, h.commentaire
        FROM historiqueaction h
        JOIN demande d ON h.id_demande = d.id_demande
        JOIN utilisateur u ON d.id_utilisateur = u.id_utilisateur
        WHERE h.action = 'rejetée'
        ORDER BY h.date_action DESC
    ";

    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nom_complet = htmlspecialchars($row['prenom'] . ' ' . $row['nom']);
        $type_document = htmlspecialchars($row['type_document']);
        $date_action = htmlspecialchars($row['date_action']);
        $commentaire = htmlspecialchars($row['commentaire']);

        echo "<tr>
                <td>$nom_complet</td>
                <td>$type_document</td>
                <td>$date_action</td>
                <td>$commentaire</td>
              </tr>";
    }
}


function afficherStats()
{
    global $pdo;

    // Requête pour compter les demandes en attente (pas encore dans historiqueaction)
    $sqlAttente = "
        SELECT COUNT(*) AS total
        FROM demande
        WHERE id_demande NOT IN (
            SELECT id_demande FROM historiqueaction
        )
    ";
    $stmtAttente = $pdo->query($sqlAttente);
    $attente = $stmtAttente->fetch(PDO::FETCH_ASSOC)['total'];

    // Requête pour compter les demandes acceptées
    $sqlAcceptees = "
        SELECT COUNT(*) AS total
        FROM historiqueaction
        WHERE action = 'acceptée'
    ";
    $stmtAcceptees = $pdo->query($sqlAcceptees);
    $acceptees = $stmtAcceptees->fetch(PDO::FETCH_ASSOC)['total'];

    // Requête pour compter les demandes rejetées
    $sqlRejetees = "
        SELECT COUNT(*) AS total
        FROM historiqueaction
        WHERE action = 'rejetée'
    ";
    $stmtRejetees = $pdo->query($sqlRejetees);
    $rejetees = $stmtRejetees->fetch(PDO::FETCH_ASSOC)['total'];

    // Affichage HTML
    echo "
    <div class='stats'>
        <div class='stat-box'>
            <h3>Demandes en attente</h3>
            <p>$attente</p>
        </div>
        <div class='stat-box'>
            <h3>Documents approuvés</h3>
            <p>$acceptees</p>
        </div>
        <div class='stat-box'>
            <h3>Documents rejetés</h3>
            <p>$rejetees</p>
        </div>
    </div>
    ";
}


//traitement du demande


function traiterDemande($id_demande, $action, $id_admin, $commentaire = null)
{
    global $pdo;

    // Valider l'action
    if (!in_array($action, ['approuver', 'rejeter'])) {
        return false;
    }

    // Convertir l'action en valeur pour la base
    $etat = ($action === 'approuver') ? 'acceptée' : 'rejetée';

    try {
        $pdo->beginTransaction();

        // 1. Insérer dans historiqueaction
        $sql = "
            INSERT INTO historiqueaction (id_admin, id_demande, action, commentaire)
            VALUES (:id_admin, :id_demande, :action, :commentaire)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_admin'    => $id_admin,
            ':id_demande'  => $id_demande,
            ':action'      => $etat,
            ':commentaire' => $commentaire
        ]);

        // 2. Mettre à jour la table Demande avec les infos de traitement
        $sqlUpdate = "
            UPDATE demande
            SET id_admin_traitant = :id_admin, commentaire_admin = :commentaire
            WHERE id_demande = :id_demande
        ";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':id_admin'    => $id_admin,
            ':commentaire' => $commentaire,
            ':id_demande'  => $id_demande
        ]);

        $pdo->commit();
        return true;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Erreur dans traiterDemande : " . $e->getMessage());
        return false;
    }
}



?>


