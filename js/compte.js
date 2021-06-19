$(document).ready(function () {
  $(document).on("click", "div.cancel-reservation", function () {
    var id = $(this).data("id-reservation");
    var that = this;
    console.log(id);
    $.post(
      "libs/api.php",
      {
        action: "delete_reservation",
        id_reservation: id,
      },
      function (res) {
        if (!res.success) {
          console.log("La réservation n'a pas pu être annulée");
        } else {
          //cacher la réservation
          $(that).closest(".card").parent().slideUp();
        }
      },
      "json"
    );
  });
  $("form#personal_info").on("submit", function (event) {
    var data = formToJson(this);
    data.action = "update_user";
    $.post(
      "libs/api.php",
      data,
      function (res) {
        console.log(res);
      },
      "json"
    );
    event.preventDefault();
  });
});
