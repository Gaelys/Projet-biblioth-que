<?php
include "header.php";

require '_connec.php';
$pdo = new \PDO(DSN, USER, PASS);

if (!empty($_POST)) {
    var_dump($_POST);
    $title = $_POST['title'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $price = $_POST['price'];
    $resume = $_POST['resume'];
    $categories = isset($_POST["category"]) ?
    $_POST["category"]:[];
    $date = $_POST['date'];


    
    try {  
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        $query = 'INSERT INTO book (title, `resume`, `date`, price) VALUES (:title, :resume, :date, :price)';
        $statement =  $pdo ->prepare($query);
        $statement ->bindValue(':title', $title, \PDO::PARAM_STR);
        $statement ->bindValue(':resume', $resume, \PDO::PARAM_STR);
        $statement ->bindValue(':date', $date, PDO::PARAM_STR);
        $statement ->bindValue(':price', $price, PDO::PARAM_STR);
        $statement ->execute();
        $idbook = $pdo->lastInsertId();
        $query2 = "INSERT INTO author (lastname, firstname) VALUES (:lastname, :firstname)";
        $statement2 = $pdo ->prepare($query2);
        $statement2 ->bindValue(':lastname', $lastname, \PDO::PARAM_STR);
        $statement2 ->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
        $statement2 ->execute();
        $idauthor = $pdo->lastInsertId();
        foreach ($categories as $category) {
            $query3 = "INSERT INTO book_category (idbook, idcategory) VALUES (:idbook, :idcategory)";
            $statement3 = $pdo ->prepare($query3);
            $statement3 ->bindValue(':idbook', $idbook, \PDO::PARAM_INT);
            $statement3 ->bindValue(':idcategory', $category, \PDO::PARAM_STR);
            $statement3 -> execute();
        };
        $query4 ="INSERT INTO book_author (idbook, idauthor) VALUES (:idbook, :idauthor)";
        $statement4 = $pdo ->prepare($query4);
        $statement4 ->bindValue(':idbook', $idbook, \PDO::PARAM_INT);
        $statement4 ->bindValue(':idauthor', $idauthor, \PDO::PARAM_INT);
        $statement4 -> execute();
        $pdo->commit();
        
      
        } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
        };
header('Location: /index.php');
die;
};

echo "Ajouter un livre à la bibliothèque.<br/>";
echo '<form method="post">';
echo '<table>';
echo '<tr><th>Titre</th><td><input type="text" maxlength="45" name="title" value=""/></td></tr>';
echo '<tr><th>Nom de l\'auteur</th><td><input type="text" maxlength="45" name="lastname" value=""/></td></tr>';
echo '<tr><th>Prénom de l\'auteur</th><td><input type="text" maxlength="45"  name="firstname" value=""/></td></tr>';
echo '<tr><th>Résumé</th><td><textarea name="resume" maxlength="1500" rows="5" cols="15" value=""></textarea></td></tr>';
$query2 = "SELECT * FROM `category`";
$statement1 = $pdo->query($query2);
$categories = $statement1->fetchAll(PDO::FETCH_ASSOC);
echo '<tr>';
foreach ($categories as $category) {
    echo '<td><label for="category[]">' . $category['category'] . '</label><input name="category[]" type="checkbox" id="' . $category['idcategory'] . '"value="' . $category['idcategory'] . '"/></td>';
};
echo '</tr>';
echo '<tr><th>Date d\'édition</th><td><input type="date" name="date" value=""/></td></tr>';
echo '<tr><th>Prix</th><td><input type="number" step="0.01" name="price" value=""/></td></tr>';
echo '<tr><td><input type="submit" value="Enregistrer"></td></tr>';
echo '</table>';

?>