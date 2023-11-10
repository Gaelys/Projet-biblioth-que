<?php
$title = "Ajout";
include "header.php";

if (!empty($_POST)) {
    $error = 0;
    if ((empty($_POST['title'])) || (mb_strlen($_POST['title']) > 45)) {
        echo "Vous devez entrer un nom de livre de moins de 45 caractères.<br/>";
        $error ++;
    }
    if (mb_strlen($_POST['lastname']) > 45) {
        echo "Vous devez entrer un nom d'auteur de moins de 45 caractères.<br/>";
        $error ++;
    }
    if (mb_strlen($_POST['firstname']) > 45) {
        echo "Vous devez entrer un prénom d'auteur de moins de 45 caractères.<br/>";
        $error ++;
    }
    if ((empty($_POST['lastname'])) AND ((empty($_POST['firstname'])) ) ) {
        echo "Vous devez entrer un prénom ou un nom.<br/>";
        $error ++;
    }
    if (empty($_POST['category'])) {
        echo "Vous devez entrer au moins un genre.<br/>";
        $error ++;
    }
    if (isset($_POST['resume'])) {
        if (mb_strlen($_POST['resume']) > 1500) {
        echo "Votre résumé doit faire moins de 1500 caractères.<br/>";
        $error ++;
        }
    }
    $price = $_POST['price'];
    if (empty($_POST['price'])) {
        $price =0;
    }
    $price = intval($_POST['price']);
    if ($price < 0 ) {
        echo "Le prix doit être positif.<br/>";
         $error ++;
    }
    $currentDate = date('Y-m-d');
    if ( $_POST['date'] > $currentDate) {
        echo "La date n'est pas valide.<br/>";
        $error++;
    }

    if ($error === 0) {
        $title = trim(ucfirst($_POST['title']));
        $lastname = trim(ucfirst($_POST['lastname']));
        $firstname = trim(ucfirst($_POST['firstname']));
        
        $resume = $_POST['resume'];
        $categories = isset($_POST["category"]) ?
        $_POST["category"]:[];
        $date = $_POST['date'];
        $idauthor = 0;
        if (empty($_POST['date'])) {
            $date = "1000-01-01";
        }

        try {  
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();
            $datasAuths = $pdo->prepare("SELECT * FROM author");
            $datasAuths ->execute();
            $auths = $datasAuths->fetchAll(PDO::FETCH_ASSOC);
            $verif = true;
            foreach($auths as $auth){
                if($auth['lastname'] === $lastname && $auth['firstname'] === $firstname ){
                    $idauthor = $auth['idauthor'];
                    $verif=false;
                }
            }
            if($verif) {
                $queryCreatAuthor = "INSERT INTO author (lastname, firstname) VALUES (:lastname, :firstname)";
                $statementCreatAuthor = $pdo ->prepare($queryCreatAuthor);
                $statementCreatAuthor ->bindValue(':lastname', $lastname, \PDO::PARAM_STR);
                $statementCreatAuthor ->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
                $statementCreatAuthor ->execute();
                $idauthor = $pdo->lastInsertId();
            }
            
            $query = 'INSERT INTO book (title, `resume`, `date`, price, idauthor) VALUES (:title, :resume, :date, :price, :idauthor)';
            $statement =  $pdo ->prepare($query);
            $statement ->bindValue(':title', $title, \PDO::PARAM_STR);
            $statement ->bindValue(':resume', $resume, \PDO::PARAM_STR);
            $statement ->bindValue(':date', $date, PDO::PARAM_STR);
            $statement ->bindValue(':price', $price, PDO::PARAM_STR);
            $statement ->bindValue(':idauthor', $idauthor, \PDO::PARAM_INT);
            $statement ->execute();
            $idbook = $pdo->lastInsertId();
            foreach ($categories as $category) {
                $query3 = "INSERT INTO book_category (idbook, idcategory) VALUES (:idbook, :idcategory)";
                $statement3 = $pdo ->prepare($query3);
                $statement3 ->bindValue(':idbook', $idbook, \PDO::PARAM_INT);
                $statement3 ->bindValue(':idcategory', $category, \PDO::PARAM_INT);
                $statement3 -> execute();
            }
            $pdo->commit();
            
        
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "Failed: " . $e->getMessage();
                die;
            }
    header('Location: /index.php');
    die;
    }
}
echo "Ajouter un livre à la bibliothèque.<br/>";
echo '<form method="post">';
echo '<table>';
echo '<tr><th>Titre*</th><td><input type="text" maxlength="45" name="title" value="" required/></td></tr>';
echo '<tr><th>Nom de l\'auteur</th><td><input type="text" maxlength="45" name="lastname" value="" /></td></tr>';
echo '<tr><th>Prénom de l\'auteur</th><td><input type="text" maxlength="45"  name="firstname" value=""/></td></tr>';
echo '<tr><th>Résumé</th><td><textarea name="resume" maxlength="1500" rows="10" cols="70" value=""></textarea></td></tr>';
echo '<tr><td clospan ="3">Si vous connaissez uniquement l\'année, entrer 01 pour le jour et le mois.</td></tr>';
echo '<tr><th>Date de parution</th><td><input type="date" name="date" value=""/></td></tr>';
echo '<tr><th>Prix</th><td><input type="number" step="0.01" name="price" value=""/></td></tr>';
echo '</table>';
echo '<table class="categ">';
$query2 = "SELECT * FROM `category`";
$statement1 = $pdo->query($query2);
$categories = $statement1->fetchAll(PDO::FETCH_ASSOC);
echo '<tr><td colspan="3" >Il faut choisir au moins 1 genre.</td></tr>';
echo '<tr>';
$counter = 0;
foreach ($categories as $category) {
    if ($counter > 0 && $counter % 5 === 0) { 
        echo '</tr><tr>'; 
    }
    echo '<td><label for="category[]">' . $category['category'] . '</label><input name="category[]" type="checkbox" id="' . $category['idcategory'] . '"value="' . $category['idcategory'] . '"/></td>';
    $counter++;
}
echo '</tr>';
echo '<tr><td><input type="submit" value="Enregistrer"></td></tr>';
echo '</table>';
echo '</form>';

include "footer.php";
?>