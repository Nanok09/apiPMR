function ajouterAlert(container, alertClass, message) {
  container.empty();
  container.append(
    $("<div>").addClass("alert").addClass(alertClass).text(message)
  );
}

function formToJson(form) {
  var serializedArray = $(form).serializeArray();
  var json = {};
  for (var i = 0; i < serializedArray.length; i++) {
    json[serializedArray[i].name] = serializedArray[i].value;
  }
  return json;
}
