<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set("display_errors", 1);

function getConnexion(): PDO {
    try {
        $host = getenv('MYSQLHOST') ?: 'localhost';
        $db   = getenv('MYSQLDATABASE') ?: 'grainray';
        $user = getenv('MYSQLUSER') ?: 'root';
        $pass = getenv('MYSQLPASSWORD') ?: '';
        $port = getenv('MYSQLPORT') ?: '3306';

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
        $connexion = new PDO($dsn, $user, $pass);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $connexion;
    } catch (PDOException $e) {
        exit("Erreur BDD : " . $e->getMessage());
    }
}
?>
