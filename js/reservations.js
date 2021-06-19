$(document).ready(function () {
  var calendarEl = document.getElementById("calendar");
  var calendar = new FullCalendar.Calendar(calendarEl, {
    schedulerLicenseKey: "CC-Attribution-NonCommercial-NoDerivatives",
    initialView: "timeGridWeek",
    customButtons: {
      refreshButton: {
        text: "Actualiser",
        click: function () {
          console.log("clicked the custom button!");
          calendar.refetchEvents();
        },
      },
    },
    headerToolbar: {
      left: "title",
      right: "refreshButton today prev,next",
    },
    height: 500,
    locale: "fr",
    allDaySlot: false,
    events: function (info, successCallback, failureCallback) {
      $.post({
        type: "POST",
        url: "libs/api.php",
        data: {
          action: "get_creneaux_place",
          id_place: id_place,
          start: info.startStr,
          end: info.endStr,
        },
        dataType: "json",
        error: function (err) {
          failureCallback(err);
        },
        success: function (res) {
          successCallback(res.data);
        },
      });
    },
  });
  calendar.render();

  $("form#add_creneau").submit(function (event) {
    // console.log('form add creneau');
    var data = formToJson(this);
    data.action = "add_creneau_dispo";
    data.id_place = id_place;
    if (data.time_end === "00:00") {
      data.time_end = "23:59";
    }
    $.post(
      "libs/api.php",
      data,
      function (res) {
        console.log(res);
        if (!res.success) {
          ajouterAlert($("#alertContainer"), "alert-danger", "Erreur");
        } else {
          ajouterAlert($("#alertContainer"), "alert-success", "Créneau ajouté");
        }
        calendar.refetchEvents();
      },
      "json"
    );
    event.preventDefault();
  });
  $("form#add_reservation").submit(function (event) {
    // console.log("form reservation");
    var data = formToJson(this);
    data.action = "add_reservation";
    data.id_place = id_place;
    if (data.time_end === "00:00") {
      data.time_end = "23:59";
    }
    $.post(
      "libs/api.php",
      data,
      function (res) {
        // console.log(res);
        if (!res.success) {
          ajouterAlert(
            $("#alertContainer"),
            "alert-danger",
            "Impossible de réserver ce créneau"
          );
        } else {
          ajouterAlert(
            $("#alertContainer"),
            "alert-success",
            "Créneau réservé !"
          );
        }
        calendar.refetchEvents();
      },
      "json"
    );
    event.preventDefault();
  });
});
