<?php
function test() {
    echo "test";
};

function linktodb() {
    require_once '_connec.php';
    return $pdo = new \PDO(DSN, USER, PASS);
};

function booklist($pdo) {
    $query = "SELECT book.idbook, title, lastname, firstname FROM `book` JOIN book_author ON book.idbook = book_author.idbook JOIN author ON author.idauthor = book_author.idauthor;";
    $statement = $pdo->query($query);
    return $books = $statement->fetchAll(PDO::FETCH_ASSOC);
};
?>





















((empty($_POST['title']) || mb_strlen($_POST['title']) > 45 ) || (empty($_POST['lastname']) || mb_strlen($_POST['lastname']) > 45) || (empty($_POST['firstname'])|| mb_strlen($_POST['firstname']) > 45) || (empty($_POST['category'])) || ($_POST['date'] > $currentDate) || ($_POST['price'] <= 0) ) {
    
//
    $currentDate = date("Y-m-d");
    if (empty($_POST['title']) || mb_strlen($_POST['title']) > 45 ) {
        echo "Vous devez entrer un nom de livre.<br/>";

    } else if (isset($_POST['resume'])) {
        if  (mb_strlen($_POST['resume']) > 1500) {
        echo "Votre résumé doit faire moins de 1500 caractères.";
        };    