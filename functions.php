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


    return false;}




