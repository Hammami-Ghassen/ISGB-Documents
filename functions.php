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






?>


