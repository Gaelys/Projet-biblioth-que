
<?php
$title = "Accueil";
if (isset($_GET['keyword']) && empty($_GET['keyword'])) {
    header ('Location: index.php');
}
include "header.php";
$query = "SELECT book.idbook, title, lastname, firstname FROM `book` JOIN author ON book.idauthor = author.idauthor ORDER BY title;";
$statement = $pdo->query($query);
$books = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="get">
    <label>Recherche : </label>
    <input type="text" name="keyword" placeholder="
    <?php
    if (isset($_GET['keyword'])) {
        echo $_GET['keyword'];
    } else {
    echo "entrer un mot clé";
    }
    ?>
    "/>
    <input type="submit" value="Filtrer"/>
</form>
<?php

if (isset($_GET['keyword'])) {
    echo 'Votre mot-clé est : <strong>' . $_GET['keyword'] . '</strong>.<br/><br/>';
    $keyword = strtolower($_GET['keyword']);
    $filter = array_filter($books, function ($book) use ($keyword) {
        return ( strpos(strtolower($book['title']), $keyword) !== false ||
                 strpos(strtolower($book['lastname']), $keyword) !== false ||
                 strpos(strtolower($book['firstname']), $keyword) !== false);
    });
    $booksFilter = array_values(array_unique($filter, SORT_REGULAR));
    echo 'Il y a ' . count($booksFilter) . ' livres correspondant à votre recherche.<br/>' ;
} else {
    $booksFilter = $books;
}
echo 'Il y a ' . count($books) . " livres dans cette bibliothèque.<br/></br>";
echo '<a href="add.php">Ajouter un livre</a><br/><br/>';
echo '<table class="maintable">';
echo '<tr><th>Titre</th><th>Nom</th><th>Prénom</th><th>détails</th><th>Modifier</th><th>Supprimer</th></tr>';
foreach ($booksFilter as $book) {
    echo '<tr>
    <td>' . $book['title'] . '</td>
    <td>' . $book['lastname'] . '</td>
    <td>' . $book['firstname'] . '</td>
    <td><a href="detail.php?identifiant=' . $book["idbook"] . '">Détails</a></td>
    <td><a href="edit.php?identifiant=' . $book["idbook"] . '">Modifier</a></td>
    </td><td><a href="delete.php?identifiant=' . $book["idbook"] . '">supprimer</a></td></tr>';
}
echo '</table>';


include "footer.php";
?>
