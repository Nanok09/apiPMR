<?php //créer une fonction qui a une adresse renvoie les coordonnées associées

//------------------------FIRST TRY : ON EVALUE LA CONCU TAVU ----------------------------------
/*
function get_current_user_location() {
    // pour l'instant ça retourne un certain endroit à villeneuve d'ascq 
    return [50.6232523,3.1442651];
}

function getXmlCoordsFromAdress($address)
{
$coords=array();
$user_region = 'FR';
$region_url = "&region=".$user_region; // pour l'instant on suppose que l'utilisateur est en france
$base_url="http://maps.googleapis.com/maps/api/geocode/xml?";
// ajouter &region=FR si ambiguit? (lieu de la requete pris par d?faut)
$request_url = $base_url . "address=" . urlencode($address).'&sensor=false'.$region_url;
$xml = simplexml_load_file($request_url) or die("url not loading");
print_r($xml);
$coords['lat']=$coords['lon']='';
$coords['status'] = $xml->status ;
if($coords['status']=='OK')
{
 $coords['lat'] = $xml->result->geometry->location->lat ;
 $coords['lon'] = $xml->result->geometry->location->lng ;
}
return $coords;
}
//test
$coords=getXmlCoordsFromAdress("22 rue rambuteau, 75003 PARIS, france");
echo $coords['status']." ".$coords['lat']." ".$coords['lon'];

*/







//---------------------------------------------GOOGLE API (NOT WORKING, NEED A TOKEN)-----------------
/*
function get_coordinates_json_with_google_API() {
    $url='http://maps.googleapis.com/maps/api/geocode/json?address=2+bis+avenue+Foch75116+Paris+FR&sensor=false';
    $source = file_get_contents($url);
    $obj = json_decode($source);
    var_dump($source);
    //$LATITUDE = $obj->results[0]->geometry->location->lat;
    //echo $LATITUDE;

function get_coordinates_with_Google_API($address)
{
$coords=array();
$user_region = 'FR';
$region_url = "&region=".$user_region; // pour l'instant on suppose que l'utilisateur est en france
$base_url="http://maps.googleapis.com/maps/api/geocode/xml?";
// ajouter &region=FR si ambiguit? (lieu de la requete pris par d?faut)
$request_url = $base_url . "address=" . urlencode($address).'&sensor=false'.$region_url;
$xml = simplexml_load_file($request_url) or die("url not loading");
print_r($xml);
$coords['lat']=$coords['lon']='';
$coords['status'] = $xml->status ;
if($coords['status']=='OK')
{
 $coords['lat'] = $xml->result->geometry->location->lat ;
 $coords['lon'] = $xml->result->geometry->location->lng ;
}
return $coords;
}
*/
// Exemple de réponse de l'API GOOGLE:
/*'{
    "error_message" : "You must use an API key to authenticate each request to Google Maps Platform APIs. For additional information, please refer to http://g.co/dev/maps-no-account",
    "results" : [],
    "status" : "REQUEST_DENIED"
 }*/


//test
//$coords=get_coordinates("22 rue rambuteau, 75003 PARIS, france");
//echo $coords['status']." ".$coords['lat']." ".$coords['lon'];

//---------------------------FUNCTIONS TO BE USED : USING MAPBOX API---------------------------------
    
$public_token = 'pk.eyJ1IjoiYXlwMzAzIiwiYSI6ImNrbjhueDA1aDB6dGEyeG54cnNiMXU5enIifQ.xF0Hdno28id2nLnF-rqg2w';

function get_current_user_location() {


    return array(3.1442651,50.6232523); // /!\ ATTENTION : les coordonnées sont dans  l'autre sens par rapport a Google maps
}






//pour l'instant calculatebbox n'est pas fonctionnelle...

function calculate_bbox(float $bbox_param,array $localisation) {
    // pour l'instant cette fonction n'est pas utile en l'état mais ça pourrait changer 
    //$bbox_array = array($localisation[0]-$bbox_param,$localisation[1]-$bbox_param,$localisation[0]+$bbox_param,$localisation[1]+$bbox_param);
    //return implode(",",$bbox_array);
    return '';
}


// cette fonction renvoie un tableau de couples de coordonnées correspondant à l'adresse $adresse et prends des arguments optionels

// dans mapbox le c'est dans l'ordre : array(longitude,latitude)
// sinon pour le reste c'est dans l'odre latitude,longitude

function adress_to_coordinates(string $adress, $proximity = array(3.1442651,50.6232523) , float $bbox_param = 1 , int $limit = 10) {
    //Cette fonction fait un appel à l'API de mapbox. On a le droit à 50 000 requêtes gratuites par jour.
    // $proximity donne les coordonnées autour desquelles on veut chercher a priori (par exemple la localisation de l'utilisateur)
    //par défaut on a mis les coordonnées de villeneuve d'ascq
    // bbox stands for 'Bouding box' voir la fonction calculate bbox les plus le parametre est grand plus le secteur de recherche est grand
    //limit détermine le nombre des résultats maximals autorisés si il y a plusieurs match dans une adresse
    global $public_token;
    $url_root= "https://api.mapbox.com/geocoding/v5/mapbox.places/";

    $bbox = calculate_bbox($bbox_param,$proximity);
    $proximity_url = implode(",",$proximity);

     

    $request_url = $url_root . urlencode($adress) . ".json?" . 'proximity='. $proximity_url .'&bbox='. $bbox. '&limit=' .$limit . '&access_token='. $public_token;
    
    // faire la requête à l'api 
    //TODO : on utilise le parametre BBOX pour restreindre le champ des possible à partir de la localisation actuelle
    var_dump($request_url);

    $source = file_get_contents($request_url); //on récupère la donné de l'api 
    $obj = json_decode($source); //on interprête la données reçue sachant que c'est du json



    $plausible_coordinates= array();
    for ($i = 0; $i < count($obj->features); $i++) {
        
        //var_dump($obj->features[0]);
        $coord1 = $obj->features[$i]->geometry->coordinates[1]; // attention à l'ordre 
        $coord2 = $obj->features[$i]->geometry->coordinates[0];
        $i_coord = array($coord1,$coord2);
        $plausible_coordinates[]=$i_coord;
    } //end For
    //($plausible_coordinates);
    return array ($plausible_coordinates,$obj);
}


function address_research($address,array $proximity = array(3.1442651,50.6232523) , float $bbox_param = 1 , int $limit = 10 ) {
    global $public_token;
    $url_root= "https://api.mapbox.com/geocoding/v5/mapbox.places/";

    $bbox = calculate_bbox($bbox_param,$proximity);
    $proximity_url = implode(",",$proximity);

    $request_url = $url_root . urlencode($address) . ".json?" . 'proximity='. $proximity_url .'&bbox='. $bbox. '&limit=' .$limit . '&access_token='. $public_token;
    //var_dump($request_url);
    $source = file_get_contents($request_url);
    $obj = json_decode($source);
    $results = array();
    for ($i = 0; $i < count($obj->features); $i++) {
        $result = array(
            'coordinates' => array('lat' => $obj->features[$i]->geometry->coordinates[1], 'long' => $obj->features[$i]->geometry->coordinates[0]),
            'address' => $obj->features[$i]->place_name
            );
        $results[]=$result;
    }
    return $results;

}


//pour tester 
/*
$test = address_research('Avenue paul langevin');
echo '<h2> Voici ce que retourne la fonction native : </h2>';
var_dump($test);
*/
/*
echo "<h2> Voici ce que renvoie l' API de mapbox : </h2>";
var_dump($test[1]);
*/


// la réponse intéressante est : 49.141262,6.200084

// il semblerait que la fonction marche correctement mais on dirait que l'api inverse longitude et lattitude par rapport à google..