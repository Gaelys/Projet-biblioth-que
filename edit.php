<?php
$title = "Modifier";
if ((empty($_GET)) || (is_string($_GET['identifiant']) && !is_numeric($_GET['identifiant']))) {
    header('Location: index.php');
    exit();
}
include "header.php";
$books = bookList();
if (!in_array($_GET['identifiant'],$books)) {
    header('Location: index.php');
    exit();
}

$error =0;

if (!empty($_POST)) {
    if ((empty($_POST['title'])) || (mb_strlen($_POST['title']) > 45)) {
        echo "Vous devez entrer un nom de livre de moins de 45 caractères.<br/>";
        $error ++;
    }
    if (mb_strlen($_POST['lastname']) > 45) {
        echo "Vous devez entrer un nom d'auteur de moins de 45 caractères.<br/>";
        $error ++;
    }
    if (mb_strlen($_POST['firstname']) > 45) {
        echo "Vous devez entrer un prénom d'auteur de moins de 45 caractères.<br/>";
        $error ++;
    }
    if ((empty($_POST['lastname'])) AND ((empty($_POST['firstname'])) ) ) {
        echo "Vous devez entrer un prénom ou un nom.<br/>";
        $error ++;
    }
    if (empty($_POST['category'])) {
        echo "Vous devez entrer au moins un genre.<br/>";
        $error ++;
    }
    if (isset($_POST['resume'])) {
        if (mb_strlen($_POST['resume']) > 1500) {
        echo "Votre résumé doit faire moins de 1500 caractères.<br/>";
        $error ++;
        };
    }
    $price = $_POST['price'];
    if (empty($_POST['price'])) {
        $price =0;
    }
    if ($price < 0) {
        echo "Le prix doit être positif.<br/>";
        $error ++;
    }
    $currentDate = date('Y-m-d');
    if (empty($_POST['date']) || $_POST['date'] > $currentDate) {
        echo "La date n'est pas valide.<br/>";
        $error++;
    }
    if ($error === 0) {
        $index = $_GET['identifiant'];
        $query5 = "SELECT title, lastname, firstname, price, `resume`, `date`, book.idauthor FROM `book` 
        INNER JOIN author ON book.idauthor = author.idauthor
        WHERE book.idbook = :identifiant";
        $statement5 = $pdo->prepare($query5);
        $statement5 ->bindValue(':identifiant', $index, \PDO::PARAM_INT);
        $statement5 ->execute();
        $book = $statement5->fetchAll(PDO::FETCH_ASSOC);
        $idAuth = $book[0]['idauthor'];
        $title = trim(ucfirst($_POST['title']));
        $lastname = trim(ucfirst($_POST['lastname']));
        $firstname = trim(ucfirst($_POST['firstname']));
        $author =$lastname . $firstname;
        $resume = $_POST['resume'];
        $categories = isset($_POST["category"]) 
            ? $_POST["category"]
            :[];
        $date = $_POST['date'];
        $idbook = $_GET['identifiant'];
        
        try {  
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        $datasAuths = $pdo->prepare("SELECT * FROM author");
        $datasAuths ->execute();
        $auths = $datasAuths->fetchAll(PDO::FETCH_ASSOC);
    
        
        $dataBooks = $pdo->prepare("SELECT * FROM  book ");
        $dataBooks->execute();
        $bookas = $dataBooks->fetchAll(PDO::FETCH_ASSOC);

        $countNbAuth = 0;
        $verif = true;
        foreach($auths as $auth){
            if($auth['lastname'] == $_POST['lastname'] && $auth['firstname'] == $_POST['firstname'] ){
                $idauthor = $auth['idauthor'];
                $verif = false;
            }
        }
        if($verif) {
            $queryCreatAuthor = "INSERT INTO author (lastname, firstname) VALUES (:lastname, :firstname)";
            $statementCreatAuthor = $pdo ->prepare($queryCreatAuthor);
            $statementCreatAuthor ->bindValue(':lastname', $lastname, \PDO::PARAM_STR);
            $statementCreatAuthor ->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
            $statementCreatAuthor ->execute();
            $idauthor = $pdo->lastInsertId();
        }
        foreach($bookas as $booka){
            if($booka['idauthor'] == $book[0]['idauthor']){
                $countNbAuth +=1;
            }
        }
        
        $querySeeCateg = "SELECT idcategory FROM book_category WHERE idbook = :identifiant";
        $statementSeeCateg = $pdo->prepare($querySeeCateg);
        $statementSeeCateg ->bindValue(':identifiant', $idbook, \PDO::PARAM_INT);
        $statementSeeCateg ->execute();
        $categOfBook = $statementSeeCateg->fetchAll(PDO::FETCH_COLUMN);
        foreach ($categOfBook as $category) {
            if (!in_array($category, $categories)) {
                $queryDeleteCategory = "DELETE FROM book_category WHERE idbook = :idbook AND idcategory = :idcategory";
                $statementDeleteCategory = $pdo->prepare($queryDeleteCategory);
                $statementDeleteCategory->bindValue(':idbook', $idbook, PDO::PARAM_INT);
                $statementDeleteCategory->bindValue(':idcategory', $category, PDO::PARAM_INT);
                $statementDeleteCategory->execute();
            }
        }
        foreach ($categories as $category) {
            if (!in_array($category, $categOfBook)) {
                $queryInsertCategory = "INSERT INTO book_category (idbook, idcategory) VALUES (:idbook, :idcategory)";
                $statementInsertCategory = $pdo->prepare($queryInsertCategory);
                $statementInsertCategory->bindValue(':idbook', $idbook, PDO::PARAM_INT);
                $statementInsertCategory->bindValue(':idcategory', $category, PDO::PARAM_INT);
                $statementInsertCategory->execute();
            }
        }
        
        $queryModifyBook = "UPDATE book SET title = :title, `resume` = :resume, `idauthor` = :idauthor ,`date` = :date, price = :price WHERE idbook = :identifiant";
        $statementModifyBook = $pdo->prepare($queryModifyBook);
        $statementModifyBook ->bindValue(':title', $title, \PDO::PARAM_STR);
        $statementModifyBook ->bindValue(':resume', $resume, \PDO::PARAM_STR);
        $statementModifyBook ->bindValue(':date', $date, PDO::PARAM_STR);
        $statementModifyBook ->bindValue(':price', $price, PDO::PARAM_STR);
        $statementModifyBook ->bindValue(':idauthor', $idauthor, PDO::PARAM_INT);
        $statementModifyBook ->bindValue(':identifiant', $idbook, \PDO::PARAM_INT);
        $statementModifyBook ->execute();

        if ($countNbAuth === 1 AND $idauthor !== $idAuth){
            $queryFinalDelete = "DELETE FROM author WHERE idauthor = :idauthor";
            $statementFinalDelete = $pdo ->prepare($queryFinalDelete);
            $statementFinalDelete ->bindValue(':idauthor', $book[0]['idauthor'], \PDO::PARAM_INT);
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
    }
}

// affiche les infos du livre
$index = $_GET['identifiant'];
$query5 = "SELECT title, lastname, firstname, price, `resume`, `date` FROM `book` 
INNER JOIN author ON book.idauthor = author.idauthor
 WHERE book.idbook = :identifiant";
$statement5 = $pdo->prepare($query5);
$statement5 ->bindValue(':identifiant', $index, \PDO::PARAM_INT);
$statement5 ->execute();
$book = $statement5->fetchAll(PDO::FETCH_ASSOC);
echo '<h2>Modifier les informations du livre "' . $book[0]['title'] . '".</h2><br/<br/>';


echo '<form method="post">';
echo '<table>';

echo '<tr><th>Titre*</th><td><input type="text" name="title" value="' . $book[0]['title'] . '" required></td></tr>';
echo '<tr><th>Nom de l\'auteur</th><td><input type="text" name="lastname" value="' . $book[0]['lastname'] . '"/></td></tr>';
echo '<tr><th>Prénom de l\'auteur</th><td><input type="text" name="firstname" value="' . $book[0]['firstname'] . '"/></td></tr>';
echo '<tr><th>Résumé</th><td><textarea rows="6" cols="40" id="resume" name="resume" >' . $book[0]['resume'] . '</textarea></td></tr>';
echo '<tr><td>Si aucune date de parution n\'a été séléctionné, elle est par défaut réglée sur le 01-01-1000</td></tr>';
echo '<tr><th>Date de parution</th><td><input type="date" name="date" value="' . $book[0]['date'] . '"/></td></tr>';
echo '<tr><th>Prix</th><td><input type="number" step="0.01" name="price" value="' . $book[0]['price'] . '"/></td></tr>';
echo '</table>';
echo '<table class="categ">';
echo '<tr>';
$query6 = "SELECT * FROM `category`";
$statement6 = $pdo->query($query6);
$categories = $statement6->fetchAll(PDO::FETCH_ASSOC);
$queryCategoryOfBook = "SELECT idcategory FROM book_category WHERE idbook = :identifiant";
$statementCategoryOfBook = $pdo->prepare($queryCategoryOfBook);
$statementCategoryOfBook ->bindValue(':identifiant', $index, \PDO::PARAM_INT);
$statementCategoryOfBook ->execute();
$categoryOfBook = $statementCategoryOfBook->fetchAll(PDO::FETCH_COLUMN);
$counter = 0;
foreach ($categories as $category) {
    $isChecked = in_array($category['idcategory'], $categoryOfBook) ? 'checked' : '';

    if ($counter > 0 && $counter % 5 === 0) { 
        echo '</tr><tr>'; 
    }

    echo '<td><label for="category[]">' . $category['category'] . '</label><input name="category[]" type="checkbox" id="' . $category['idcategory'] . '" value="' . $category['idcategory'] . '" ' . $isChecked . '></td>';
    $counter++;
    
}

echo '</tr>';
echo '<tr><td><input type="submit" value="Enregistrer"></td></tr>';
echo '</table>';
echo '</form>';

include "footer.php";
?>