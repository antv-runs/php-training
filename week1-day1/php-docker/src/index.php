<?php

$pdo = new PDO("mysql:host=db;dbname=demo", "root", "root");

echo "<h1>PHP Docker App Runningocker App Runniocker App Runni 🚀 hihi chào nhé</h1>";

$stmt = $pdo->query("SELECT NOW() as time");
$row = $stmt->fetch();

echo "<p>Database time: " . $row['time'] . "</p>";
