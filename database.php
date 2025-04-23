<?php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_documents", "root", "Mysql123a");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);