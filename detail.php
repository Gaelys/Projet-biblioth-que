<?php
$title = "Détails";
if ((empty($_GET)) || (is_string($_GET['identifiant']) && !is_numeric($_GET['identifiant']))) {
    header('Location: index.php');
    exit();
}

include "header.php";
$books = booklist();
if (!in_array($_GET['identifiant'],$books)) {
    header('Location: index.php');
    exit();
}
$idbook = $_GET['identifiant'];


$query = "SELECT title, lastname, firstname, price, `resume`, GROUP_CONCAT(`category` SEPARATOR ', ') as genre, `date` FROM `book`
INNER JOIN author ON book.idauthor = author.idauthor
INNER JOIN book_category ON book_category.idbook = book.idbook
INNER JOIN category ON category.idcategory = book_category.idcategory WHERE book.idbook = " . $_GET['identifiant'] . " GROUP BY title, lastname, firstname, price , `resume`, `date` ";
$statement = $pdo->query($query);
$book = $statement->fetchAll(PDO::FETCH_ASSOC);
// if id unvalid, back to index.php
if (empty($book)) {
    header('Location: index.php');
    exit();
}

echo 'Vous êtes sur la page détail du livre <strong>"' . $book[0]['title'] . '"</strong>.<a class="button" href="edit.php?identifiant=' . $idbook . '">Modifier</a>  <a class="button" href="delete.php?identifiant=' . $idbook . '">Supprimer le livre</a><br/><br/>';
echo "L'auteur de ce livre est <strong>\"" . $book[0]['firstname'] . " " . $book[0]['lastname'] . "\"</strong>.<br/><br/>";
if (!empty($book[0]['resume'])) {
    echo "<p>Résumé : </p>" . $book[0]['resume'] . '<br/><br/>';
} else {
    echo '<p>Résumé : </p>non renseigné.<br/><br/>';
}
echo "Ce livre appartient aux genres : " . $book[0]['genre'] . '.<br/><br/>';
$dateObj = new DateTime($book[0]['date']);
$dateAff = new DateTime($book[0]['date']);
$dayMonth = $dateObj->format('m-d');
$year =$dateAff->format('Y');
if ($book[0]['date'] === "1000-01-01") {
    echo "Date de parution non renseignée.<br/>";
} else if ($dayMonth === '01-01') {
    echo "Date de parution: " . $year . ".<br/>";
} else {
    echo "Date de parution : " . $book[0]['date'] . '.<br/>';
}


if ($book[0]['price'] != 0) {
    echo 'Le prix de ce livre est ' . $book[0]['price'] . '€.<br/>';
}

include "footer.php";
?>