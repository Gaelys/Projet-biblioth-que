<?php
if (empty($_GET)) {
    header('Location: index.php');
    exit();
}
include "header.php";
$idbook = $_GET['identifiant'];
try {  

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();
    //select data from book
    $queryGetBook = "SELECT idauthor FROM book WHERE idbook =:identifiant";
    $statementGetBook = $pdo ->prepare($queryGetBook);
    $statementGetBook ->bindValue(':identifiant', $idbook, \PDO::PARAM_INT);
    $statementGetBook-> execute();
    $idauth = $statementGetBook->fetch(PDO::FETCH_ASSOC);
    $idauthor = $idauth['idauthor'] ;
    
    //vérifie si auteur à d'autres livres, si non delete
    $datasAuths = $pdo->prepare("SELECT * FROM author");
    $datasAuths ->execute();
    $auths = $datasAuths->fetchAll(PDO::FETCH_ASSOC);
    $dataBooks = $pdo->prepare("SELECT * FROM  book ");
    $dataBooks->execute();
    $bookas = $dataBooks->fetchAll(PDO::FETCH_ASSOC);
    $countNbAuth = 0;
    foreach($bookas as $booka){
      if($booka['idauthor'] == $idauthor){
          $countNbAuth +=1;
      }
    }
    $queryDeleteCat = "DELETE FROM book_category WHERE idbook =:identifiant";
    $queryDeleteCat = $pdo ->prepare($queryDeleteCat);
    $queryDeleteCat ->bindValue(':identifiant', $idbook, \PDO::PARAM_INT);
    $queryDeleteCat-> execute();

    $queryBook = "DELETE FROM book WHERE idbook =:identifiant";
    $statementqueryBook = $pdo ->prepare($queryBook);
    $statementqueryBook ->bindValue(':identifiant', $idbook, \PDO::PARAM_INT);
    $statementqueryBook-> execute();
    
    if ($countNbAuth === 1){
      $queryFinalDelete = "DELETE FROM author WHERE idauthor = :idauthor";
      $statementFinalDelete = $pdo ->prepare($queryFinalDelete);
      $statementFinalDelete ->bindValue(':idauthor', $idauthor, \PDO::PARAM_INT);
      $statementFinalDelete ->execute();  
    }
    
    
    $pdo->commit();
  
  } catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
    die;
}
header('Location: /index.php');
die;
include "footer.php";
?>