<?php
include_once "libs/modele.php";
include_once "libs/libUtils.php";
include_once "libs/libForms.php";
include_once "libs/libSecurisation.php";

securiser("?view=accueil"); //vérifie si l'utilisateur est connecté et le renvoie à l'accueil sinon
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=mesTerrains");
    die("");
}

$id_user = valider("id_user", "SESSION");
$mesTerrains = get_places_created_by($id_user);
$photos = get_photos();

?>
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
<script src="js/geolocalisation.js"></script>
<style>
h5+p:hover {
    cursor: pointer;
}

#map {
    width: 400px;
    height: 400px;
    display: none;
}
</style>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script>
<script>
var terrains = <?php echo json_encode($mesTerrains); ?>;
var photos = <?php echo json_encode($photos); ?>;
$(document).ready(function() {


    console.log(terrains);
    //console.log(photos);


    //On affiche le menu qui permet de choisir d'éditer un terrain
    //ou d'en créer un nouveau

    var i;
    for (i = 0; i < terrains.length; i++) {
        $("#liste_terrains select").append(
            $("<option>")
            .html(terrains[i].nom)
            .data(terrains[i])
        );
    }
    $("#liste_terrains select").append($("<option>").html('Ajouter un nouveau terrain'))


    //On affiche l'édition ou la création en fonction du choix
    //de l'utilisateur.

    var selected = $("#liste_terrains select option:selected");

    //bouton gérer créneaux
    console.log("selected : "+selected.data().id);
    $("#gerer_creneaux").attr('onclick', 'window.location.href=\'index.php?view=ajouterCreneau&id=' +
        selected.data().id + '\';');
    //console.log(selected.data());
    if (selected.html() == 'Ajouter un nouveau terrain') {
        print_new_place_creation();
    } else {
        print_place_edition(selected.data());
    }

    $("#liste_terrains select").change(function() {
        selected = $("#liste_terrains select option:selected");
        $("#gerer_creneaux").attr('onclick', 'window.location.href=\'index.php?view=ajouterCreneau&id=' +
            selected.data().id + '\';');
        if (selected.html() == 'Ajouter un nouveau terrain') {
            print_new_place_creation();
        } else {
            print_place_edition(selected.data());
        }
    })

    //On passe en mode édition lors du click sur un paragraphe
    $(document).on("click", "p", function() {
        var contenu = this.innerHTML;
        if (this.id == "current_address") return;
        console.log(contenu);
        if (this.id == "description") {
            $(this).replaceWith(
                "<textarea name='description'>" + contenu + "</textarea>"
            );
        } else if (this.id == "capacite" || this.id == "prix") {
            $(this).replaceWith(
                "<input name='" + this.id + "' type='number' value=\"" + contenu + "\" />"
            );
        } else {
            $(this).replaceWith(
                "<input name='" + this.id + "' type='text' value=\"" + contenu + "\" />"
            );
        }
    });

    $(document).on("keyup", "#adresse", function() {
        var adress = $("#adresse")[0].value;
        console.log(adress);
        if (adress) {
            get_coord(adress);
        }
    });




    //création form photo
    $("#ajout_photo").append(
        "<input class='m-2' style='background-color: #153455; color: #fdedcf; text-align: center;' type='hidden' value='" +
        selected.data().id + "' name='id_place'/>");
    $("#ajout_photo").append("<input class='m-2' type='file' name='fileToUpload'/></br>");
    $("#ajout_photo").append(
        "<input class='m-2' style='background-color: #153455; color: #fdedcf; text-align: center;' id='ajouter_photo' type='submit' name='action' value='ajouter photo'/>"
    );


});

function print_choix(coord) {
    add_markers(coord);
    $("#choice").empty();
    for (let i = 0; i < coord.length; i++) {
        var str = JSON.stringify(coord[i]);
        console.log(str);
        $("#choice").append(
            $("<option>").html(coord[i].address)
            .attr('value', str)
        )
    }
    $("#choice").css('display', 'inline');
}

//recherches coordonnée, requete à notre API
function get_coord(adresse) {
    //console.log(geolocation);
    $.ajax({
        type: "POST",
        url: "libs/api.php",
        data: {
            'action': 'address_research',
            'address': adresse
        },
        error: function() {
            console.log("Error");
        },
        success: function(oRep) {
            console.log("réponse requête :" + oRep);
            print_choix(JSON.parse(oRep).data);
        }
    })
}

//Structure html de l'édition d'un terrain
function print_place_edition(terrain) {
    $("#ajout_creneaux").css('display', 'block');
    $("#ajout_photo").css('display', 'block');
    $("#edition").empty();
    $("#creation_place").empty();
    // $("#edition").append($("<div class='container''>").append($("<div class='row justify-content-center''>").append($("<h5 class='body-color-blue'>").html("Photos : "))));
    // $("#edition").append($("<div class='container''>").append($("<div class='row justify-content-center''>").append($("<div class='col-10 bg-custom-beige custom-rounded-corners m-5'>").append($("<h3 class='body-color-blue m-5 text-center'>").html("Photos")))));

    $("#edition").append($("<h3 class='body-color-blue m-5 text-center'>").html("Photos"));

    $("#map").css('display', 'none');
    for (let i = 0; i < photos.length; i++) {
        if (photos[i].idLieu == terrain.id) {
            console.log(photos[i]);

            $("#edition").append("<img style='width : 300px;display: block;margin: auto;' src=\"images/terrains/" +
                photos[i].nomFichier + "\"/>");
        }
    }
    $("#edition").append($("<h3 class='body-color-blue m-5 text-center'>").html(
        "Informations à propos de mon terrain"));
    $("#edition").append($("<p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($("<strong>")
        .html("Nom")));
    $("#edition").append($("<p class='body-color-blue' style='display: inline-block' id='nom' name='nom'>").html(terrain
        .nom));
    $("#edition").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Description")));
    if (terrain.description == '') {
        $("#edition").append($(
                "<p class='body-color-blue' style='display: inline-block' id='description' name='description'>")
            .html('Pas de description'));
    } else {
        $("#edition").append($(
                "<p class='body-color-blue' style='display: inline-block' id='description' name='description'>")
            .html(terrain.description));
    }
    $("#edition").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Adresse (non modifiable)")));
    $("#edition").append($("<p class='body-color-blue' style='display: inline-block' id='current_address'>").html(
        terrain.adresse));
    $("#edition").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Capacité (nombre de personnes)")));
    $("#edition").append($("<p class='body-color-blue' style='display: inline-block' id='capacite' name='capacite'>")
        .html(terrain.capacite));
    $("#edition").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("prix (horaire)")));
    $("#edition").append($("<p class='body-color-blue' style='display: inline-block' id='prix' name='prix' >").html(
        terrain.prix));
    $("#edition").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("sport : ")));
    $("#edition").append($("<p class='body-color-blue' style='display: inline-block' id='sport' name='sport'>").html(
        terrain.sport));
    $("#edition").append("<input name='action' type='hidden' value='modif_place'>");
    $("#edition").append("<input name='id_place' type='hidden' value='" + terrain.id + "'>");
    $("#edition").append("</br>");
    // $("#edition").append("</br><input type='button' value='Enregistrer modifications' onClick='submit();'>");
    $("#edition").append(
        "</br><div class='row justify-content-center''><input id='submitForm' type='button' name='action' value='Enregistrer' class='btn col-3 mb-5 mt-3 custom-rounded-corners' style='background-color: #153455; color: #fdedcf;' onClick='submit();'></div>"
    );

}
/*
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-10 bg-custom-beige custom-rounded-corners m-5">
                <h3 class="body-color-blue m-5 text-center">Mes Photos :</h3>
                <h3 class="body-color-blue m-5 text-center">Informations à propos de mon terrain</h3>
                <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Nom</strong></p>
                <p class="body-color-blue" style="display: inline-block">terrain 1 fjandsfna</p>
                <br/>
                <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Description</strong></p>
                <p class="body-color-blue" style="display: inline-block">blablalba</p>
                <br/>
                <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Adresse</strong></p>
                <p class="body-color-blue" style="display: inline-block">blablalba</p>
                <br/>
                <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Capacité</strong></p>
                <p class="body-color-blue" style="display: inline-block">blablalba</p>
                <br/>
                <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Prix</strong></p>
                <p class="body-color-blue" style="display: inline-block">blablalba</p>
                <br/>
                <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Sport</strong></p>
                <p class="body-color-blue" style="display: inline-block">blablalba</p>
                <br/>
                <div class="row justify-content-center">
                    <input id="submitForm" type="submit" name="action" value="Enregistrer les données" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                           style="background-color: #153455; color: #fdedcf;">
                </div>
                <h3 class="body-color-blue m-5 text-center">Ajouter une photo</h3>
                <div class="row justify-content-center">
                    <input id="submitForm" type="submit" name="action" value="Choisir un fichier" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                           style="background-color: #153455; color: #fdedcf;">
                </div>
                <div class="row justify-content-center">
                    <input id="submitForm" type="submit" name="action" value="Ajouter photo" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                           style="background-color: #153455; color: #fdedcf;">
                </div>
                <div class="row justify-content-center">
                    <input id="submitForm" type="submit" name="action" value="Gérer mes créneaux" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                           style="background-color: #153455; color: #fdedcf;">
                </div>
            </div>
        </div>
    </div>
*/

//Structure html de la création d'un terrain
function print_new_place_creation() {
    $("#creation_place").empty();
    $("#edition").empty();
    $("#ajout_creneaux").css('display', 'none');
    $("#ajout_photo").css('display', 'none');
    $("#creation_place").append($("<h2 class='body-color-blue m-5 text-center'>").html(
        "Création d'un nouveau terrain"));
    $("#creation_place").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Nom :")));
    $("#creation_place").append(
        "<input class='crea' style='width: 30%' id='nom' type='text' name='nom' placeholder='nom du nouveau terrain'/></br>"
    );
    $("#creation_place").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Adresse :")));
    $("#creation_place").append(
        "<input class='crea' style='width: 50%' id='adresse' type='text' placeholder='1500 Avenue Médicis, Paris'/></br>"
    );
    // $("#creation_place").append("<select class='crea' id='choice' name='coord' style='display:none;'></select></br>");
    $("#creation_place").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Sport :")));
    $("#creation_place").append(
        "<input class='crea' id='sport' type='text' name='sport' placeholder='exemple : tennis'/></br>");
    $("#creation_place").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Prix (horaire) :")));
    $("#creation_place").append(
        "<input class='crea' id='prix' type='number' name='prix' placeholder='exemple : 5'/></br>");
    $("#creation_place").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Capacité (nombre de personnes) :")));
    $("#creation_place").append(
        "<input class='crea' id='capacite' type='number' name='capacite' placeholder='exemple : 5'/></br>");
    $("#creation_place").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Type :")));
    $("#creation_place").append(
        "<input id='publique' style='display: inline-block' type='radio' name='type' value=0 checked/>" +
        "<label style='display: inline-block; padding-right: 5px;' class='body-color-blue' for='publique'>Public  </label>"
    );
    $("#creation_place").append("<input id='prive' style='display: inline-block' type='radio' name='type' value=1 />" +
        "<label style='display: inline-block' class='body-color-blue' for='prive'>Privé</label>");

    $("#creation_place").append("</br>");
    $("#creation_place").append($("<br/><p class='ml-5 mr-2 body-color-blue' style='display: inline-block'>").append($(
        "<strong>").html("Description générale :")));
    $("#creation_place").append(
        "<br/><div class='row justify-content-center'><textarea cols='100' class='m-3' id='description' class='crea' name='description'></textarea></div></br>"
    );
    $("#creation_place").append($("<br/><h4 class='ml-5 mr-2 mb-3 body-color-blue text-center'>").append($("<strong>")
        .html("Photo du terrain à importer")));
    $("#creation_place").append(
        "<div class='row justify-content-center'><input id='fileToUpload' type='file' name='fileToUpload'/></div></br>"
    );
    $("#creation_place").append(
        "<div class='row justify-content-center''><input id='creation' name='action' type='submit' value='Créer terrain' class='btn col-3 mb-5 mt-3 custom-rounded-corners' style='background-color: #153455; color: #fdedcf;'/></div>"
    );

}

function add_markers(coord) {
    mapboxgl.accessToken = 'pk.eyJ1IjoiYXlwMzAzIiwiYSI6ImNrbjhueDA1aDB6dGEyeG54cnNiMXU5enIifQ.xF0Hdno28id2nLnF-rqg2w';
    var map = new mapboxgl.Map({
        container: 'map', // container id
        style: 'mapbox://styles/mapbox/streets-v11', // style URL
        center: [3, 47], // starting position [lng, lat]
        zoom: 4 // starting zoom
    });
    $("#map").css('display', 'inline-block');

    var markers = []

    for (let i = 0; i < coord.length; i++) {
        markers.push({
            'type': 'Feature',
            'geometry': {
                'type': 'Point',
                'coordinates': [coord[i].coordinates.long, coord[i].coordinates.lat]
            },
            'properties': {
                'title': coord[i].address
            }
        });
    }


    map.on('load', function() {
        // Add an image to use as a custom marker
        map.loadImage(
            'https://docs.mapbox.com/mapbox-gl-js/assets/custom_marker.png',
            function(error, image) {
                if (error) throw error;
                map.addImage('custom-marker', image);
                // Add a GeoJSON source with 2 points
                map.addSource('points', {
                    'type': 'geojson',
                    'data': {
                        'type': 'FeatureCollection',
                        'features': markers
                    }
                });

                // Add a symbol layer
                map.addLayer({
                    'id': 'points',
                    'type': 'symbol',
                    'source': 'points',
                    'layout': {
                        'icon-image': 'custom-marker',
                        // get the title name from the source's "title" property
                        'text-field': ['get', 'title'],
                        'text-font': [
                            'Open Sans Semibold',
                            'Arial Unicode MS Bold'
                        ],
                        'text-offset': [0, 1.25],
                        'text-anchor': 'top'
                    }
                });
            }
        );
    });
}
</script>

<style>
.wrapper {
    display: flex;
    width: 100%;
    align-items: stretch;
}

#sidebar {
    min-width: 250px;
    max-width: 250px;
    min-height: 100vh;
}

#sidebar.active {
    margin-left: -250px;
}

a[data-toggle="collapse"] {
    position: relative;
}

@media (max-width: 768px) {
    #sidebar {
        margin-left: -250px;
    }

    #sidebar.active {
        margin-left: 0;
    }
}


/*
        ADDITIONAL DEMO STYLE, NOT IMPORTANT TO MAKE THINGS WORK BUT TO MAKE IT A BIT NICER :)
    */
@import "https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700";


body {
    font-family: 'Poppins', sans-serif;
    background: #dcdcdc;
}

p {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1em;
    font-weight: 300;
    line-height: 1.7em;
    color: #999;
}

a,
a:hover,
a:focus {
    color: inherit;
    text-decoration: none;
    transition: all 0.3s;
}

#sidebar {
    /* don't forget to add all the previously mentioned styles here too */
    background: #fdedcf;
    color: #000;
    transition: all 0.3s;
}

#sidebar .sidebar-header {
    padding: 20px;
    background: #fdedcf;
}

#sidebar ul.components {
    padding: 20px 0;
    border-bottom: 1px solid #47748b;
}

#sidebar ul p {
    color: #000;
    padding: 10px;
}

#sidebar ul li a {
    padding: 10px;
    font-size: 1.1em;
    display: block;
}

#sidebar ul li a:hover {
    color: #7386D5;
    background: #fff;
}

#sidebar ul li.active>a,
a[aria-expanded="true"] {
    color: #000;
    background: #fdedcf;
}

ul ul a {
    font-size: 0.9em !important;
    padding-left: 30px !important;
    background: #fdedcf;
}

#content>div:hover {
    cursor: pointer;
    transform: translateY(-5px);
}
</style>



<script>
$(document).ready(function() {

    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').toggleClass('active');
    });

});
</script>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3 class="text-center mt-5 body-color-blue">Mes Terrains</h3>
        </div>
        <div id="liste_terrains" class="text-center">
            <select>
            </select>
        </div>
    </nav>
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="fas fa-align-left"></i>
                    <span>Voir les terrains</span>
                </button>
            </div>
        </nav>


        <div class="container-fluid">
            <div class="row justify-content">

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-6 bg-custom-blue custom-rounded-corners m-5">
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="m-5" id="map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-10 bg-custom-beige custom-rounded-corners m-5">
                            <form id="edition" action="controleur.php" method="post">
                            </form>

                            <form id='creation_place' action='controleur.php' method="POST"
                                enctype="multipart/form-data">
                            </form>

                            <?php
                            if ($msg = valider('msg')) {
                                echo "<span style='color:red'>$msg</span>";
                            }

                            ?>
                            <div class="row justify-content-center">
                                <div class="col-10 bg-custom-grey custom-rounded-corners m-5">
                                    <div class="row justify-content-center">
                                        <form class="mb-4" id="ajout_photo" action="controleur.php" method='post'
                                            style="display:none;" enctype="multipart/form-data">
                                            <h3 class="body-color-blue m-5 text-center">Ajouter une photo</h3>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <input class="btn col-3 my-5 custom-rounded-corners"
                                    style="background-color: #153455; color: #fdedcf;" id="gerer_creneaux" type="button"
                                    value="Gérer mes créneaux" /></br>
                            </div>
                        </div>
                    </div>
                </div>
                <!--
                <input id="gerer_creneaux" type="button" value="Gérer mes créneaux"/></br>


                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-10 bg-custom-beige custom-rounded-corners m-5">
                            <h3 class="body-color-blue m-5 text-center">Mes Photos :</h3>
                            <h3 class="body-color-blue m-5 text-center">Informations à propos de mon terrain</h3>
                            <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Nom</strong></p>
                            <p class="body-color-blue" style="display: inline-block">terrain 1 fjandsfna</p>
                            <br/>
                            <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Description</strong></p>
                            <p class="body-color-blue" style="display: inline-block">blablalba</p>
                            <br/>
                            <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Adresse</strong></p>
                            <p class="body-color-blue" style="display: inline-block">blablalba</p>
                            <br/>
                            <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Capacité</strong></p>
                            <p class="body-color-blue" style="display: inline-block">blablalba</p>
                            <br/>
                            <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Prix</strong></p>
                            <p class="body-color-blue" style="display: inline-block">blablalba</p>
                            <br/>
                            <p class="ml-5 mr-2 body-color-blue" style="display: inline-block"><strong>Sport</strong></p>
                            <p class="body-color-blue" style="display: inline-block">blablalba</p>
                            <br/>
                            <div class="row justify-content-center">
                                <input id="submitForm" type="submit" name="action" value="Enregistrer les données" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                                       style="background-color: #153455; color: #fdedcf;">
                            </div>
                            <h3 class="body-color-blue m-5 text-center">Ajouter une photo</h3>
                            <div class="row justify-content-center">
                                <input id="submitForm" type="submit" name="action" value="Choisir un fichier" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                                       style="background-color: #153455; color: #fdedcf;">
                            </div>
                            <div class="row justify-content-center">
                                <input id="submitForm" type="submit" name="action" value="Ajouter photo" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                                       style="background-color: #153455; color: #fdedcf;">
                            </div>
                            <div class="row justify-content-center">
                                <input id="submitForm" type="submit" name="action" value="Gérer mes créneaux" class="btn col-3 mb-5 mt-3 custom-rounded-corners"
                                       style="background-color: #153455; color: #fdedcf;">
                            </div>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </div>
    </div>
</div>