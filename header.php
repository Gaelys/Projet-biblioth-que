<!DOCTYPE html>
<html lang="fr">
        <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo $title; ?></title>
                <link rel="stylesheet"  href="styles.css"/>
        </head>
        <body>
                        
                <h1>Bibliothèque de livre</h1>
                <nav>
                        <a href="index.php">Page d'accueil</a> -
                        <a href="add.php"> Ajout d'un livre</a>
                </nav>
                <hr>
                <?php
                include "function.php";
                $pdo = linkToDb();
                ?>