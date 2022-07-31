<?php
session_start();

Include "library.php";


    /* Connect to DB */
    $pdo = connectDB();   
    
    //Delete the movie from the table
    $query="DELETE FROM `Movies` WHERE Mid=?";
    $stmt=$pdo->prepare($query);
    $stmt->execute([$_GET['id']]);

    //delete Movie from moviesOwned table
    $query="DELETE FROM `MoviesOwned` WHERE Mid=?";
    $stmt=$pdo->prepare($query);
    $stmt->execute([$_GET['id']]);

    //delete entries in movie availability table
    $query="DELETE FROM `MovieAvailabilty` WHERE Mid=?";
    $stmt=$pdo->prepare($query);
    $stmt->execute([$_GET['id']]);

    //delete entries in ActedIn table
    $query="DELETE FROM `ActedIn` WHERE Mid=?";
    $stmt=$pdo->prepare($query);
    $stmt->execute([$_GET['id']]);

    header("Location: ../index.php");
    exit();

?>
