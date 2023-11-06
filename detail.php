<?php
if (empty($_GET)) {
    header('Location: index.php');
    exit();
};
include "header.php";

require '_connec.php';
$pdo = new \PDO(DSN, USER, PASS);
$query = "SELECT title, lastname, firstname, price, `resume`, GROUP_CONCAT(`category` SEPARATOR ', ') as genre, `date` FROM `book` INNER JOIN book_author ON book.idbook=book_author.idbook
INNER JOIN author ON author.idauthor = book_author.idauthor
INNER JOIN book_category ON book_category.idbook = book.idbook
INNER JOIN category ON category.idcategory = book_category.idcategory WHERE book.idbook = " . $_GET['identifiant'] . " GROUP BY title, lastname, firstname, price , `resume`, `date` ";
$statement = $pdo->query($query);
$book = $statement->fetchAll(PDO::FETCH_ASSOC);

// if id unvalid, back to index.php
if (empty($book)) {
    header('Location: index.php');
    exit();
};

echo 'Vous êtes sur la page détail du livre "' . $book[0]['title'] . '".<br/><br/>';
echo "L'auteur de ce livre est " . $book[0]['firstname'] . " " . $book[0]['lastname'] . '.<br/><br/>';
echo "Résumé : <br/>" . $book[0]['resume'] . '<br/><br/>';
echo "Ce livre appartient aux genres : " . $book[0]['genre'] . '.<br/><br/>';
echo "Date de publication : " . $book[0]['date'] . '.<br/>';
echo "Le prix de ce livre est " . $book[0]['price'] . '€.<br/>';
?>