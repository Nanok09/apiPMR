<?php

include_once "libs/modele.php";
include_once "libs/libUtils.php";

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=accueil");
    die("");
}

$sports = get_all_sports();

//TODO: mettre le style et script dans des fichiers spécifiques
?>
<style>
#imageLoupe {
    max-width: 5%;
}

#imageLoupe2 {
    max-width: 5%;
    transform: translateY(-10px);
}

.sportLogos {
    max-height: 120px;
}

.sportLogos:hover {
    transform: translateY(-10px);
    cursor: pointer;
}

.sportLogos2 {
    max-height: 200px;
    max-width: 200px;
    margin: 10px;
}

.sportLogos2:hover {
    transform: translateY(-10px);
    cursor: pointer;
    z-index: 2000;
}

#searchbar:hover {
    cursor: pointer;

}





.carouselControls {
    width: 5%;
}

.carouselControls>.carousel-control-prev-icon,
.carousel-control-next-icon {
    height: 100px;
    width: 100px;
    outline: black;
    background-size: 100%, 100%;
    border-radius: 50%;
    background-image: none;
}

.carouselControls>.carousel-control-next-icon:after {
    content: '>';
    font-size: 55px;
    color: #b39769;
}

.carouselControls>.carousel-control-prev-icon:after {
    content: '<';
    font-size: 55px;
    color: #b39769;
}




#svg {
    transform: translateY(-10px);
    width: 100px;
    height: 80px;
}

.hoveredCircle {
    r: 35;
    transform: translateY(5px);
    cursor: pointer;
}


@media (max-width: 576px) {
    #svg {
        transform: translateY(-20px);
    }

    #svg>circle {
        r: 20;
    }

    .hoveredCircle {
        r: 25;
        cursor: pointer;
    }

    #svg>image {
        height: 25px;
        width: 25px;
    }

}

.custom-rounded-corners {
    border-radius: 50px;
}

#containerCroix {
    right: 0;
}

#imageCroix {
    width: 30px;
    height: auto;
}

#imageCroix:hover {
    width: 40px;
    cursor: pointer;
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
var typingTimer; //timer identifier
var doneTypingInterval = 4000; //time in ms (5 seconds)
var intervalle;
var donetyping = true;

$(window).ready(function() {

    // Searchbar hover effect
    $("#searchbar").hover(
        function() {
            let jSearchbar = $(this)
            jSearchbar.addClass('shadow')
            jSearchbar.css("background-color", '#fafafa')
            jSearchbar.removeClass('bg-light')
        },
        function() {
            let jSearchbar = $(this)
            jSearchbar.removeClass('shadow')
            jSearchbar.css("background-color", "")
            jSearchbar.addClass('bg-light')
        }
    )
    // Click shows searchbar
    $("#searchbar").click(
        function() {
            $("#recherche").removeClass("d-none");
            $("#blockRecherche").addClass("d-none");
        }
    )

    // Searchbar effect
    $("#svg").hover(function() {
        $("#svg>circle").addClass("hoveredCircle")
    }, function() {
        $("#svg>circle").removeClass('hoveredCircle');
    });

    $("#svg").click(function() {
        console.log("click svg")
        $("#rechercheForm").trigger("submit");
        return false;
    });

    $("#rechercheForm").submit(function(event) {

    });


    $("#containerCroix").click(function() {
        $("#recherche").addClass("d-none");
        $("#blockRecherche").removeClass("d-none");
    })
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

    setInterval(function() {
        if ($("#adresseInput").val() == "") {
            $("#maLocalisation").removeAttr("disabled");
            $("#suggestList").hide();
            $("#ajaxLoader").hide();
        };
    }, 1000);


    $("#maLocalisation").click(function() {
        if ($("#maLocalisation").prop("checked")) {
            $('#adresseInput').attr("disabled", "disabled");
        } else {
            $("#adresseInput").removeAttr("disabled");
        };
    })

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

        var selectedSport = $("#selectSport").children("option:selected").val();
        var adresseVal =$("#adresseInput").val();
        var maLocalisation =$("#maLocalisation").checked;

        console.log(selectedSport);
        console.log(adresseVal);
        console.log(maLocalisation);


});


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
</script>
<div class="container">
    <div class="my-5"></div>
    <div id="recherche" class="row justify-content-center d-none">
        <div class="col-8 bg-custom-grey text-center custom-rounded-corners">
            <div id="containerCroix" class="position-absolute"> <img id="imageCroix" src="./images/croix.png"
                    class="d-block"> </div>
            <svg id="svg">
                <circle r="30" cx="50%" cy="30" fill="#35516E">

                </circle>
                <image href="./images/loupe.png" x="35%" y="15" height="35px" width="35px" />
            </svg>
            <form id="rechercheForm" action="./controleur.php" name="Recherche" method="get">
                <div class="form-row justify-content-center">
                    <div class="col-8">
                        <div class="form-group">
                            <label for="selectSport">Choisissez votre sport</label>
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

                    <div class="col-12">
                        <div class="form-row justify-content-around">
                            <div class="col-7">
                                <label for="maLocalisation" class="checkbox-container"
                                       style="color: #153455; font-size: 1.1rem;">Chercher un terrain proche de moi</label>
                                <input id="maLocalisation" type="checkbox" name="maLocalisation">
                                <span class="checkmark"></span>
                            </div>
                        </div>
                    </div>



                    <div class="col-8 mb-2">
                        <img id="ajaxLoader" src="./images/ajaxLoader.gif">
                        <input id="adresseInput" class="form-control custom-rounded-corners" type="text" name="adresse"
                            placeholder="Adresse" required>
                        <ul id="suggestList">
                        </ul>
                        <input type="text" name="lat" id="adresseLat" class="d-none">
                        <input type="text" name="long" id="adresseLong" class="d-none">

                    </div>
                    <div class="w-100"></div>
                    <div class="col-8 mb-2">
                        <div class="form-row justify-content-around">
                            <div class="col-4">
                                <input class="form-control custom-rounded-corners" type="text" name="prixMa" placeholder="Prix Max">
                            </div>
                            <div class="col-4">
                                <input class="form-control custom-rounded-corners" type="text" name="prixMi" placeholder="Prix Min">
                            </div>
                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-8 mb-2">
                        <input class="form-control custom-rounded-corners" type="text" name="distanceMax"
                               placeholder="Rayon Maximal">
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

                    <input type="text" name="action" value="Recherche" class="d-none">
                </div>
            </form>
        </div>
    </div>
    <div id="blockRecherche" class="row justify-content-center">
        <div class="col col-xl-10 rounded-pill bg-custom-grey my-2 py-3">
            <h1 class="text-center">Rechercher des terrains proches de vous !</h1>
            <div class="bg-light rounded-lg p-3 w-75 mx-auto" id="searchbar">
                <img src="./images/loupe.png" class="img-fluid" id="imageLoupe">
            </div>
        </div>
    </div>
</div>

<div class="my-5"></div>

<!-- Sports -->
<div class="container-fluid bg-custom-grey">

    <h1> Choisissez votre sport! </h1>
    <div id="sports" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php
            $sport_groups = array_chunk($sports, 3); //séparer en arrays de 3 sports
            $i = 0;
            foreach ($sport_groups as $group) {
                echo '<div class="carousel-item' . ($i == 0 ? ' active' : '') . '"><div class="row justify-content-around pt-3">';
                foreach ($group as $sport) {
                    echo "<img src=\"./images/sports/$sport[logo]\"
                        class=\"d-block col-12 col-md-3 col-lg-2 col-xl-1 sportLogos2\">";
                }
                echo "</div></div>";
                $i += 1;
            }
            ?>
        </div>
        <!--Controles-->
        <a class="carousel-control-prev primary carouselControls" href="#sports" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Précédent</span>
        </a>
        <a class="carousel-control-next carouselControls" href="#sports" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Suivant</span>
        </a>
    </div>
</div>

<div class="my-5"></div>

<!-- Terrains bien notés -->
<div class="container-fluid bg-custom-grey">

    <h1> Ce que vous avez aimé: </h1>
    <div id="terrains" class="carousel slide" data-interval="false">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="row justify-content-around pt-3">
                    <div class="col-8 col-md-4 mb-2 text-center">
                        <img src="./images/terrains/terrain1.jpg" class="d-block m-auto sportLogos2">
                        <h5>Hoops factory Lille</h5>
                    </div>
                    <div class="col-8 col-md-4 mb-2 text-center">
                        <img src="./images/terrains/terrain2.jpg" class="d-block m-auto sportLogos2">
                        <h5>Playground de la Porte Dorée</h5>
                    </div>
                    <div class="col-8 col-md-4 mb-2 text-center">
                        <img src="./images/terrains/terrain3.jpg" class="d-block m-auto sportLogos2">
                        <h5>Terrain de tennis du Triolo</h5>
                    </div>
                </div>
            </div>s
            <div class="carousel-item">
                <div class="row justify-content-around pt-3">
                    <div class="col-8 col-md-4 mb-2 text-center">
                        <img src="./images/terrains/terrain4.jpg" class="d-block m-auto sportLogos2">
                        <h5>Stade Hunebelle - Clamart</h5>
                    </div>
                    <div class="col-8 col-md-4 mb-2 text-center">
                        <img src="./images/terrains/terrain5.png" class="d-block m-auto sportLogos2">
                        <h5>Parc Street Workout - Fontenay-Aux-Roses</h5>
                    </div>
                    <div class="col-8 col-md-4 mb-2 text-center">
                        <img src="./images/terrains/terrain6.jpg" class="d-block m-auto sportLogos2">
                        <h5>Salle de Musculation - Villeneuve d'Ascq</h5>
                    </div>
                </div>
            </div>
        </div>
        <!--Controles-->
        <a class="carousel-control-prev primary carouselControls" href="#terrains" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Précédent</span>
        </a>
        <a class="carousel-control-next carouselControls" href="#terrains" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Suivant</span>
        </a>
    </div>

</div>


<div>


</div>

<div class="my-5"></div>