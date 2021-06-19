<?php
session_start();


if (empty($_SESSION['id_user'])) {
    if (!empty($_COOKIE['pseudo'])) {
        verif_user($_COOKIE['pseudo'], $_COOKIE['password']);
    }
}

include_once "libs/libUtils.php";

// Dans tous les cas, on affiche l'entete,
// qui contient les balises de structure de la page, le logo, etc.
// Le formulaire de recherche ainsi que le lien de connexion
// si l'utilisateur n'est pas connecté
include "templates/header.php";

// on récupère le paramètre view éventuel
$view = valider("view");

// S'il est vide, on charge la vue accueil par défaut
if (!$view) {
    $view = "accueil";
}

// En fonction de la vue à afficher, on appelle tel ou tel template
switch ($view) {
        /*
    case "accueil" :
    include("templates/accueil.php");
    break;

    case "login" :
    include("templates/login.php");
    break;

    case "users" :
    include("templates/users.php");
    break;
     */
    default: // si le template correspondant à l'argument existe, on l'affiche
        if (file_exists("templates/$view.php")) {
            include "templates/$view.php";
        } else {
            include "templates/error.php";
        }
}

// Dans tous les cas, on affiche le pied de page
// Qui contient les coordonnées de la personne si elle est connectée
include "templates/footer.php";