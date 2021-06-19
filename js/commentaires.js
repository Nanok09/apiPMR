$(document).ready(function () {
  $(".confirmComment").hide();
  $(".cancelComment").hide();
  $(".comment[data-time]").each(function () {
    var timestamp = $(this).data("time");
    $(this)
      .find(".infoCommentTime")
      .text(new Date(timestamp * 1000).toLocaleString());
  });
  $(document).on("click", ".comment .editComment", function () {
    // console.log("edit");
    var comment = $(this).closest(".comment");
    var text = comment.data("text");
    comment
      .find(".card-body")
      .html($("<textarea>").addClass("form-control").val(text));
    $(this).hide();
    comment.find(".confirmComment").show();
    comment.find(".cancelComment").show();
  });
  $(document).on("click", ".comment .deleteComment", function () {
    // console.log("delete");
    var comment = $(this).closest(".comment");
    var id = comment.data("id-comment");
    // console.log(id);
    $.post(
      "libs/api.php",
      {
        action: "delete_comment",
        id_comment: id,
      },
      function (res) {
        // console.log(res)
        comment.slideUp();
      },
      "json"
    );
  });
  $(document).on("click", ".comment .confirmComment", function () {
    // console.log("confirm");
    var id_comment = $(this).closest(".comment").data("id-comment");
    // console.log(id_comment);
    var comment = $(this).closest(".comment");
    var content = comment.find(".card-body textarea").val();
    // console.log(content);
    if (content && content !== comment.data("text")) {
      $.post(
        "libs/api.php",
        {
          action: "modify_comment",
          id_comment: id_comment,
          comment: content,
        },
        function (res) {
          // console.log(res)
          if (res.success) {
            comment.find(".card-body").text(content);
            comment.data("text", content);
            comment
              .find(".infoCommentTime")
              .text(new Date(res.data.timestamp * 1000).toLocaleString());
          }
        },
        "json"
      );
    }
    $(this).hide();
    comment.find(".cancelComment").hide();
    comment.find(".editComment").show();
  });
  $(document).on("click", ".comment .cancelComment", function () {
    var comment = $(this).closest(".comment");
    var text = comment.data("text");
    comment.find(".card-body").text(text);
    $(this).hide();
    comment.find(".confirmComment").hide();
    comment.find(".editComment").show();
  });

  $(document).on("click", "#addComment", function () {
    // console.log("add_comment")
    var comment = $(this).closest(".comment");
    var text = comment.find(".card-body textarea").val();
    if (text) {
      $.post(
        "libs/api.php",
        {
          action: "add_comment",
          id_place: id_place,
          comment: text,
        },
        function (res) {
          var el = $(
            `<div class="card my-1 shadow-sm comment" data-id-comment="` +
              res.data.id_comment +
              `" data-text="` +
              text +
              `" data-time="` +
              res.data.timestamp +
              `">
                    <div class="card-body">` +
              text +
              `</div>
                    <div class="card-footer">
                        <span class="infoCommentUser">` +
              res.data.pseudo +
              `</span>
                        <span class="ml-2 text-muted infoCommentTime">` +
              new Date(res.data.timestamp * 1000).toLocaleString() +
              `</span><button type="button" class="btn btn-danger btn-sm float-right mx-1 deleteComment"><i class="fas fa-fw fa-trash"></i></button>
                        <button type="button" class="btn btn-primary btn-sm float-right mx-1 editComment" style=""><i class="fa fa-fw fa-pencil"></i> Modifier</button>
                        <button type="button" class="btn btn-success btn-sm float-right mx-1 confirmComment" ><i class="fa fa-fw fa-check"></i> Confirmer</button>
                        <button type="button" class="btn btn-warning btn-sm float-right mx-1 cancelComment"><i class="fa fa-fw fa-ban" ></i> Annuler</button>
                </div>
                </div>`
          );
          $("#areaComments").prepend(el);
          el.find(".confirmComment").hide();
          el.find(".cancelComment").hide();
        },
        "json"
      );
    }
  });
});
