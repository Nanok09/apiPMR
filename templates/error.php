<?php


// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
// Pas de soucis de bufferisation, puisque c'est dans le cas où on appelle directement la page sans son contexte
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=accueil");
    die("");
}

?>

<div class="container">
    <h1>Erreur</h1>

    <div>La vue sélectionnée n'existe pas.</div>
    <a href="index.php?view=accueil">Retour à la page d'accueil</a>

</div>