<?php
// link call to db (used)
function linkToDb() {
    require_once '_connec.php';
    return $pdo = new \PDO(DSN, USER, PASS);
    return $pdo;
}

// list id of all books (call linktodb() first) (used)
function bookList() {
    $pdo = linkToDb();
    $query = "SELECT idbook FROM `book` ;";
    $statement = $pdo->query($query);
    $books = $statement->fetchAll(PDO::FETCH_COLUMN);
    return $books;
}

function getBook($param) {
    $pdo = linkToDb();
    $queryGetBook = "SELECT * FROM book WHERE idbook = :identifiant";
    $statementGetBook = $pdo ->prepare($queryGetBook);
    $statementGetBook ->bindValue(':identifiant', $param, \PDO::PARAM_INT);
    $statementGetBook =  $pdo->query($queryGetBook);
    $book = $statementGetBook->fetchAll(PDO::FETCH_ASSOC);
    return $book;
}

// delete all from $table where idauthor = ? 
function delete($table, $param) {
    $pdo = linkToDb();
    $queryDelete = "DELETE FROM $table WHERE idauthor = :idauthor";
    $statementDelete = $pdo ->prepare($queryDelete);
    $statementDelete ->bindValue(':idauthor', $param, \PDO::PARAM_INT);
    $statementDelete ->execute(); 
}

// select all from $table order by $column ascending (used in : category.php)
function getAll($table, $column) {
    $pdo = linkToDb($table);
    $queryGetAll = "SELECT * FROM $table ORDER BY $column ASC";
    $statementGetAll = $pdo->query($queryGetAll);
    $categ = $statementGetAll->fetchAll(PDO::FETCH_ASSOC);
    return $categ;
}

function addOne($table, $param, $value, $pdoparam) {
    $pdo = linkToDb();
    $queryAddOne = "INSERT INTO $table ($param) VALUES (':value')";
    $statementAddOne = $pdo->prepare($queryAddOne);
    $statementAddOne ->bindValue(':value', $value, $pdoparam);
    $statementAddOne ->execute();
}