<?php
if(isset($_GET['username'])){
    $Username = $_GET['username'] ?? null;

    if (!$Username) {
        echo 'error';
        return;
    }

    require_once './includes/library.php';
    $pdo = connectDB();

    $statement = $pdo->prepare("SELECT username FROM `Users` WHERE username=?");
    $statement->execute([$Username]);

    if ($statement->fetch()) echo 'true';
    else echo 'false';
}
if(isset($_GET['Email'])){

    $email = $_GET['Email'] ?? null;

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'error';
        return;
    }

    require_once './includes/library.php';
    $pdo = connectDB();

    $statement = $pdo->prepare("SELECT Email FROM `Users` WHERE Email=?");
    $statement->execute([$email]);

    if ($statement->fetch()) echo 'true';
    else echo 'false';
}
?>