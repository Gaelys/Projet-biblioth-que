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
    $categories = isset($_POST["category"]) 
        ? $_POST["category"]
        :[];
    $date = $_POST['date'];

    $query = "UPDATE book SET title = :title, `resume` = :resume WHERE idbook = :identifiant";
    $statement = $pdo->prepare($query);
    $statement ->bindValue(':title', $title, \PDO::PARAM_STR);
    $statement ->bindValue(':resume', $resume, \PDO::PARAM_STR);
    $statement ->bindValue(':identifiant', $_GET['identifiant'], \PDO::PARAM_INT);
    $statement ->execute();
};

// affiche les infos du livre
$query2 = "SELECT title, lastname, firstname, price, `resume`, GROUP_CONCAT(`category` SEPARATOR ', ') as genre, `date` FROM `book` INNER JOIN book_author ON book.idbook=book_author.idbook
INNER JOIN author ON author.idauthor = book_author.idauthor
INNER JOIN book_category ON book_category.idbook = book.idbook
INNER JOIN category ON category.idcategory = book_category.idcategory WHERE book.idbook = " . $_GET['identifiant'] . " GROUP BY title, lastname, firstname, price , `resume`, `date` ";
$statement2 = $pdo->query($query2);
$book = $statement2->fetchAll(PDO::FETCH_ASSOC);

echo '<h2>Modifier les informations du livre "' . $book[0]['title'] . '".</h2><br/<br/>';


echo '<form method="post">';
echo '<table>';

echo '<tr><th>Titre</th><td><input typr="text" name="title" value="' . $book[0]['title'] . '"></td></tr>';
echo '<tr><th>Nom de l\'auteur</th><td><input type="text" name="lastname" value="' . $book[0]['lastname'] . '"/></td></tr>';
echo '<tr><th>Prénom de l\'auteur</th><td><input type="text" name="firstname" value="' . $book[0]['firstname'] . '"/></td></tr>';
echo '<tr><th>Résumé</th><td><textarea rows="6" cols="40" id="resume" name="resume" >' . $book[0]['resume'] . '</textarea></td></tr>';
$query3 = "SELECT * FROM `category`";
$statement3 = $pdo->query($query3);
$categories = $statement3->fetchAll(PDO::FETCH_ASSOC);
echo '<tr>';
foreach ($categories as $category) {
    echo '<td><label for="categoty[]">' . $category['category'] . '</label><input name="category[]" type="checkbox" id="' . $category['idcategory'] . '"value="' . $category['idcategory'] . '" /></td>';
};
echo '</tr>';
echo '<tr><th>Date d\'édition</th><td><input type="date" name="date" value="' . $book[0]['date'] . '"/></td></tr>';
echo '<tr><td><input type="submit" value="Enregistrer"></td></tr>';
echo '</table>';
echo '</form>';
?>