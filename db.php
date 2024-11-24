<?php
// db.php
$host = 'fdb1032.awardspace.net';
$dbname = '4450245_lf';
$username = '4450245_lf';
$password = '9czftBRA3jys}dsq';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>