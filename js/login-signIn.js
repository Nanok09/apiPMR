$(document).ready(function () {
  $("#inscription").click(function (event) {
    afficherInscription();
    animerBarre("60%");
    $(this).find("h1").addClass("bold");
    $("#connexion>h1").removeClass("bold");
  });

  $("#connexion").click(function (event) {
    afficherConnexion();
    animerBarre("10%");
    $(this).find("h1").addClass("bold");
    $("#inscription>h1").removeClass("bold");
  });

  $("#inscriptionForm").hide();

  function afficherConnexion() {
    $("#inscriptionForm").fadeOut("fast");
    $("#connexionForm").fadeIn("slow");
  }

  function afficherInscription() {
    $("#connexionForm").fadeOut("fast");
    $("#inscriptionForm").fadeIn("slow");
  }

  function animerBarre(position) {
    $("#barre").animate({
      left: position,
    });
  }
});
