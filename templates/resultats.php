<?php
include_once ("./libs/libUtils.php");
include_once "libs/modele.php";

$all_sports = get_all_sports();

$sports = valider("sports");
$localisation= valider("localisation");
$lat=valider("lat");
$long=valider("long");


$adresse=valider("adresse");
$horaireA=valider("horaireA");
$horaireD=valider("horaireD");
$prixMi=valider("prixMi");
$prixMa=valider("prixMa");
$dateReservation=valider("dateRes");
$distanceMax=valider("dMax");
$acceptPublic=valider("acceptPublic");
$acceptPrive=valider("acceptPrive");
$nBMax=valider("nBMax");


?>
<script>
    var localisation = "<?php echo $localisation ?>";
    var sport = "<?php echo $sports ?>";
    var lat = "<?php echo $lat ?>";
    var long = "<?php echo $long ?>";

    var horaireFin = "<?php echo $horaireA ?>";
    var horaireDebut = "<?php echo $horaireD ?>";
    var prixMi = "<?php echo $prixMi ?>";
    var prixMa = "<?php echo $prixMa ?>";
    var dateReservation = "<?php echo $dateReservation ?>";
    var distanceMax = "<?php echo $distanceMax ?>";
    var acceptPrive = "<?php echo $acceptPrive ?>";
    var acceptPublic = "<?php echo $acceptPublic ?>";
    var nBMax = "<?php echo $nBMax ?>";

    var all_info = [sport, lat, long, distanceMax, acceptPublic, acceptPrive, prixMi, prixMa, nBMax, horaireDebut, horaireFin, dateReservation];

    console.log(all_info);



    //setup before functions
    var typingTimer; //timer identifier
    var doneTypingInterval = 4000; //time in ms (5 seconds)
    var intervalle;
    var donetyping = true;


    $("window").ready(function (){

        if(localisation === ""){
            getListPlacesByAddress(sport);
        }
        if(localisation === "on"){
            console.log("HI");
            var geolocation = null;

            if (window.navigator && window.navigator.geolocation) {
                geolocation = window.navigator.geolocation;
            }

            if (geolocation) {
                geolocation.getCurrentPosition(function(position) {

                    window.position = position;
                    console.log(position);
                    getListPlacesByCurrentPosition(position, sport);
                });
            }


        }


        //on keyup, start the countdown
        $("#adresseInput").keyup(function(event) {
            addKeyBoardEvent(event);
            clearTimeout(typingTimer);
            if (donetyping) {
                setTimeout(function() {
                    let adresse = $("#adresseInput").val();
                    appelAdresseResearch(adresse, 6);
                    // addLi(adresse, 6);

                }, 1000);
                intervalle = setInterval(function() {
                    let adresse = $("#adresseInput").val();
                    appelAdresseResearch(adresse, 6);
                    // addLi(adresse, 6);
                }, 2000);
            }
            donetyping = false;
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //user is "finished typing," do something
        function doneTyping() {
            console.log("donetyping appelé");
            clearInterval(intervalle);
            donetyping = true;
            $("#ajaxLoader").hide();

        }







        $("#adresseInput").keydown(function() {
            $('#maLocalisation').attr("disabled", "disabled");
            $("#suggestList").show();
            $("#ajaxLoader").show();
        });


        $(document).on("click", "#suggestList li", function(event) {
            $("#adresseInput").val(event.target.innerHTML);
            window.adressePosition = $(this).data("coordinates");
            $("#suggestList").hide();
            $("#ajaxLoader").hide();
            $("#adresseLat").val(window.adressePosition.lat);
            $("#adresseLong").val(window.adressePosition.long);
        })





        $("#maLocalisation").click(function() {
            if ($("#maLocalisation").prop("checked")) {
                $('#adresseInput').attr("disabled", "disabled");
            } else {
                $("#adresseInput").removeAttr("disabled");
            };
        })


        setInterval(function() {
            if ($("#adresseInput").val() == "") {
                $("#maLocalisation").removeAttr("disabled");
                $("#suggestList").hide();
                $("#ajaxLoader").hide();
            };
        }, 1000);

        $("#maLocalisation").click(function() {
            if ($("#maLocalisation").prop("checked")) {
                var geolocation = null;

                if (window.navigator && window.navigator.geolocation) {
                    geolocation = window.navigator.geolocation;
                }

                if (geolocation) {
                    geolocation.getCurrentPosition(function(position) {

                        window.position = position;
                    });
                }
            }
        })

        $('#dateReservation').val(new Date().toDateInputValue());





            $("#submitForm").click(function() {

                let selectedSport = $("#selectSport").children("option:selected").val();

                if ($("#maLocalisation").prop("disabled")) {
                    console.log("Recherche par adresse");
                    console.log(window.adressePosition);
                    $.ajax({
                        type: "POST",
                        url: "libs/api.php",
                        headers: {
                            "debug-data": true
                        },
                        data: {
                            "action": "get_list_places",
                            "user_location_lat": window.adressePosition["lat"],
                            "user_location_long": window.adressePosition["long"],
                            "sport": selectedSport
                        },
                        success: function(oRep) {
                            printPlaces(oRep);

                        },
                        dataType: "json"
                    });

                } else if ($("#adresseInput").prop("disabled")) {
                    $.ajax({
                        type: "POST",
                        url: "libs/api.php",
                        headers: {
                            "debug-data": true
                        },
                        data: {
                            "action": "get_list_places",
                            "user_location_lat": window.position["coords"]["latitude"],
                            "user_location_long": window.position["coords"]["longitude"],
                            "sport": selectedSport
                        },
                        success: function(oRep) {
                            printPlaces(oRep);
                        },
                        dataType: "json"
                    });
                }


            })


        $("#rechercheForm").submit(function(event) {
            event.preventDefault();
        })


    })

    function getListPlacesByCurrentPosition(position, sport){
        let data={
            "action": "get_list_places",
            "sport":sport,
            "user_location_lat":position["coords"]["latitude"],
            "user_location_long":position["coords"]["longitude"],
            "distance_max":distanceMax,
            "accept_public":acceptPublic,
            "accept_private":acceptPrive,
            "prix_min":prixMi,
            "prix_max":prixMa,
            "max_results":nBMax,
            "start_time":horaireDebut,
            "end_time":horaireFin,
            "date":dateReservation
        };

        if(acceptPrive === "on"){
            console.log("Hi: 1");

            delete data["accept_private"];
        }
        if(acceptPublic === "on"){
            console.log("Hi: 2");

            delete data["accept_public"];
        }


        if(acceptPrive === ""){
            console.log("Hi: 3");

            data["accept_private"]='no';
        }
        if(acceptPublic === ""){
            console.log("Hi: 4");

            data["accept_public"]='no';
        }



        for(key in data){
            if(data[key] == ""){

                delete data[key];
            }
        }


        console.log(data);



        $.ajax({
            type: "POST",
            url: "libs/api.php",
            headers: {
                "debug-data": true
            },
            data: data,
            success: function(oRep) {
                console.log(oRep);
                printPlaces(oRep);

            },
            dataType: "json"
        });
    }

    function getListPlacesByAddress(sport){
        let data={
            "action": "get_list_places",
            "sport":sport,
            "user_location_lat":lat,
            "user_location_long":long,
            "distance_max":distanceMax,
            "accept_public":acceptPublic,
            "accept_private":acceptPrive,
            "prix_min":prixMi,
            "prix_max":prixMa,
            "max_results":nBMax,
            "start_time":horaireDebut,
            "end_time":horaireFin,
            "date":dateReservation
        };

        if(acceptPrive === "on"){
            console.log("Hi: 1");

            delete data["accept_private"];
        }
        if(acceptPublic === "on"){
            console.log("Hi: 2");

            delete data["accept_public"];
        }


        if(acceptPrive === ""){
            console.log("Hi: 3");

            data["accept_private"]='no';
        }
        if(acceptPublic === ""){
            console.log("Hi: 4");

            data["accept_public"]='no';
        }



        for(key in data){
            if(data[key] == ""){

                delete data[key];
            }
        }



        $.ajax({
            type: "POST",
            url: "libs/api.php",
            headers: {
                "debug-data": true
            },
            data: data,
            success: function(oRep) {
                console.log(oRep);
                printPlaces(oRep);


            },
            dataType: "json"
        });
    }
    function printPlaces(oRep){

        $("#content").empty();
        var content;
        console.log(oRep.data);
        var isPrivate;
        var stars;
        var idTerrain;
        var photoSrc




        oRep.data.forEach(function (item, index){
            idTerrain = "resultat"+item.id;
            photoSrc = "./images/terrains/"+item.photos[0].nomFichier;
            if(item.private == 1){
                isPrivate = "Contacter l\'Admin";
            } else {
                isPrivate = "Terrain Public";
            }
            stars = "";
            for (i=1;i<=item.note;i++){
                stars += '<img src="./images/Icon%20étoile.svg">'
            }


            content = '<a href=index.php?view=enSavoirPlus&id='+item.id+'>'+
                '<div class="container-fluid" id='+idTerrain+'>' +
                '<div class="row justify-content-center">'+
                '<div class="col-12 row bg-custom-grey pt-4">'+
                '<div class="col-6">'+
                '<h2>Resultat '+(index+1)+'</h2>'+
                 stars+
                '<div class="position-absolute" style="bottom: 0"><h5>'+isPrivate+'</h5></div>'+
                '</div>'+
                '<div class="col-6">'+
                '<div class="text-center">'+
                '<img src='+photoSrc+' class="img-fluid">'+
                '<h5>'+item.name+'</h5>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</a>';

            $("#content").append(content);

        })

    }








    function addLi(val, maxListSize) {
        console.log(val);
        $("#suggestList").empty();
        if (val == "") {
            $("#suggestList").html("");
        }
        if (val.length >= maxListSize) {
            val.slice(0, maxListSize).map(function(suggestion, index) {
                $("#suggestList").append(
                    $("<li>")
                        .html(suggestion.address)
                        .data("coordinates", suggestion.coordinates)
                        .attr("id", "element".concat(index + 1))

                )
            })
        } else {
            val.forEach(function(suggestion, index) {
                console.log("Suggestion:" + suggestion.address);
                $("#suggestList").append(
                    $("<li>")
                        .html(suggestion.address)
                        .data("coordinates", suggestion.coordinates)
                        .attr("id", index + 1)
                )
            })
        }
    }

    function appelAdresseResearch(adresse, max_results) {
        console.log("JE suis appelé oyé")

        if (adresse != "") {
            $.ajax({
                type: "POST",
                url: "libs/api.php",
                headers: {
                    "debug-data": true
                },
                data: {
                    "action": "address_research",
                    "address": adresse,
                    "max_results": max_results
                },
                success: function(oRep) {
                    console.log(oRep);
                    addLi(oRep.data)
                },
                dataType: "json"
            });
        }
    }

    function addKeyBoardEvent(e) {
        var selectedId = 0;
        var elementId;
        var previousElementId;
        if (e.key == "ArrowDown") {
            console.log(e.key);
            selectedId += 1;
            elementId = "element".concat(selectedId);
            previousElementId = "element".concat(selectedId - 1);
            if ($(previousElementId).hasClass("hovered")) {
                $(previousElementId).toggleClass("hovered");
            }
            $(elementId).addClass("hovered");
        }
        if (e.key == "ArrowUp") {
            console.log(e.key);
            selectedId -= 1;
            elementId = "element".concat(selectedId);
            previousElementId = "element".concat(selectedId + 1);
            if ($(previousElementId).hasClass("hovered")) {
                $(previousElementId).toggleClass("hovered");
            }
            $(elementId).toggleClass("hovered");
        }
        if (e.key == "ArrowRight") {
            console.log(e.key); //tab
        }


    }

    Date.prototype.toDateInputValue = (function() {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });


</script>


<style>

.wrapper{
    display:flex;
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

.dropdown-toggle::after {
    display: block;
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
}
@media (max-width: 768px) {
    #sidebar {
        margin-left: -250px;
    }
    #sidebar.active {
        margin-left: 0;
    }
}

#suggestList {
    display: none;
    padding-left: 0;
    border-top: none;
    left: 0px;
    top: 10px;
    right: 0px;
    text-decoration: none;
}

#suggestList>li {
    display: block;
    border: 1px solid black;
    padding: 2px;
    border-radius: 50px;
    font-size: 12px;
}

#suggestList>li:hover {
    cursor: pointer;
    background-color: lightgrey;
}



#ajaxLoader {
    display: none;
    position: absolute;
    right: 30px;
    top: 10px;
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

a, a:hover, a:focus {
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

#sidebar ul li.active > a, a[aria-expanded="true"] {
    color: #000;
    background: #fdedcf;
}
ul ul a {
    font-size: 0.9em !important;
    padding-left: 30px !important;
    background: #fdedcf;
}

#content>a>div:hover{
    cursor: pointer;
    transform: translateY(-5px);
}
</style>

<script>


$(document).ready(function () {

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

});
</script>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>Filtres</h3>
        </div>

        <ul class="list-unstyled components">
            <p>Paramètres de recherche</p>

            <form name="action" id="rechercheForm">
                <div class="form-row justify-content-center mb-2">


                    <select id="selectSport" class="form-control col-10 custom-rounded-corners mb-2" name="sports" required>
                        <option value=""> - Choisir Sport - </option>
                        <?php
                        foreach ($all_sports as $sport) {
                            echo "<option value=\"$sport[id]\">$sport[nom]</option>";
                        }
                        ?>
                    </select>

                    <div class="col-9 mb-3">
                        <label for="maLocalisation" class="checkbox-container m-r-45"
                               style="color: #153455; font-size: 1.1rem;">Chercher un terrain proche de moi
                            <input id="maLocalisation" type="checkbox" name="maLocalisation">
                            <span class="checkmark"></span>
                        </label>

                    </div>
                    <p class="col-1"> ou </p>


                </div>
                <div class="form-row justify-content-center mb-2">

                    <div class="col-10 mb-3">
                        <img id="ajaxLoader" src="./images/ajaxLoader.gif">
                        <input id="adresseInput" class="form-control custom-rounded-corners" type="text" name="adresse"
                               placeholder="Adresse" required>
                        <ul id="suggestList">
                        </ul>
                        <input type="text" name="lat" id="adresseLat" class="d-none">
                        <input type="text" name="long" id="adresseLong" class="d-none">
                    </div>
                </div>

                <div class="form-row justify-content-center mb-2">
                    <div class="col-12 mb-3">
                        <div class="row mb-2 justify-content-around">
                            <div class="col-5">
                                <input class="form-control custom-rounded-corners" type="text" name="prixMi"
                                       placeholder="Prix Minimal">
                            </div>
                            <div class="col-5">
                                <input class="form-control custom-rounded-corners" type="text" name="prixMa"
                                       placeholder="Prix Maximal">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row justify-content-center mb-2">

                    <div class="col-10 mb-3">
                        <input class="form-control custom-rounded-corners" type="text" name="distanceMax"
                               placeholder="Rayon maximal">
                    </div>

                </div>



                <div class="form-row justify-content-center">

                    <div class="form-check col-9">
                        <input id="terrainPublic" type="checkbox" class="form-check-input" name="public" checked="checked">
                        <label for="terrainPublic" class="form-check-label text-center">Voulez-vous des terrains public?</label>
                    </div>
                    <div class="form-check col-9">
                        <input id="terrainPrive" type="checkbox" class="form-check-input" name="prive" checked="checked">
                        <label for="terrainPrive" class="form-check-label text-center">Voulez-vous des terrains privés?</label>
                    </div>
                </div>

                <div class="form-row justify-content-center">
                    <input id="submitForm" type="submit" value="Nouvelle Recherche" class="custom-rounded-corners bg-custom-grey">
                </div>

            </form>

        </ul>

    </nav>


    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="fas fa-align-left"></i>
                    <span>Changer le filtre</span>
                </button>
            </div>
        </nav>
        <div class="container-fluid" id="resultat1">
            <div class="row justify-content-center">
                <div class="col-12 row bg-custom-grey pt-4">
                    <div class="col-6">
                        <h2>Resultat 1:</h2>
                        <img src="./images/Icon%20étoile.svg"><img src="./images/Icon%20étoile.svg"><img src="./images/Icon%20étoile.svg"><img src="./images/Icon%20étoile.svg"><img src="./images/Icon%20étoile.svg">
                        <div class="position-absolute" style="bottom: 0"><h5>Contacter l'Admin</h5></div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <img src="./images/terrains/terrain1.jpg" class="img-fluid">
                            <h5>Hoops Facory Lille</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid" id="resultat2">


            <div class="row justify-content-center">

                <div class="col-12 row bg-custom-grey pt-4">
                    <div class="col-6">
                        <h2>Resultat 2</h2>
                        <img src="./images/Icon%20étoile.svg"><img src="./images/Icon%20étoile.svg"><img src="./images/Icon%20étoile.svg"><img src="./images/Icon%20étoile.svg">
                        <div class="position-absolute" style="bottom: 0"> <h5>Terrain Public</h5></div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <img src="./images/terrains/terrain2.jpg" class="img-fluid">
                            <h5>Playground de la Porte Dorée</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


