<?php
include "header.php";

require '_connec.php';
$pdo = new \PDO(DSN, USER, PASS);

if (!empty($_POST)) {
    $error = 0;
    if ((empty($_POST['title'])) || (mb_strlen($_POST['title']) > 45)) {
        echo "Vous devez entrer un nom de livre de moins de 45 caractères.<br/>";
        $error ++;
    }; 
    if ((empty($_POST['lastname'])) || (mb_strlen($_POST['lastname']) > 45)) {
        echo "Vous devez entrer un nom d'auteur de moins de 45 caractères.<br/>";
        $error ++;
    };
    if ((empty($_POST['firstname'])) || (mb_strlen($_POST['firstname']) > 45)) {
        echo "Vous devez entrer un prénom d'auteur de moins de 45 caractères.<br/>";
        $error ++;
    };
    if (empty($_POST['category'])) {
        echo "Vous devez entrer au moins un genre.<br/>";
        $error ++;
    };
    if (isset($_POST['resume'])) {
        if (mb_strlen($_POST['resume']) > 1500) {
        echo "Votre résumé doit faire moins de 1500 caractères.<br/>";
        $error ++;
        };
    };
    if ($_POST['price'] < 0) {
        echo "Le prix doit être positif.<br/>";
        $error ++;
    };
    $currentDate = date('Y-m-d');
    if (empty($_POST['date']) || $_POST['date'] > $currentDate) {
        echo "La date n'est pas valide.<br/>";
        $error++;
    };
    if ($error === 0) {
        $title = trim($_POST['title']);
        $lastname = trim(ucfirst(strtolower($_POST['lastname'])));
        $firstname = trim(ucfirst(strtolower($_POST['firstname'])));
        $price = $_POST['price'];
        $resume = $_POST['resume'];
        $categories = isset($_POST["category"]) ?
        $_POST["category"]:[];
        $date = $_POST['date'];

        try {  
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();
            $query = 'INSERT INTO book (title, `resume`, `date`, price) VALUES (:title, :resume, :date, :price)';
            $statement =  $pdo ->prepare($query);
            $statement ->bindValue(':title', $title, \PDO::PARAM_STR);
            $statement ->bindValue(':resume', $resume, \PDO::PARAM_STR);
            $statement ->bindValue(':date', $date, PDO::PARAM_STR);
            $statement ->bindValue(':price', $price, PDO::PARAM_STR);
            $statement ->execute();
            $idbook = $pdo->lastInsertId();
            $queryExistingAuthor = "SELECT idauthor FROM author WHERE lastname = :lastname AND firstname = :firstname";
            //
            $statementExistingAuthor = $pdo->prepare($queryExistingAuthor);
            $statementExistingAuthor->bindValue(':lastname', $lastname, PDO::PARAM_STR);
            $statementExistingAuthor->bindValue(':firstname', $firstname, PDO::PARAM_STR);
            $statementExistingAuthor->execute();
            $existingAuthor = $statementExistingAuthor->fetch();
            if (!empty($existingAuthor)) {
                $idauthor = $existingAuthor['idauthor'];
            } else {
                $query2 = "INSERT INTO author (lastname, firstname) VALUES (:lastname, :firstname)";
                $statement2 = $pdo ->prepare($query2);
                $statement2 ->bindValue(':lastname', $lastname, \PDO::PARAM_STR);
                $statement2 ->bindValue(':firstname', $firstname, \PDO::PARAM_STR);
                $statement2 ->execute();
                $idauthor = $pdo->lastInsertId();
            };
            foreach ($categories as $category) {
                $query3 = "INSERT INTO book_category (idbook, idcategory) VALUES (:idbook, :idcategory)";
                $statement3 = $pdo ->prepare($query3);
                $statement3 ->bindValue(':idbook', $idbook, \PDO::PARAM_INT);
                $statement3 ->bindValue(':idcategory', $category, \PDO::PARAM_INT);
                $statement3 -> execute();
            };
            $query4 ="INSERT INTO book_author (idbook, idauthor) VALUES (:idbook, :idauthor)";
            $statement4 = $pdo ->prepare($query4);
            $statement4 ->bindValue(':idbook', $idbook, \PDO::PARAM_INT);
            $statement4 ->bindValue(':idauthor', $idauthor, \PDO::PARAM_INT);
            $statement4 -> execute();
            $pdo->commit();
            
        
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "Failed: " . $e->getMessage();
            };
    header('Location: /index.php');
    die;
    };
};
echo "Ajouter un livre à la bibliothèque.<br/>";
echo '<form method="post">';
echo '<table>';
echo '<tr><th>Titre*</th><td><input type="text" maxlength="45" name="title" value="" required/></td></tr>';
echo '<tr><th>Nom de l\'auteur*</th><td><input type="text" maxlength="45" name="lastname" value="" required/></td></tr>';
echo '<tr><th>Prénom de l\'auteur*</th><td><input type="text" maxlength="45"  name="firstname" value=""required/></td></tr>';
echo '<tr><th>Résumé</th><td><textarea name="resume" maxlength="1500" rows="5" cols="15" value=""></textarea></td></tr>';
$query2 = "SELECT * FROM `category`";
$statement1 = $pdo->query($query2);
$categories = $statement1->fetchAll(PDO::FETCH_ASSOC);
echo '<tr><td>Il faut choisir au moins 1 genre.</td><td></td>';
foreach ($categories as $category) {
    echo '<td><label for="category[]">' . $category['category'] . '</label><input name="category[]" type="checkbox" id="' . $category['idcategory'] . '"value="' . $category['idcategory'] . '"/></td>';
};
echo '</tr>';
echo '<tr><th>Date d\'édition*</th><td><input type="date" name="date" value=""/></td></tr>';
echo '<tr><th>Prix</th><td><input type="number" step="0.01" name="price" value=""/></td></tr>';
echo '<tr><td><input type="submit" value="Enregistrer"></td></tr>';
echo '</table>';

?>