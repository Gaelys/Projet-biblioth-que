<?php
if (empty($_GET)) {
    header('Location: index.php');
    exit();
};
require_once '_connec.php';
$pdo = new \PDO(DSN, USER, PASS);
try {  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
    $pdo->beginTransaction();
    $pdo->exec("DELETE FROM book_author WHERE idbook=" . $_GET['identifiant']);
    $pdo->exec("DELETE FROM book WHERE idbook=" . $_GET['identifiant']);
    $pdo->exec("DELETE FROM book_category WHERE idbook=" . $_GET['identifiant']);
    
    $pdo->commit();
  
  } catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
};
die;
header('Location: /index.php');
die;
?>