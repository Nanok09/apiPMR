<?php

include_once "libUtils.php"; // Car on utilise la fonction valider()
include_once "modele.php"; // Car on utilise la fonction connecterUtilisateur()

/**
 * @file login.php
 * Fichier contenant des fonctions de vérification de logins
 */

/**
 * Cette fonction vérifie si le login/passe passés en paramètre sont légaux
 * Elle stocke les informations sur la personne dans des variables de session : session_start doit avoir été appelé...
 * Infos à enregistrer : pseudo, idUser, heureConnexion, isAdmin
 * Elle enregistre l'état de la connexion dans une variable de session "connecte" = true
 * L'heure de connexion doit être stockée au format date("H:i:s")
 * @pre login et passe ne doivent pas être vides
 * @param string $login
 * @param string $password
 * @return false ou true ; un effet de bord est la création de variables de session
 */
function verif_user($login, $password)
{
    if ($id = verif_user_bdd($login, $password)) {

        $_SESSION['is_connected'] = true;
        $_SESSION['pseudo'] = $login;
        $_SESSION['id_user'] = $id;
        $_SESSION['heure_connexion'] = date("H:i:s");
        $_SESSION['id_admin'] = is_admin($id);
        return true;
    } else {
        return false;
    }
}

/**
 * Fonction à placer au début de chaque page privée

 * Cette fonction redirige vers la page $url_bad en envoyant un message d'erreur
 * et arrête l'interprétation si l'utilisateur n'est pas connecté
 * Elle ne fait rien si l'utilisateur est connecté, et si $urlGood est faux
 * Elle redirige vers urlGood sinon
 */
function securiser($urlBad, $urlGood = false)
{
    if (!valider("is_connected", "SESSION")) {
        header("Location:$urlBad");
        die("");
    }
    if ($urlGood) {
        header("Location:$urlGood");
        die("");
    }
}

/**
 * A placer au début de chaque page uniquement accessible pour les administrateurs
 */
function securiser_admin($url_bad, $url_good = false)
{
    if (valider("is_admin", "SESSION") == 1 && valider("is_connected", "SESSION")) {
        if ($url_good) {
            header("Location:" . $url_good);
        }
    } else {
        header("Location:" . $url_bad);
    }

}
