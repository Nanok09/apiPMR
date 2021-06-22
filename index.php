<?php
include_once("libs/maLibUtils.php");
include_once("libs/modele.php");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$data = array("version"=>1.1);

// Routes : /api/...

$method = $_SERVER["REQUEST_METHOD"];
debug("method",$method);

if ($method == "OPTIONS") die("ok - OPTIONS");

$data["success"] = false;
$data["status"] = 400; 

// Verif autorisation : il faut un hash
// Il peut être dans le header, ou dans la chaîne de requête

$connected = false; 

if (!($hash = valider("hash"))) 
	$hash = valider("HTTP_HASH","SERVER");

if($hash) {
	// Il y a un hash, il doit être correct...
	if ($connectedId = hash2id($hash)) $connected = true; 
	else {
		// non connecté - peut-être est-ce POST vers /autenticate...
		$method = "error";
		$data["status"] = 403;
	}

}

if (valider("request")) {
	$requestParts = explode('/',$_REQUEST["request"]);
	debug("rewrite-request" ,$_REQUEST["request"] );
	debug("#parts", count($requestParts) ); 

	$entite1 = false;
	$idEntite1 = false;
	$entite2 = false; 
	$idEntite2 = false; 

	if (count($requestParts) >0) {
		$entite1 = $requestParts[0];
		debug("entite1",$entite1); 
	} 

	if (count($requestParts) >1) {	
		if (is_id($requestParts[1])) {
			$idEntite1 = intval($requestParts[1]);
			debug("idEntite1",$idEntite1); 
		} else {
			// erreur !
			$method = "error";
			$data["status"] = 400; 
		}
	}

	if (count($requestParts) >2) {
		$entite2 = $requestParts[2];
		debug("entite2",$entite2); 
	}

	if (count($requestParts) >3) {
		if (is_id($requestParts[3])) {
			$idEntite2 = intval($requestParts[3]);
			debug("idEntite2",$idEntite2); 
		} else {
			// erreur !
			$method = "error";
			$data["status"] = 400;
		}

	}  

// TODO: en cas d'erreur : changer $method pour préparer un case 'erreur'

	$action = $method; 
	if ($entite1) $action .= "_$entite1";
	if ($entite2) $action .= "_$entite2";
 
	debug("action", $action);

	var_dump($idEntite2);
	if ($action == "POST_authenticate") {
		if ($user = valider("user"))
		if ($password = valider("password")) {
			if ($hash = validerUser($user, $password)) {
				$data["hash"] = $hash;
				$data["success"] = true;
				$data["status"] = 202;
			} else {
				// connexion échouée
				$data["status"] = 401;
			}
		}
	}
	elseif ($connected)
	{
		// On connaît $connectedId
		switch ($action) {

			case 'GET_users' :			
				if ($idEntite1) {
					// GET /api/users/<id>
					$data["user"] = getUser($idEntite1);
					$data["success"] = true;
					$data["status"] = 200; 
				} 
				else {
					// GET /api/users
					$data["users"] = getUsers();
					$data["success"] = true;
					$data["status"] = 200;
				}
			break; 

			case 'GET_users_recipes' :
				if ($idEntite1)
				if ($idEntite2) {
					// GET /api/users/<id>/recipes/<id>
					$data["recipe"] = getRecipe($idEntite2, $idEntite1);
					$data["success"] = true;
					$data["status"] = 200;
				} else {
					// GET /api/users/<id>/recipes
					$data["recipe"] = getRecipesUser($idEntite1);
					$data["success"] = true;
					$data["status"] = 200;
				}
			break;

			case 'GET_recipes' :
				if ($idEntite1){
					// GET /api/recipes/<id>
					// TODO : vérifier user ?
					$data["recipe"] = getRecipe($idEntite1);
					$data["success"] = true;
					$data["status"] = 200;
				} else {
					// GET /api/recipes
					// Les listes de l'utilisateur connecté
					$data["recipe"] = getRecipesUser($connectedId);
					$data["success"] = true;
					$data["status"] = 200; 
				}
			break;

			case 'GET_recipes_steps' :
				if ($idEntite1)
				if ($idEntite2) {
					// GET /api/recipes/<id>/steps/<id>
					$data["step"] = getStep($idEntite2, $idEntite1);
					$data["success"] = true;
					$data["status"] = 200;
				} else {
					// GET /api/recipes/<id>/steps
					$data["step"] = getSteps($idEntite1);
					$data["success"] = true;
					$data["status"] = 200;		 
				}
			break;

			case 'GET_ingredients':

				if($idEntite1){
					// GET /api/ingredients/1
					$data["ingredient"] = getIngredient($idEntite1);
					$data["success"] = true;
					$data["status"] = 200;
				}
				else{
					// GET /api/ingredients
					$data["ingredient"] = getIngredients();
					$data["success"] = true;
					$data["status"] = 200;
				}


			break;



			case 'GET_recipes_ingredients':


			break;

			case 'POST_users' : 
				// POST /api/users?pseudo=&pass=...
				if ($pseudo = valider("user"))
				if ($pass = valider("password")) {
					$id = mkUser($pseudo, $pass); 
					$data["user"] = getUser($id);
					$data["success"] = true;
					$data["status"] = 201;
				}
			break; 

			case 'POST_users_recipes' :
				// POST /api/users/<id>/recipes?name=...
				if ($idEntite1)
				if ($name = valider("name"))
				if($time  = valider("time"))
				if($nbPpl = valider("numberOfPeople")){
					$faved = valider("faved");
					if ($connectedId != $idEntite1) {
						$data["status"] = 403;
					} else {
						$id = mkRecipe($idEntite1, $name, $time, $nbPpl, $faved);
						$data["recipe"] = getRecipe($id);
						$data["success"] = true;
						$data["status"] = 201;
					}
				}
			break;
			case 'POST_recipes' :
				// POST /api/recipes?name=...
				if ($name = valider("name"))
				if($time  = valider("time"))
				if($nbPpl = valider("numberOfPeople"))
				{
					$faved = valider("faved");
					$id = mkRecipe($connectedId, $name, $time, $nbPpl, $faved);
					$data["recipe"] = getRecipe($id);
					$data["success"] = true;
					$data["status"] = 201;
				}
			break;


			case 'POST_recipes_steps' :
				// POST /api/recipes/<id>/steps/<idStep>?description=...
				if ($idEntite1)
				if($idEntite2)
				if ($description = valider("description"))
				if($descriptionShort  = valider("descriptionShort"))
				{
					if (!isUserCreatorOfRecipe($connectedId,$idEntite1)) {
						$data["status"] = 403;
					} else {
						$id = mkSteps($idEntite1, $idEntite2, $description, $descriptionShort);
						$data["step"] = getStep($idEntite2,$idEntite1);
						$data["success"] = true; 
						$data["status"] = 201;
					}
				}
			break;

			case 'POST_ingredients':

				// POST /api/users?pseudo=&pass=...
				if ($name = valider("name"))
					{
						$id = mkIngredient($name);
						$data["ingredient"] = getIngredient($id);
						$data["success"] = true;
						$data["status"] = 201;
					}

			break;



			case 'PUT_authenticate' : 
				// régénère un hash ? 
				$data["hash"] = mkHash($connectedId); 
				$data["success"] = true; 
				$data["status"] = 200;
			break; 

			case 'PUT_users' :
				// PUT  /api/users/?pass=...
				if ($connectedId)
				if ($pass = valider("password")) {
					if (chgPassword($connectedId,$pass)) {
						$data["user"] = getUser($connectedId);
						$data["success"] = true; 
						$data["status"] = 200;
					} else {
						// erreur 
					}
				}
			break; 

//			case 'PUT_users_articles' :  //TODO
//				// PUT /api/users/<id>/articles/<id>?titre=...
//				if ($idEntite1)
//				if ($idEntite2)
//				if ($titre = valider("titre")) {
//					if ($connectedId != $idEntite1) {
//						$data["status"] = 403;
//					} else {
//						if (chgTitreArticle($idEntite2,$titre,$idEntite1)) {
//							$data["article"] = getArticle($idEntite2);
//							$data["success"] = true;
//							$data["status"] = 200;
//						} else {
//							// erreur
//						}
//					}
//				}
//			break;

			case 'PUT_recipes' :
				// PUT /api/recipes/<id>?name=...
				if ($idEntite1)
				if ($name = valider("name"))
				if($time  = valider("time"))
				if($nbPpl = valider("numberOfPeople"))
				{
					$faved = valider("faved");
					if (!isUserCreatorOfRecipe($connectedId,$idEntite1)) {
						$data["status"] = 403;
					} else {
						if (chgRecipe($idEntite1,$name,$time,$nbPpl,$faved,$connectedId)) {
							$data["recipe"] = getRecipe($idEntite1);
							$data["success"] = true; 
							$data["status"] = 200;
						} else {
							// erreur
						}
					}
				}
			break; 

			case 'PUT_recipes_steps' :
				// PUT /api/recipes/<id>/steps/<id>?description=...
				if ($idEntite1)
				if ($idEntite2)
				if ($description = valider("description"))
				if($descriptionShort  = valider("descriptionShort"))
				{
					if (!isUserCreatorOfRecipe($connectedId,$idEntite1)) {
						$data["status"] = 403;
					} else {
						if (chgStep($idEntite2,$description,$descriptionShort,$idEntite1)) {
							$data["steps"] = getStep($idEntite2,$idEntite1);
							$data["success"] = true; 
							$data["status"] = 200;
						} else {
							// erreur
						}
					}
				}
			break;

			case 'PUT_ingredients':

				// PUT /api/ingredients/1?name="salade"
				if($idEntite1)
				if ($name = valider("name"))
				{
					if (chgIngredient($idEntite1, $name)){
						$data["ingredient"] = getIngredient($idEntite1);
						$data["success"] = true;
						$data["status"] = 201;
					}else{
						//erreur
					}

				}

			break;



			case 'DELETE_users' : 
				// DELETE /api/users/<id> 
				if ($idEntite1) {
					if ($connectedId != $idEntite1) {
						$data["status"] = 403;
					} else {
						if (rmUser($idEntite1)) {
							$data["success"] = true;
							$data["status"] = 200;
						} else {
							// erreur 
						} 
					}
				}
			break; 

			case 'DELETE_users_articles' : //TODO
				// DELETE /api/users/<id>/articles/<id>
				if ($idEntite1)
				if ($idEntite2) {
					if ($connectedId != $idEntite1) {
						$data["status"] = 403;
					} else {
						if (rmArticle($idEntite2, $idEntite1)) {				
							$data["success"] = true;
							$data["status"] = 200; 
						} else {
							// erreur 
						}
					}
				}
			break; 

			case 'DELETE_recipes' :
				// DELETE /api/recipes/<id>
				if ($idEntite1) {
					if (!isUserCreatorOfRecipe($connectedId,$idEntite1)) {
						$data["status"] = 403;
					} else {
						if (rmRecipe($idEntite1, $connectedId)) {
							$data["success"] = true;
							$data["status"] = 200; 
						} else {
							// erreur 
						}
					}
				}
			break; 

			case 'DELETE_recipes_steps' :
				// DELETE /api/recipes/<id>/steps/<id>
				if ($idEntite1)
				if ($idEntite2) {
					if (!isUserCreatorOfRecipe($connectedId,$idEntite1)) {
						$data["status"] = 403;
					} else {
						if (rmStep($idEntite2, $idEntite1)) {
							$data["success"] = true;
							$data["status"] = 200;  
						} else {
							// erreur 
						}
					}
				}
			break;

			case 'DELETE_ingredients':
				// DELETE /api/ingredients/<id>

				if($idEntite1){
					if(rmIngredient($idEntite1)){
						$data["success"] = true;
						$data["status"] = 200;
					}
				}



			break;
		} // switch(action)
	} //connected
}

switch($data["status"]) {
	case 200: header("HTTP/1.0 200 OK");	break;
	case 201: header("HTTP/1.0 201 Created");	break; 
	case 202: header("HTTP/1.0 202 Accepted");	break;
	case 204: header("HTTP/1.0 204 No Content");	break;
	case 400: header("HTTP/1.0 400 Bad Request");	break; 
	case 401: header("HTTP/1.0 401 Unauthorized");	break; 
	case 403: header("HTTP/1.0 403 Forbidden");	break; 
	case 404: header("HTTP/1.0 404 Not Found");		break;
	default: header("HTTP/1.0 200 OK");
		
}

echo json_encode($data);

?>
