<?php

include_once "libSQL.pdo.php";
include_once "libValidation.php";
// ============ UTILISATEURS ==============
/**
 * Vérifie si un utilisateur est bien dans la bdd
 * @param string $login
 * @param string $passe
 * @return string,int? id de l'utilisateur ou false
 */
function verif_user_bdd($login, $passe)
{
    $SQL = "SELECT id FROM utilisateurs WHERE pseudo=:login AND password=:passe;";
    $params = array("login" => $login, "passe" => $passe);
    return SQLGetChamp($SQL, $params);
}

/**
 * Créé un nouvel utilisateur dans la bdd
 * @param string pseudo
 * @param string password
 * @param string email
 * @param string nom
 * @param string prenom
 * @return int id de l'utilisateur inséré ou false si erreur
 */
function create_user($pseudo, $password, $email, $nom, $prenom)
{
    $admin = '0';
    $SQL = "INSERT INTO utilisateurs (pseudo,password,nom,prenom,email,admin)";
    $SQL .= " VALUES (:pseudo,:password,:nom,:prenom,:email,:admin);";
    $params = array("pseudo" => $pseudo, "password" => $password, "nom" => $nom, "prenom" => $prenom, "email" => $email, "admin" => $admin);
    return SQLInsert($SQL, $params);
}

/**
 * Récupére les données affichées dans la page mon compte
 * @param int $id_user
 */
function get_user_info($id_user)
{
    $SQL = "SELECT pseudo, email, nom, prenom FROM utilisateurs WHERE id=?;";
    $params = array($id_user);
    $res = parcoursRs(SQLSelect($SQL, $params));
    if (!$res) {
        return $res;
    } else {
        return $res[0];
    }
}

/**
 * *Récupere une liste de tous les utilisateurs dans la bdd
 */
function get_users()
{
    $SQL = "SELECT * FROM utilisateurs";
    $params = [];
    return parcoursRs(SQLSelect($SQL, $params));
}

/**
 * Vérifie si un utilisateur est admin
 * @param int id_user
 * @return bool
 */
function is_admin($id_user)
{
    // vérifie si l'utilisateur est un administrateur
    $SQL = "SELECT admin from utilisateurs where id=?;";
    $params = array($id_user);
    return SQLGetChamp($SQL, $params);
}

/**
 * vériefie si il existe bien un utilisateur associé à l'id fourni dans la bdd
 * @param int id_user
 * @return bool id_user si l'utilsateur existe et false sinon
 */
function is_user($user_id)
{
    $SQL = "SELECT id FROM utilisateurs WHERE id=?";
    $param = array($user_id);
    return SQLGetChamp($SQL, $param);
}

function get_place_creator($place_id)
{
    $SQL = "SELECT createur FROM lieux WHERE id=?";
    $param = array($place_id);
    return SQLGetChamp($SQL, $param);
}

/**
 * Modifie les infos d'un utilisateur
 * @param int id_user
 * @param string password
 * @param string email
 * @param string nom
 * @param string prenom
 * @param
 */
function update_user($id_user, $email, $nom, $prenom)
{
    $SQL = "UPDATE utilisateurs SET email=:email, nom=:nom, prenom=:prenom, timestamp=timestamp WHERE id =:id_user";
    $params = array("id_user" => $id_user, "nom" => $nom, "prenom" => $prenom, "email" => $email);
    return SQLUpdate($SQL, $params);
}

// ================== LIEUX ===================

/**  fonction distance renvoie un float représentant la distance entre les points passés en entrée. La distance est donnée en km
 */

function distance($lat1, $lng1, $lat2, $lng2, $miles = false)
{
    $pi80 = M_PI / 180;
    $lat1 *= $pi80;
    $lng1 *= $pi80;
    $lat2 *= $pi80;
    $lng2 *= $pi80;

    $r = 6372.797; // rayon moyen de la Terre en km
    $dlat = $lat2 - $lat1;
    $dlng = $lng2 - $lng1;
    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin(
        $dlng / 2
    ) * sin($dlng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $km = $r * $c;
    return ($miles ? ($km * 0.621371192) : $km);
}
/* Cette version ne marche pas j'ai oublié des conversions
function calculate_distance_between(array $a,array $b){
// on utilise la formule de haversine
$r= 6371;
$first_term = (sin(($a['lat']-$b['lat'])/2))**2;
$second_term = cos($a['lat'])*cos($b['lat'])*(sin(($a['long']-$b['long'])/2)**2);
$result = 2*$r*asin(sqrt($first_term + $second_term));
return $result;
}
 */
// fonction de comparaison pour le tri customisé
function compare($array1, $array2)
{
    if ($array1['distance_to_user'] > $array2['distance_to_user']) {
        return 1;
    }
    if ($array1['distance_to_user'] == $array2['distance_to_user']) {
        return 0;
    }
    return -1;
}

/** TODO : gérer le cas où on envoie pas une localisation mais juste une adresse et instaurer un paramètre max_results
 * Récupérer les lieux les plus proches correspondant à certains critères
 * Tous les paramètres sont optionels
 * @param double lat
 * @param double long
 * @param string sport
 * @param float price_min
 * @param float price_max
 * @param bool publuc_only
 * @param bool private_only
 * @param int max_distance (km)
 * @param int max_results (TODO)
 */

function get_places($sport = false, bool $private_only = false, bool $public_only = false, $lat = false, $long = false, int $price_min = 0, int $price_max = 10000, $max_distance = 1000, $max_results = 10)
{

    // récupérer la liste des terrains potentielement intéressant dans la base de données

    $SQL = "SELECT * FROM lieux WHERE prix >= :price_min AND prix <= :price_max";

    $values = array(
        'price_min' => $price_min,
        'price_max' => $price_max,
    );

    if ($sport) {
        $SQL .= " AND sport=:sport";
        $values["sport"] = $sport;
    }

    if ($private_only && !$public_only) {
        $SQL .= " AND prive=:prive";
        $values["prive"] = 1; // si c'est que du private et pas du public alors on cherche les valeur prive=1
    }
    if ($public_only && !$private_only) {
        $SQL .= " AND prive=:prive";
        $values["prive"] = 0;
    }

    if ($private_only && $public_only) {
        // raise error 'Warning : You are asking for private and public places, those are not supported in the current version'
        trigger_error("You are asking for private and public places, those does not exist in the current version", E_USER_WARNING);
    }

    //var_dump($values);

    $query_results = parcoursRs(SQLSelect($SQL, $values));

    //var_dump($result);

    // filtrer le résultat à l'aide de la fonction calculate distance between

    if ($lat && $long) {
        //echo 'on est dans la partie calculer les distances!';
        $final_results = array();
        foreach ($query_results as $result) {
            $distance = distance($result['latitude'], $result['longitude'], $lat, $long);
            //var_dump($distance);
            if ($distance <= $max_distance) {
                $result['distance_to_user'] = $distance;
                $final_results[] = $result;
            } // end if du foreach

        } //end For each
        // trier les tableaux obtenus par distance à l'utilisateur croissante
        //echo '<h2> Avant tri : </h2>';
        //var_dump($final_results);
        usort($final_results, 'compare'); // utilisation de la fonction usort qui permet de faire un tri customisé
        return $final_results;
    } //end if

    return $query_results;

    //renvoyer le résultat au bon format

} //end Fonction

//Créer un lieu
/**
 * Fonction permettant de creer un lieu
 * @param string nom
 * @param string description
 * @param string adresse
 * @param float lat
 * @param float long
 * @param string sport
 * @param int prive
 * @param int createur_id
 * @param float price
 * @param int capacite
 * @return int lastInsertedId
 */

function create_place(string $nom, float $lat, float $long, string $sport, int $private, int $createur_id, float $price = 0, int $capacite = 10, string $description = '', string $adresse = '')
{
    // il faut vérifier si le créateur_id correspond bien à un utilisateur. On pourait utiliser les variables de session comme paramètre par défaut..?

    if ($id_user = is_user($createur_id)) {

        $param = array(
            'nom' => $nom,
            'description' => $description,
            'adresse' => $adresse,
            'latitude' => $lat,
            'longitude' => $long,
            'sport' => $sport,
            'prive' => $private,
            'createur' => $createur_id,
            'prix' => $price,
            'capacite' => $capacite,
        );
        $SQL = "INSERT INTO `lieux` (`nom`, `description`, `adresse`, `latitude`, `longitude`, `sport`, `prive`, `createur`, `prix`, `capacite`) VALUES
        ( :nom, :description, :adresse, :latitude, :longitude, :sport, :prive, :createur, :prix, :capacite)";

        return SQLInsert($SQL, $param);
    } //end if
    trigger_error("The given creator is not registered in the data base", E_USER_WARNING);
    return false;
} // end function

/** Modifier un lieu pour son créateur
 * @param int place_id
 * @param array modifications  un tablau associatif avec en clé les champs à modifier dans la bdd et en valeur les nouvelles valeurs à mettre
 * @return bool false or int nb_modifications: si l'utilisateur n'a pas le droit de modifier la place en question ou si il y a eu un pb pdt la requete, sinon renvoie le nombre de modifications
 */

function modify_place(int $place_id, int $user_id, array $modifications)
{

    // UPDATE commentaires SET message = :message, edited = true, timestamp=:timestamp WHERE idCommentaire = :id_comment AND idUtilisateur= :id_user"

    if ($user_id == get_place_creator($place_id)) { // vérifier que l'id correspond bien à un lieu dont l'utilisateur est le créateur
        $SQL = "UPDATE lieux SET";
        foreach ($modifications as $champ => $modification) {
            $SQL .= " $champ = :$champ, ";
        }
        $SQL = substr($SQL, 0, -2);
        $SQL .= " WHERE id=:id";
        var_dump($SQL);
        $modifications['id'] = $place_id;

        var_dump($modifications);
        return SQLUpdate($SQL, $modifications);
    } // end if

    trigger_error('The given user is not allowed to modify this place because it is not the place creator', E_USER_WARNING);
    return false;
}

//Récupérer les infos d'un lieu

/**
 * @param int place_id
 * @return array place_info
 */

function get_place_info(int $place_id)
{

    $SQL = "SELECT * FROM lieux WHERE id=?";
    $param = array($place_id);
    $res = parcoursRs(SQLSelect($SQL, $param));
    if (!$res) {
        return $res;
    } else {
        return $res[0];
    }
}
/**
 * Récupère le créateur d'un lieu
 * @param int id_place
 */
function get_createur_lieu($id_place)
{
    $SQL = "SELECT createur FROM lieux WHERE id = :id_place";
    $params = array("id_place" => $id_place);
    return SQLGetChamp($SQL, $params);
}
/**
 * Récupérer les lieux créés par un utilisateur
 * @param int id_user
 */
function get_places_created_by($id_user)
{
    $SQL = "SELECT * FROM lieux WHERE createur = ?";
    $params = array($id_user);
    return parcoursRs(SQLSelect($SQL, $params));
}

//// ================= NOTES =========================
/**
 * Ajoute une note à un lieu (ou la modifie si elle existe déjà)
 * @param int id_user
 * @param int id_place
 * @param int note (entre 1 et 5)
 */
function add_note($id_user, $id_place, $note)
{
    $SQL = "INSERT INTO notes (idLieu,idUtilisateur,note) VALUES (:id_place,:id_user,:note)";
    $SQL .= " ON DUPLICATE KEY UPDATE note = :note";
    $params = array("id_user" => $id_user, "id_place" => $id_place, "note" => $note);
    return SQLInsert($SQL, $params);
}

/**
 * Récupère la moyenne et le nombre de notes associées à un lieu
 * @param int id_place
 * @return Tableau_associatif
 * float note_moyenne
 * int nb_notes
 */
function get_note_place($id_place)
{
    $SQL = "SELECT avg(note) mean, count(note) nb_notes FROM notes WHERE idLieu = ?";
    $params = array($id_place);
    $res = parcoursRs(SQLSelect($SQL, $params));
    if (!$res) {
        return $res;
    } else {
        return $res[0];
    }
}

/**
 * Supprime une note associée à un lieu
 * @param int  id_place
 * @param int id_user
 */
function delete_note($id_place, $id_user)
{
    $SQL = "DELETE FROM notes WHERE idLieu=:id_place AND idUtilisateur=:id_user";
    $params = array("id_place" => $id_place, "id_user" => $id_user);
    return SQLDelete($SQL, $params);
}
// ============== COMMENTAIRES ================
/**
 * Ajouter un commentaire
 * @param int  id_place
 * @param int id_user
 * @param string message
 * @return int  id_comment
 */
function add_comment($id_place, $id_user, $message, $timestamp)
{
    $SQL = "INSERT INTO commentaires (idLieu,idUtilisateur,message,timestamp) VALUES (:id_place,:id_user,:message,:timestamp)";
    $params = array("id_place" => $id_place, "id_user" => $id_user, "message" => $message, "timestamp" => $timestamp);
    return SQLInsert($SQL, $params);
}
/**
 * Modifie un commentaire
 * @param int id_user
 * @param int id_comment
 * @param string message
 */

function modify_comment($id_user, $id_comment, $message, $timestamp)
{
    $SQL = "UPDATE commentaires SET message = :message, edited = true, timestamp=:timestamp WHERE id = :id_comment AND idUtilisateur= :id_user";
    $params = array("message" => $message, "id_comment" => $id_comment, "id_user" => $id_user, "timestamp" => $timestamp);
    return SQLUpdate($SQL, $params);
}
/**
 * Récupère la liste des commentaires pour un lieu, ordonnés du plus récent au plus vieux
 * @param int id_place
 * @return liste de tableaux associatifs contenant les champs "nomUtilisateur", "id" (du commentaire), "message", "timestamp"
 */
// TODO: limiter le nb de commentaires récupérés ?
function get_comments($id_place)
{
    $SQL = "SELECT u.pseudo nomUtilisateur, u.id idUtilisateur , c.id, c.message, c.timestamp FROM commentaires as c INNER JOIN utilisateurs as u ON c.idUtilisateur=u.id";
    $SQL .= " WHERE c.idLieu = :id_place ORDER BY c.timestamp DESC";
    $params = array("id_place" => $id_place);
    return parcoursRs(SQLSelect($SQL, $params));
}
/**
 * Supprime un commentaire
 * @param int id_user
 * @param int id_comment
 */
function delete_comment($id_user, $id_comment)
{
    $SQL = "DELETE FROM commentaires WHERE idUtilisateur=:id_user AND id=:id_comment";
    $params = array("id_user" => $id_user, "id_comment" => $id_comment);
    return SQLDelete($SQL, $params);
}
// ============ PHOTOS LIEUX ============

//
/**
 * Ajouter une photo associée à un lieu (après l'avoir uploadé sur le serveur dans un dossier spécifique)
 * @param int id_place
 * @param string nomFichier
 * @return int idPhoto
 */
function add_photo_place($id_place, $file_name)
{
    $SQL = "INSERT INTO photosLieux (idLieu,nomFichier) VALUES (:id_place,:file_name)";
    $params = array("id_place" => $id_place, "file_name" => $file_name);
    return SQLInsert($SQL, $params);
}
/**
 * Supprimer une photo (en vérifiant que le lieu associé à cette photo a bien été crée par $id_user)
 * @param int id_user
 * @param int idPhoto
 */
function delete_photo_place($id_user, $id_photo)
{
    $SQL = "DELETE FROM photosLieux p INNER JOIN lieux l ON p.idLieu = l.id WHERE p.id = :id_photo AND l.createur = :id_user";
    $params = array("id_photo" => $id_photo, "id_user" => $id_user);
    return SQLDelete($SQL, $params);
}
/**
 * Récupère les noms des fichiers des photos associés à un lieu
 * @param int id_place
 */
function get_photos_place($id_place)
{
    $SQL = "SELECT id,nomFichier FROM photosLieux WHERE idLieu=?";
    $params = array($id_place);
    return parcoursRs(SQLSelect($SQL, $params));
}
/**
 * Récupère toutes les photos
 */
function get_photos()
{
    $SQL = "SELECT * FROM photosLieux";
    $params = array();
    return parcoursRs(SQLSelect($SQL, $params));
}

// ============ CHAT =============







/**
 * Récupérer tous les messages entre 2 personnes
 * @param int conv_id
 */

function get_messages_in_conv($conv_id)
{
    $SQL = "SELECT auteur,destinataire,message,timestamp FROM messagesChat WHERE id_conv=?";
    $param = array($conv_id);
    return parcoursRs(SQLSelect($SQL, $param));
}


/**
 * Récupérer toutes les conversations d'un utilisateur
 * @param int id_user
 */
function get_conversations_user($id_user)
{
    //Récupérer tous ID des personnes avec qui la $id_auteur a parlé

    $SQL = "SELECT id FROM conversations WHERE membre_1=:id_user OR membre_2=:id_user";
    $params = array('id_user' => $id_user);
    return parcoursRs(SQLSelect($SQL, $params));
}




/**
 * Envoyer un message i.e ajouter un nouveau message dans la base de donnée
 * @param int id_user
 * @param int id_user_dest id du destinataire
 * @param string message
 */
function add_message_to_conv($id_conv, $id_auteur, $id_destinataire, $msg)
{
    //Ajoute un nouveau message à la BDD
    //INSERT INTO `messageschat` (`id`, `auteur`, `destinataire`, `message`, `timestamp`, `id_conv`) VALUES (NULL, '6', '6', 'test', CURRENT_TIMESTAMP, '2');
    $SQL = "SELECT id,membre_1,membre_2 FROM conversations WHERE id=?";
    $param = array($id_conv);
    if ($result = parcoursRs(SQLSelect($SQL, $param))) {
        //var_dump($result);
        if ($result[0]['membre_1'] == $id_auteur || $result[0]['membre_2'] == $id_auteur) {
            if ($result[0]['membre_1'] == $id_destinataire || $result[0]['membre_2'] == $id_destinataire) {
                //echo 'on est dans la partie insert into';
                $SQL = "INSERT INTO messagesChat (auteur,destinataire,message,timestamp,id_conv) VALUES ( :auteur , :destinataire , :message ,CURRENT_TIMESTAMP,:id_conv)";
                $param = array('auteur' => $id_auteur, 'destinataire' => $id_destinataire, 'message' => $msg, 'id_conv' => $id_conv);
                return SQLInsert($SQL, $param);
            }
        }
    }
    return false;
}

/**
 * Trouver le nom et le prénom de l'utilisateur associé à un id 
 * @param int id_user
 */

function find_user_name($id_user)
{

    $SQL = "SELECT nom,prenom FROM utilisateurs WHERE id= ?";
    $param = array($id_user);
    return parcoursRs(SQLSelect($SQL, $param));
}

function get_last_msg_info($id_conv)
{
    $SQL = "SELECT message,timestamp,auteur,destinataire FROM messagesChat WHERE id_conv=?";
    $param = array($id_conv);
    $result = parcoursRs(SQLSelect($SQL, $param));
    return end($result);
}

/**
 * Récupère les messages reçus par id_user après l'id du dernier message reçu
 * @param int id_user
 * @param int id_last_msg
 */

// ============= CRENEAUX DISPONIBLES ============

//
/**
 * Ajouter un créneau de disponibilité d'un lieu pour le créateur
 * @pre verifier si id_user est le createur de id_place
 * @param int id_place
 * @param string date (jour sous la forme yyyy-mm-dd)
 * @param string heure_debut (hh:mm)
 * @param string heure_fin (hh:mm)
 * @param int capacite
 */
function add_creneau_dispo($id_place, $date, $heure_debut, $heure_fin, $capacite)
{
    $SQL = "INSERT INTO creneauxDispo (idLieu,date,heureDebut,heureFin,capacite) VALUES(:id_place,:date,:heure_debut,:heure_fin,:capacite);";
    $params = array("id_place" => $id_place, "date" => $date, "heure_debut" => $heure_debut, "heure_fin" => $heure_fin, "capacite" => $capacite);
    return SQLInsert($SQL, $params);
}

// ============= RESERVATIONS ============

/**
 * Récupère les réservations qui ne sont pas encore terminées
 * @param int id_user
 */
function get_current_reservations($id_user)
{
    $SQL = "SELECT p.nomFichier, l.nom as nomTerrain, r.* FROM reservations r ";
    $SQL .= "INNER JOIN lieux l ON l.id = r.idLieu ";
    $SQL .= "INNER JOIN (SELECT idLieu,nomFichier FROM photosLieux GROUP BY idLieu) p ON p.idLieu = r.idLieu ";
    $SQL .= "WHERE r.idUtilisateur=:id_user AND r.date>=DATE(NOW())";
    $params = array("id_user" => $id_user);
    return parcoursRs(SQLSelect($SQL, $params));
}
/**
 * Réserver un créneau
 * pre vérifier qu'il y a au minimum nb_personnes en capacité restante sur le créneau ciblé
 * @param int id_user
 * @param int id_lieu
 * @param string date (yyyy-mm-dd)
 * @param string heure_debut (hh:mm)
 * @param string heure_fin (hh:mm)
 * @param int nb_personnes
 */
function add_reservation($id_user, $id_place, $date, $heure_debut, $heure_fin, $nb_personnes)
{
    $SQL = "INSERT INTO reservations (idUtilisateur,date,heureDebut,heureFin,nbPersonnes,idLieu) VALUES (:id_user,:date,:heure_debut,:heure_fin,:nb_personnes,:id_place);";
    $params = array("id_user" => $id_user, "date" => $date, "heure_debut" => $heure_debut, "heure_fin" => $heure_fin, "nb_personnes" => $nb_personnes, "id_place" => $id_place);
    return SQLInsert($SQL, $params);
}

/**
 * Annuler un créneau réservé
 * @param int id_user
 * @param int id_reservation
 */
function delete_reservation($id_user, $id_reservation)
{
    $SQL = "DELETE FROM reservations WHERE idUtilisateur = :id_user AND id = :id_reservation;";
    $params = array("id_user" => $id_user, "id_reservation" => $id_reservation);
    return SQLDelete($SQL, $params);
}

// ============== DISPONIBILITES ==================

//

/**
 * Récupère toutes les disponibilités d'un lieu entre deux dates (date_debut et fin incluses)
 * @param int id_place
 * @param string date_debut (yyyy-mm-dd)
 * @param string date_fin (yyyy-mm-dd)
 */
function get_creneaux_lieu($id_place, $date_debut, $date_fin)
{
    $condition = "WHERE t.idLieu = :id_place AND t.date >= :date_debut AND t.date <= :date_fin";
    $SQLExplode1 = "SELECT t.id as idCreneauDispo, t.idLieu, t.date, sum(t.capacite) as capacite, v.id as idCreneauHoraire, v.debut, v.fin FROM creneauxDispo as t LEFT JOIN creneauxValides as v ON t.heureDebut<=v.debut AND t.heureFin>=v.fin " . $condition . " GROUP BY date,idCreneauHoraire";
    $SQLExplode2 = "SELECT t.idLieu, t.date, sum(t.nbPersonnes) as nbPersonnes, v.id as idCreneauHoraire, v.debut, v.fin FROM reservations as t LEFT JOIN creneauxValides as v ON t.heureDebut<=v.debut AND t.heureFin>=v.fin " . $condition . " GROUP BY date,idCreneauHoraire";
    $SQL = "SELECT e1.date, e1.debut time_start, e1.fin time_end, e1.capacite, e2.nbPersonnes reservations, (e1.capacite - if(e2.nbPersonnes is null,0,e2.nbPersonnes)) as remaining_capacite FROM (" . $SQLExplode1 . ") as e1 LEFT JOIN (" . $SQLExplode2 . ") as e2 ON e1.idLieu = e2.idLieu AND e1.date = e2.date AND e1.idCreneauHoraire = e2.idCreneauHoraire ORDER BY date ASC, time_start ASC;";
    $params = array("id_place" => $id_place, "date_debut" => $date_debut, "date_fin" => $date_fin);
    return parcoursRs(SQLSelect($SQL, $params));
}
/**
 * Récupère le nombre de places disponibles pour un lieu donné, un jour donné, entre heure_debut et heure_fin
 * @param int id_place
 * @param string date (yyyy-mm-dd)
 * @param string heure_debut (hh:mm)
 * @param string heure_fin (hh:mm)
 */
function get_capacite_restante_creneau($id_place, $date, $heure_debut, $heure_fin)
{
    $condition = "WHERE t.idLieu = :id_place AND t.date = :date";
    $SQLExplode1 = "SELECT t.id as idCreneauDispo, t.idLieu, t.date, sum(t.capacite) as capacite, v.id as idCreneauHoraire, v.debut, v.fin FROM creneauxDispo as t LEFT JOIN creneauxValides as v ON t.heureDebut<=v.debut AND t.heureFin>=v.fin " . $condition . " GROUP BY idCreneauHoraire";
    $SQLExplode2 = "SELECT t.idLieu, t.date, sum(t.nbPersonnes) as nbPersonnes, v.id as idCreneauHoraire, v.debut, v.fin FROM reservations as t LEFT JOIN creneauxValides as v ON t.heureDebut<=v.debut AND t.heureFin>=v.fin " . $condition . " GROUP BY idCreneauHoraire";
    $SQL = "SELECT min(if(capaciteRestante is null,0,capaciteRestante)) as capacite FROM creneauxValides v LEFT JOIN ";
    $SQL .= "(SELECT e1.idCreneauHoraire, (e1.capacite - if(e2.nbPersonnes is null,0,e2.nbPersonnes)) as capaciteRestante FROM (" . $SQLExplode1 . ") as e1 LEFT JOIN (" . $SQLExplode2 . ") as e2 ON e1.idLieu = e2.idLieu AND e1.date = e2.date AND e1.idCreneauHoraire = e2.idCreneauHoraire) calc ";
    $SQL .= "ON calc.idCreneauHoraire = v.id ";
    $SQL .= "WHERE v.debut>=:heure_debut AND v.fin <=:heure_fin";
    $params = array("id_place" => $id_place, "date" => $date, "heure_debut" => $heure_debut, "heure_fin" => $heure_fin);
    return SQLGetChamp($SQL, $params);
}
// ============= SPORTS ===========

/**
 * Retourne la liste de tous les sports avec leur id, nom, logo
 * @return array
 */
function get_all_sports()
{
    $SQL = "SELECT id, nom, logo FROM sports";
    $params = array();
    return parcoursRs(SQLSelect($SQL, $params));
}