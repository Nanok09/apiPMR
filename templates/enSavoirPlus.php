<?php

include_once "libs/libUtils.php";
include_once "libs/libSQL.pdo.php";
include_once "libs/libSecurisation.php";
include_once "libs/modele.php";


$id_place = valider('id');
$photos = get_photos_place($id_place);
$note = get_note_place($id_place);
$terrain = get_place_info($id_place);
$createur_id = get_createur_lieu($id_place);
$createur = get_user_info($createur_id);
//var_dump($id_place);



?>
<div class="container">

    <?php
    if (!$id_place = valider("id")) {
        echo '<div class="alert alert-danger" role="alert">Aucun terrain demandé</div>';
        die("");
    }
    if (!$terrain = get_place_info($id_place)) {
        echo '<div class="alert alert-danger" role="alert">Terrain non trouvé</div>';
        die("");
    }

    $photos = get_photos_place($id_place);
    $note = get_note_place($id_place);
    $createur_id = get_createur_lieu($id_place);
    $createur = get_user_info($createur_id);
    $comments = get_comments($id_place);
    $is_connected = valider("is_connected", "SESSION");
    $id_user = valider("id_user", "SESSION");
    ?>

    <style>
    .image_custom_center {
        display: block;
        margin: auto;
    }
    </style>


    <div class="container">
        <div class="row justify-content-center">
            <h1><?php echo $terrain["nom"] ?></h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-10">
                <?php
                foreach ($photos as $photo) {
                    echo "<img src='./images/terrains/" . $photo['nomFichier'] . "' class='image_custom_center img-fluid custom-rounded-corners my-3'>";
                }
                ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="row w-50 justify-content-around">
                <div class="my-4 pt-3 col-4 bg-custom-grey custom-rounded-corners">
                    <p class="body-color-blue text-center"><?php echo $terrain['sport']; ?></p>
                </div>
                <div class="my-4 pt-3 col-4 bg-custom-grey custom-rounded-corners">
                    <p class="body-color-blue text-center">
                        <?php
                        if ($terrain['prive']) {
                            echo "Privé";
                        } else {
                            echo "Public";
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="container mb-5">
        <div class="row justify-content-around">
            <div class="col-4 bg-custom-grey custom-rounded-corners h-50">
                <h3 class="mt-3 body-color-blue text-center">Résultat</h3>
                <div class="my-2 noteContainer">
                    <?php
                    for ($i = 1; $i <= round($note['mean']); $i++) {
                        echo '<div class="star"></div>';
                    }
                    ?>
                </div>
                <p class="mt-1 text-muted">A partir de <?php echo $terrain['prix']; ?> euros
                    par heure</p>
                <a href="?view=reserver&id=<?php echo $id_place ?>" id="louer" type="button"
                    class="btn col-12 my-3 custom-rounded-corners bg-custom-blue font-custom-light">Louer</a>
                <p class="mt-2 ml-2 body-color-blue">Gérant du terrain :
                    <?php
                    echo $createur['pseudo'];
                    ?>
                </p>
                <input id="submitForm" type="submit" name="action" value="Discuter avec le gérant"
                    class="btn col-12 mb-5 mt-3 custom-rounded-corners bg-custom-blue font-custom-light">
            </div>

            <div class="col-7 p-5 bg-custom-grey custom-rounded-corners">
                <p class="body-color-blue"><strong>Informations générales</strong></p>
                <p class="body-color-blue"><?php echo $terrain['description']; ?></p>
                <hr class="w-75">
                <p class="my-2 body-color-blue"><strong>Adresse</strong></p>
                <p class="body-color-blue"><?php echo $terrain['adresse']; ?></p>
                <p class="my-5 body-color-blue d-inline-block"><strong>Capacité maximale</strong>
                </p>
                <p class="my-5 body-color-blue d-inline-block"><?php echo $terrain['capacite']; ?>
                </p>
            </div>

        </div>

    </div>

    <div class="row justify-content-end">
        <div class="col-8 p-5 bg-custom-grey custom-rounded-corners">
            <h3 class="body-color-blue">Noter</h3>
            <div class="my-2 noteContainer">
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    echo '<div class="star"></div>';
                }
                ?>
            </div>
            <hr class="w-100">
            <h3 class="body-color-blue">Commentaires</h3>
            <?php if ($is_connected) { ?>
            <div class="card my-1 shadow-sm comment">
                <div class="card-body">
                    <textarea class="form-control" placeholder="Ecrivez un nouveau commentaire..."></textarea>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success btn-sm float-right mx-1" id="addComment"><i
                            class="fas fa-fw fa-plus"></i>Ajouter</button>
                </div>
            </div>
            <?php } ?>
            <div id="areaComments">
                <?php foreach ($comments as $comment) {
                ?>

                <div class="card my-1 shadow-sm comment" data-id-comment="<?php echo $comment["id"] ?>"
                    data-text="<?php echo $comment["message"] ?>"
                    data-time="<?php echo strtotime($comment["timestamp"]) ?>">
                    <div class="card-body">
                        <?php echo $comment["message"] ?>
                    </div>
                    <div class="card-footer">
                        <span class="infoCommentUser"><?php echo $comment["nomUtilisateur"] ?></span>
                        <span class="ml-2 text-muted infoCommentTime"></span>
                        <?php if ($is_connected && $comment["idUtilisateur"] == $id_user) { ?>
                        <button type="button" class="btn btn-danger btn-sm float-right mx-1 deleteComment"><i
                                class="fas fa-fw fa-trash"></i></button>
                        <button type="button" class="btn btn-primary btn-sm float-right mx-1 editComment"><i
                                class="fa fa-fw fa-pencil"></i> Modifier</button>
                        <button type="button" class="btn btn-success btn-sm float-right mx-1 confirmComment"><i
                                class="fa fa-fw fa-check"></i> Confirmer</button>
                        <button type="button" class="btn btn-warning btn-sm float-right mx-1 cancelComment"><i
                                class="fa fa-fw fa-ban"></i> Annuler</button>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
var id_place = <?php echo $id_place ?>;
</script>
<script src="js/commentaires.js"></script>