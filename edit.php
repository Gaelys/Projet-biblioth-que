<?php
if (empty($_GET)) {
    header('Location: index.php');
    exit();
};
include "header.php";

require '_connec.php';
$pdo = new \PDO(DSN, USER, PASS);
//update les valeurs
if (!empty($_POST)) {
   
    $title = $_POST['title'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $firstname = $_POST['firstname'];
    $resume = $_POST['resume'];
    $price = $_POST['price'];
    $categories = isset($_POST["category"]) 
        ? $_POST["category"]
        :[];
    $date = $_POST['date'];
    $idbook = $_GET['identifiant'];

    
    try {  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();
    $query = "UPDATE book SET title = :title, `resume` = :resume, `date` = :date, price = :price WHERE idbook = :identifiant";
    $statement = $pdo->prepare($query);
    $statement ->bindValue(':title', $title, \PDO::PARAM_STR);
    $statement ->bindValue(':resume', $resume, \PDO::PARAM_STR);
    $statement ->bindValue(':date', $date, PDO::PARAM_STR);
    $statement ->bindValue(':price', $price, PDO::PARAM_STR);
    $statement ->bindValue(':identifiant', $idbook, \PDO::PARAM_INT);
    $statement ->execute();
    $query2 = "UPDATE author SET lastname = :lastname, firstname = :firstname";
    $statement2 = $pdo ->prepare($query2);
    $statement2 ->bindValue(':lastname', $lastname, \PDO::PARAM_STR);
    $statement2 ->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
    $statement2 ->execute();
    $idauthor = $pdo->lastInsertId();
    foreach ($categories as $category) {
        $query3 = "UPDATE book_category SET idbook = :idbook, idcategory = :idcategory";
        $statement3 = $pdo ->prepare($query3);
        $statement3 ->bindValue(':idbook', $idbook, \PDO::PARAM_INT);
        $statement3 ->bindValue(':idcategory', $category, \PDO::PARAM_INT);
        $statement3 -> execute();
    };
    $query4 ="UPDATE book_author SET idbook = :idbook, idauthor = :idauthor";
    $statement4 = $pdo ->prepare($query4);
    $statement4 ->bindValue(':idbook', $idbook, \PDO::PARAM_INT);
    $statement4 ->bindValue(':idauthor', $idauthor, \PDO::PARAM_INT);
    $statement4 -> execute();
    $pdo->commit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
    };
};

// affiche les infos du livre
$query5 = "SELECT title, lastname, firstname, price, `resume`, GROUP_CONCAT(`category` SEPARATOR ', ') as genre, `date` FROM `book` INNER JOIN book_author ON book.idbook=book_author.idbook
INNER JOIN author ON author.idauthor = book_author.idauthor
INNER JOIN book_category ON book_category.idbook = book.idbook
INNER JOIN category ON category.idcategory = book_category.idcategory WHERE book.idbook = " . $_GET['identifiant'] . " GROUP BY title, lastname, firstname, price , `resume`, `date` ";
$statement5 = $pdo->query($query5);
$book = $statement5->fetchAll(PDO::FETCH_ASSOC);

echo '<h2>Modifier les informations du livre "' . $book[0]['title'] . '".</h2><br/<br/>';


echo '<form method="post">';
echo '<table>';

echo '<tr><th>Titre</th><td><input typr="text" name="title" value="' . $book[0]['title'] . '"></td></tr>';
echo '<tr><th>Nom de l\'auteur</th><td><input type="text" name="lastname" value="' . $book[0]['lastname'] . '"/></td></tr>';
echo '<tr><th>Prénom de l\'auteur</th><td><input type="text" name="firstname" value="' . $book[0]['firstname'] . '"/></td></tr>';
echo '<tr><th>Résumé</th><td><textarea rows="6" cols="40" id="resume" name="resume" >' . $book[0]['resume'] . '</textarea></td></tr>';
$query6 = "SELECT * FROM `category`";
$statement6 = $pdo->query($query6);
$categories = $statement6->fetchAll(PDO::FETCH_ASSOC);
echo '<tr>';
foreach ($categories as $category) {
    echo '<td><label for="categoty[]">' . $category['category'] . '</label><input name="category[]" type="checkbox" id="' . $category['idcategory'] . '"value="' . $category['idcategory'] . '" /></td>';
};
echo '</tr>';
echo '<tr><th>Date d\'édition</th><td><input type="date" name="date" value="' . $book[0]['date'] . '"/></td></tr>';
echo '<tr><th>Prix</th><td><input type="number" step="0.01" name="price" value="' . $book[0]['price'] . '"/></td></tr>';
echo '<tr><td><input type="submit" value="Enregistrer"></td></tr>';
echo '</table>';
echo '</form>';
?>