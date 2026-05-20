<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set("display_errors", 1);

function getConnexion(): PDO {
    try {
        $host = getenv('PGHOST')     ?: 'localhost';
        $db   = getenv('PGDATABASE') ?: 'postgres';
        $user = getenv('PGUSER')     ?: 'postgres';
        $pass = getenv('PGPASSWORD') ?: '';
        $port = getenv('PGPORT')     ?: '5432';

        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
        $connexion = new PDO($dsn, $user, $pass);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $connexion->setAttribute(PDO::ATTR_CASE, PDO::CASE_UPPER);
        return $connexion;
    } catch (PDOException $e) {
        exit("Erreur BDD : " . $e->getMessage());
    }
}
?>
