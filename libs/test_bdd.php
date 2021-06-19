<?php
//======================== CE FICHIER SERA A SUPPRIMMER DANS LA VERSION FINALE=========================
include_once("modele.php");

//hotel de ville lat, long = 48.856614,2.3522219
/*
$raw= 'Avenue paul langevin';

//$result = get_note_place($id_place);

//var_dump($result);
$id_auteur = 1;
$id_destinataire = 4;

$result = add_message_to_conv(1,4,5,'ça faisait longtemps! :) ');



$coded = urlencode($raw);
echo '<h2> Version codée : </h2>';
var_dump($coded);
echo 'echo <h2> Version décodée : </h2>';
var_dump(urldecode($coded));
date('d-m-Y h:i:s')
*/
$last_msg_info = get_messages_in_conv(1);
var_dump($last_msg_info);