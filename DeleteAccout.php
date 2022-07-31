<?php
session_start();
include "includes/library.php";

$pdo=connectDB();

$query="DELETE FROM `MoviesOwned` WHERE username = ?" ;
$stmt=$pdo->prepare($query);
$stmt->execute([$_SESSION['username']]);

$query="DELETE FROM `Users` WHERE username = ?" ;
$stmt=$pdo->prepare($query);
$stmt->execute([$_SESSION['username']]);

header("Location: login.php");
exit();
?>