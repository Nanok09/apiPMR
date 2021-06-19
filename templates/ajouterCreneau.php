<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=ajouterCreneau");
    die("");
}

include_once "libs/libSecurisation.php"; // pour securiser

securiser("?view=accueil"); //vérifie si l'utilisateur est connecté et le renvoie à l'accueil sinon
$id_user = valider("id_user", "SESSION");

?>


<div class="container">

    <?php
    if (!$id_place = valider("id")) {
        echo '<div class="alert alert-danger" role="alert">Aucun terrain demandé</div>';
        die("");
    }
    if (!$infos = get_place_info($id_place)) {
        echo '<div class="alert alert-danger" role="alert">Terrain non trouvé</div>';
        die("");
    }
    ?>

    <h1 class="font-custom-blue">Ajouter un créneau disponible pour : <?php echo $infos["nom"] ?></h1>
    <div class="card">
        <div class="card-body bg-light">
            <div id="calendar"></div>
        </div>
    </div>
    <div class="row justify-content-center mt-3">
        <div class="card col-12 col-sm-8 col-md-6 p-0">
            <div class="card-body bg-custom-blue font-custom-light rounded">
                <form id="add_creneau">
                    <div class="form-group row">
                        <label class="col-6"><i class="far fa-fw fa-calendar-alt"></i> Date</label>
                        <input class="col-6" type="date" name="date" required>
                    </div>
                    <div class="form-group row">
                        <label class="col-6"><i class="far fa-fw fa-clock"></i> Horaire de début</label>
                        <input class="col-6" type="time" name="time_start" step="1800" required>
                    </div>
                    <div class="form-group row">
                        <label class="col-6"><i class="far fa-fw fa-clock"></i> Horaire de fin</label>
                        <input class="col-6" type="time" name="time_end" step="1800" required>
                    </div>
                    <div class="form-group row">
                        <label class="col-6"><i class="fas fa-fw fa-user-friends"></i> Capacité</label>
                        <input class="col-6" type="number" min="1" max="100" name="capacite" required>
                    </div>
                    <div id="alertContainer">
                    </div>
                    <input type="submit" value="Confirmer">
                </form>
            </div>
        </div>
    </div>
</div>
<script>
var id_place = <?php echo $id_place ?>;
</script>
<script src="js/reservations.js"></script>