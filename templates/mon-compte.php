<?php

include_once("./libs/modele.php");
include_once("./libs/libUtils.php");
include_once("./libs/libSecurisation.php");
// Si l'utilisateur est connecte, on affiche un lien de deconnexion
securiser("?view=accueil");

$idUser = $_SESSION["id_user"];
$user_info = get_user_info($idUser);
$reservations = get_current_reservations($idUser);
?>

<h1 class="text-center font-custom-blue">Mon Compte</h1>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-md-12">
            <div class="card rounded bg-custom-blue font-custom-light">
                <div class="card-body">
                    <h2>Mes informations personnelles</h2>
                    <label class="mr-2">Mon pseudo : </label><span><?php echo $user_info["pseudo"] ?></span>
                    <form id="personal_info">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="inputPseudo">Nom</label>
                                    <input type="text" class="form-control" name="nom" placeholder="Mon nom"
                                        value="<?php echo $user_info["nom"] ?>" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="inputPseudo">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" placeholder="Mon prénom"
                                        value="<?php echo $user_info["prenom"] ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPseudo">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Mon email"
                                value="<?php echo $user_info["email"] ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </form>
                    <h2 class="mt-4">Mes réservations en cours</h2>

                    <?php
                    if (count($reservations) == 0) {
                        echo "<div>Aucune réservation en cours</div>";
                    }
                    foreach ($reservations as $reservation) { ?>
                    <div class="row">
                        <div class="p-2 col-12 col-sm-6 col-lg-4">
                            <div class="card p-0 m-2 d-inline-block font-custom-blue bg-custom-light ">
                                <img class="card-img-top w-100"
                                    src="images/terrains/<?php echo $reservation["nomFichier"] ?>" alt="Photo du lieu">
                                <div class="card-body">
                                    <h5><?php echo $reservation["nomTerrain"] ?></h5>
                                    <p class="card-text mb-0"><i class="far fa-fw fa-calendar-alt"></i> Date :
                                        <?php echo $reservation["date"] ?>
                                    </p>
                                    <p class="card-text mb-0"><i class="far fa-fw fa-clock"></i> Début :
                                        <?php echo $reservation["heureDebut"] ?>
                                    </p>
                                    <p class="card-text mb-0"><i class="far fa-fw fa-clock"></i> Fin :
                                        <?php echo $reservation["heureFin"] ?>
                                    </p>
                                    <p class="card-text"><i class="fas fa-fw fa-user-friends"></i>
                                        <?php echo $reservation["nbPersonnes"] ?> personnes
                                    </p>
                                    <div data-id-reservation="<?php echo $reservation["id"] ?>"
                                        class="btn btn-sm btn-danger cancel-reservation"><i
                                            class="fas fa-fw fa-trash"></i>Annuler</div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/compte.js"></script>