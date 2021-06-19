<?php
include_once "libs/modele.php";

$sports = get_all_sports();

?>
<style>
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
</style>
<script>
//setup before functions
var typingTimer; //timer identifier
var doneTypingInterval = 4000; //time in ms (5 seconds)
var intervalle;
var suggestions = ["1 Rue Béranger", "12 Rue d'Esquermes", "30 Rue Léon Gambetta", "4 Rue des Pyramides",
    "12 Rue Solférino",
    "16 Rue d'Arras", "27 Rue de Trévise", "63 Rue de Cambrai", "Porte des Postes"
];
suggestions.forEach(function(item, index) {
    suggestions[index] = {
        "address": item,
        "coordinates": {
            "lat": 50,
            "long": 3
        }
    }
})
console.log(suggestions);
var donetyping = true;

$("window").ready(function() {



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



    /*
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
                        console.log(oRep);

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
                        console.log(oRep);

                    },
                    dataType: "json"
                });
            }


        })
    */
    /*
    $("#rechercheForm").submit(function(event) {
        event.preventDefault();
    })
*/

}) //end window.ready()

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


<div class="bg-custom-grey custom-rounded-corners mx-4">


    <h1 style="color: #123455; text-align: center; margin: 40px; ">
        Rechercher
    </h1>
    <div class="container">
        <form id="rechercheForm" name="Recherche" method="GET" action="./controleur.php">
            <div class="form-row justify-content-center">
                <div class="col-12">
                    <div class="form-group">
                        <label for="selectSport" style="color: #153455; font-size: 1.2rem;">Choisissez votre
                            sport</label>
                        <select id="selectSport" class="form-control custom-rounded-corners" name="sports" required>
                            <option value=""> - Choisir Sport - </option>
                            <?php
                            foreach ($sports as $sport) {
                                echo "<option value=\"$sport[id]\">$sport[nom]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="w-100"></div>

                <div class="col-11 mb-3">
                    <label for="maLocalisation" class="checkbox-container m-r-45"
                        style="color: #153455; font-size: 1.1rem;">Chercher un terrain proche de moi
                        <input id="maLocalisation" type="checkbox" name="maLocalisation">
                        <span class="checkmark"></span>
                    </label>

                </div>
                <p class="col-1"> ou </p>

                <div class="col-12 mb-3">
                    <img id="ajaxLoader" src="./images/ajaxLoader.gif">
                    <input id="adresseInput" class="form-control custom-rounded-corners" type="text" name="adresse"
                        placeholder="Adresse" required>
                    <ul id="suggestList">
                    </ul>
                    <input type="text" name="lat" id="adresseLat" class="d-none">
                    <input type="text" name="long" id="adresseLong" class="d-none">
                </div>
                <div class="w-100"></div>
                <div class="col-12 mb-3">
                    <div class="row mb-2 justify-content-around">
                        <div class="col-md-3 col-6">
                            <input class="form-control custom-rounded-corners" type="text" name="prixMi"
                                placeholder="Prix Minimal">
                        </div>
                        <div class="col-md-3 col-6">
                            <input class="form-control custom-rounded-corners" type="text" name="prixMa"
                                placeholder="Prix Maximal">
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <input class="form-control custom-rounded-corners" type="text" name="distanceMax"
                           placeholder="Rayon maximal">
                </div>
                <div class="col-12 mb-3">
                    <div class="row mb-2 justify-content-around">
                        <div class="col-3">
                            <label for="dateReservation">Date réservation</label>
                            <input id="dateReservation" type="date" name="date" name="dateReservation">
                        </div>
                        <div class="col-3">
                            <label for="dateReservation">Date réservation</label>
                            <input type="time" name="time_start" step="1800" name="heureDebut" value="15:30:00">
                        </div>

                        <div class="col-3">
                            <label for="dateReservation">Date réservation</label>
                            <input type="time" name="time_end" step="1800" name="heureFin" value="16:30:00">
                        </div>

                    </div>

                </div>

                <div class="col-12 mb-3">
                    <div class="row justify-content-center">
                        <div class="form-group col-4">
                            <label for="nbResultats" class="radio-container"
                                   style="color: #153455; font-size: 1.1rem;">Nombre de Résultats Max?</label>
                            <input id="nbResultats" type="number" name="nbResultats">
                        </div>

                    </div>
                </div>
                <div class="col-12 mb-1 row justify-content-center">
                    <div class="form-group col-4">
                        <label for="publicTerrains" class="radio-container m-r-45"
                               style="color: #153455; font-size: 1.1rem;">Terrains Public</label>
                        <input id="publicTerrains" type="checkbox" checked="checked" name="public">
                        <span class="checkmark"></span>
                    </div>

                    <div class="form-group col-4">
                        <label for="priveTerrains" class="radio-container"
                               style="color: #153455; font-size: 1.1rem;">Terrains Privés</label>
                        <input id="priveTerrains" type="checkbox" checked="checked" name="prive">
                        <span class="checkmark"></span>
                    </div>
                </div>
                <!--        <button id="submitForm" class="btn col-3 my-2" style="background-color: #153455; color: #fdedcf;">Rechercher</button>-->
                <input id="submitForm" type="submit" name="action" value="Recherche" class="btn col-3 my-2"
                    style="background-color: #153455; color: #fdedcf;">
            </div>
        </form>

    </div>

</div>