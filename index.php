
<?php
include "header.php";

require_once '_connec.php';
$pdo = new \PDO(DSN, USER, PASS);
$query = "SELECT book.idbook, title, lastname, firstname FROM `book` JOIN book_author ON book.idbook = book_author.idbook JOIN author ON author.idauthor = book_author.idauthor;";
$statement = $pdo->query($query);
$books = $statement->fetchAll(PDO::FETCH_ASSOC);

var_dump($books);

echo 'Il y a ' . count($books) . " livres<br/><br/>";
echo '<a href="add.php">Ajouter un livre</a><br/>';
echo '<table>';
echo '<tr><th>Titre</th><th>Nom</th><th>Prénom</th><th>détails</th><th>Modifier</th><th>Supprimer</th></tr>';
foreach ($books as $book) {
    echo '<tr>
    <td>' . $book['title'] . '</td>
    <td>' . $book['lastname'] . '</td>
    <td>' . $book['firstname'] . '</td>
    <td><a href="detail.php?identifiant=' . $book["idbook"] . '">Détails</a></td>
    <td><a href="edit.php?identifiant=' . $book["idbook"] . '">Modifier</a></td>
    </td><td><a href="delete.php?identifiant=' . $book["idbook"] . '">supprimer</a></td></tr><br/>';
};
echo '</table>';


include "footer.php";
?>
