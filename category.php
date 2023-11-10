<?php
$title = "Genre littéraire";
include "header.php";
$categ = getAll("category", "category");
echo '<strong>Liste de tous les genres littéraires</strong>';

echo '<table class="tablecat">';
echo '<tr>';
$counter = 0;
foreach ($categ as $categ) { 
    if ($counter > 0 && $counter % 5 === 0) { 
        echo '</tr><tr>';   
    }
    echo '<td>' . $categ['category'] . '</td>';
    $counter ++;
}
echo '</tr>';
echo '</table>';

include 'footer.php';
?>