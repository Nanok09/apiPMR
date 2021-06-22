<?php
include_once("maLibSQL.pdo.php"); 
// définit les fonctions SQLSelect, SQLUpdate...


// Users ///////////////////////////////////////////////////

function validerUser($pseudo, $pass){
	$SQL = "SELECT id FROM users WHERE pseudo='$pseudo' AND pass='$pass'";
	if ($id=SQLGetChamp($SQL))
		return getHash($id);
	else return false;
}

function hash2id($hash) {
	$SQL = "SELECT id FROM users WHERE hash='$hash'";
	return SQLGetChamp($SQL); 
}

function hash2pseudo($hash) {
	$SQL = "SELECT pseudo FROM users WHERE hash='$hash'";
	return SQLGetChamp($SQL); 
}

function getUsers(){
	$SQL = "SELECT id,pseudo FROM users";
	return parcoursRs(SQLSelect($SQL));
}

function getUser($idUser){
	$SQL = "SELECT id,pseudo FROM users WHERE id='$idUser'";
	$rs = parcoursRs(SQLSelect($SQL));
	if (count($rs)) return $rs[0]; 
	else return array();
}

function getHash($idUser){
	$SQL = "SELECT hash FROM users WHERE id='$idUser'";
	return SQLGetChamp($SQL);
}

function mkHash($idUser) {
	// génère un (nouveau) hash pour cet user
	// il faudrait ajouter une date d'expiration
	$dataUser = getUser($idUser);
	if (count($dataUser) == 0) return false;
 
	$payload = $dataUser["pseudo"] . date("H:i:s");
	$hash = md5($payload); 
	$SQL = "UPDATE users SET hash='$hash' WHERE id='$idUser'"; 
	SQLUpdate($SQL); 
	return $hash; 
}

function mkUser($pseudo, $pass){
	$SQL = "INSERT INTO users(pseudo,pass) VALUES('$pseudo', '$pass')";
	$idUser = SQLInsert($SQL);
	mkHash($idUser); 
	return $idUser; 
}


function rmUser($idUser) {
	$SQL = "DELETE FROM users WHERE id='$idUser'";
	return SQLDelete($SQL);
}

function chgPassword($idUser,$pass) {
	$SQL = "UPDATE users SET pass='$pass' WHERE id='$idUser'";
	SQLUpdate($SQL);
	return 1; 
}

// Articles ///////////////////////////////////////////////////

function getRecipes(){
	$SQL = "SELECT r.id, r.name, r.time FROM recipes r INNER JOIN users u ON r.idAuthorRecipe = u.id";
	return parcoursRs(SQLSelect($SQL));
}

function getRecipe($id,$idUser=false){
	$SQL = "SELECT * FROM recipe WHERE id='$id'";
	if ($idUser)
		$SQL .= " AND idAuthorRecipe='$idUser'";
	$rs = parcoursRs(SQLSelect($SQL));
	if (count($rs)) return $rs[0]; 
	else return array();
}

function isUserCreatorOfRecipe($idUser,$idRecipe) {
	$SQL = "SELECT id FROM recipe WHERE id='$idRecipe'";
	$SQL .= " AND idAuthorRecipe='$idUser'";
	return SQLGetChamp($SQL); 
}

function getRecipesUser($idUser){
	$SQL = "SELECT * FROM recipe WHERE idAuthorRecipe='$idUser'";
	return parcoursRs(SQLSelect($SQL));
}

function mkRecipe($idUser, $name, $time, $nbPpl, $faved){
	$SQL = "INSERT INTO recipe(idAuthorRecipe, name, time, numberOfPeople, faved) 
			VALUES('$idUser', '$name', '$time', '$nbPpl', '$faved')";
	return SQLInsert($SQL);
}

function rmRecipe($id, $idUser=false) {
	$SQL = "DELETE FROM recipe WHERE id='$id'";
	if ($idUser) $SQL .= " AND idAuthorRecipe='$idUser'";
	return SQLDelete($SQL);
}

function chgRecipe($id,$name,$time,$numberOfPeople,$faved, $idUser=false) {
	$SQL = "UPDATE recipe SET name='$name',time='$time',numberOfPeople='$numberOfPeople',faved='$faved'";
 	$SQL.= "WHERE id='$id'";
	if ($idUser) $SQL .= " AND idAuthorRecipe='$idUser'";
	SQLUpdate($SQL);
	return 1; 
	// return SQLUpdate() pose souci si il n'y a pas modif de titre
	// SQLUpdate renvoie alors 0 ! 
}

// Paragraphes ///////////////////////////////////////////////////

function getSteps($id) {
	$SQL = "SELECT * FROM step WHERE idRecipe='$id'";
	return parcoursRs(SQLSelect($SQL));
}

function getStep($id, $idRecipe) {
	$SQL = "SELECT * FROM step WHERE stepNumber='$id'";
	$SQL .= " AND idRecipe='$idRecipe'";

	$rs = parcoursRs(SQLSelect($SQL));
	if (count($rs)) return $rs[0]; 
	else return array();
}


function rmStep($id,$idRecipe) {
	$SQL = "DELETE FROM step WHERE stepNumber='$id' AND idRecipe='$idRecipe'";
	return SQLDelete($SQL);
}

function mkSteps($idRecipe,$stepnumber, $description,$descriptionShort=false){
	if (!$descriptionShort)
		$SQL = "INSERT INTO step(idRecipe,stepNumber,description,descriptionShort) 
				VALUES('$idRecipe','$stepnumber', '$description', '$description')";
	else 
		$SQL = "INSERT INTO step(idRecipe,stepNumber,description, descriptionShort) 
				VALUES('$idRecipe','$stepnumber','$description', '$descriptionShort')";
	return SQLInsert($SQL);
}

function chgStep($id,$description,$descriptionShort,$idRecipe) {
	$SQL = "UPDATE step SET description='$description', descriptionShort='$descriptionShort' WHERE stepNumber='$id'";
	$SQL .=  " AND idRecipe='$idRecipe'";
	SQLUpdate($SQL);
	return 1; 
}

function getIngredients(){
	$SQL = "SELECT * FROM ingredient";
	return parcoursRs(SQLSelect($SQL));
}
function getIngredient($id){
	$SQL = "SELECT * FROM ingredient WHERE id='$id'";
	return parcoursRs(SQLSelect($SQL));
}

function mkIngredient($name){
	$SQL = "INSERT INTO ingredient(name) VALUES('$name')";
	$idIngredient = SQLInsert($SQL);
	return $idIngredient;
}
function chgIngredient($id,$name) {
	$SQL = "UPDATE ingredient SET name='$name' WHERE id='$id'";
	SQLUpdate($SQL);
	return 1;
}
function rmIngredient($id) {
	$SQL = "DELETE FROM ingredient WHERE id='$id'";
	return SQLDelete($SQL);
}

function getRecipeQuantity($idIngredient, $idRecipe){
	$SQL = "SELECT rq.unit,rq.quantity,r.name, i.name
	FROM ingredient AS i  INNER JOIN recipe_quantity AS rq ON rq.idIngredient=i.id INNER JOIN recipe as r ON rq.idRecipe=r.id 
	WHERE rq.idIngredient='$idIngredient' AND rq.idRecipe='$idRecipe'";

	$rs = parcoursRs(SQLSelect($SQL));
	if (count($rs)) return $rs[0];
}
function getRecipeQuantities($idRecipe){
	$SQL = "SELECT rq.unit,rq.quantity,r.name,i.name
	FROM ingredient AS i  INNER JOIN recipe_quantity AS rq ON rq.idIngredient=i.id INNER JOIN recipe as r ON rq.idRecipe=r.id 
	WHERE rq.idRecipe='$idRecipe'";
	return parcoursRs(SQLSelect($SQL));}

function mkRecipeQuantity($idIngredient, $idRecipe, $unit, $quantity){
	$SQL = "INSERT INTO recipe_quantity(idIngredient,idRecipe,unit, quantity) 
				VALUES('$idIngredient','$idRecipe','$unit', '$quantity')";
	return SQLInsert($SQL);
}
function chgRecipeQuantity($idIngredient, $idRecipe, $unit, $quantity){
	$SQL = "UPDATE recipe_quantity SET quantity='$quantity', unit='$unit' WHERE idIngredient='$idIngredient'";
	$SQL .=  " AND idRecipe='$idRecipe'";
	SQLUpdate($SQL);
	return 1;
}
function rmRecipeQuantity($idIngredient, $idRecipe){
	$SQL = "DELETE FROM recipe_quantity WHERE idIngredient='$idIngredient' AND idRecipe='$idRecipe'";
	return SQLDelete($SQL);
}


function updateOrdreParagraphe($ordre,$id,$idArticle){
	$SQL = "SELECT id FROM paragraphes WHERE ordre = '$ordre' AND idArticle='$idArticle'"; 
	if (SQLGetChamp($SQL)) {
		// Il peut s'agir d'un numéro d'ordre qui est déjà utilisé
		// On va décaler les ordres des paragraphes existants après
		// TODO: SEULEMENT si c'est le CAS (doit être inutile ?)
		$SQL = "UPDATE paragraphes SET ordre = ordre+1 
					WHERE ordre >= '$ordre' AND idArticle='$idArticle'"; 
		SQLUpdate($SQL);
	}

	// avant de changer 
	// l'ordre du paragraphe concerné 
	$SQL = "UPDATE paragraphes SET ordre = '$ordre' WHERE id='$id' AND idArticle='$idArticle'";
	SQLUpdate($SQL);
	return 1; 
}



?>
