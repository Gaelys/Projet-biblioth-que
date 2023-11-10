<?php
// link call to db
function linkToDb() {
    require_once '_connec.php';
    return $pdo = new \PDO(DSN, USER, PASS);
    return $pdo;
}

// list id of all books (call linktodb() first)
function bookList() {
    $pdo = linkToDb();
    $query = "SELECT idbook FROM `book` ;";
    $statement = $pdo->query($query);
    $books = $statement->fetchAll(PDO::FETCH_COLUMN);
    return $books;
}

function getBook($idbook) {
    $pdo = linkToDb();
    $queryGetBook = "SELECT * FROM book WHERE idbook = :identifiant";
    $statementGetBook = $pdo ->prepare($queryGetBook);
    $statementGetBook ->bindValue(':identifiant', $idbook, \PDO::PARAM_INT);
    $statementGetBook =  $pdo->query($queryGetBook);
    $book = $statementGetBook->fetchAll(PDO::FETCH_ASSOC);
    return $book;
}

function delete($table, $parametre) {
    $pdo = linkToDb();
    $queryDelete = "DELETE FROM $table WHERE idauthor = :idauthor";
    $statementDelete = $pdo ->prepare($queryDelete);
    $statementDelete ->bindValue(':idauthor', $parametre, \PDO::PARAM_INT);
    $statementDelete ->execute(); 
}