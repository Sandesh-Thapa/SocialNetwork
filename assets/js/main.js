function getDropdownData(user, type) {
  if ($(".dropdown-data-window").css("height") == "0px") {
    var pagename;

    if (type == "notification") {
      pagename = "ajax_load_notifications.php";
      $("span").remove("#unread_notification");
    } else if (type == "message") {
      pagename = "ajax_load_messages.php";
      $("span").remove("#unread_message");
    }

    var ajaxreq = $.ajax({
      url: "includes/handlers/" + pagename,
      type: "POST",
      data: "page=1&userLoggedIn=" + user,
      cache: false,
      success: function (response) {
        $(".dropdown-data-window").html(response);
        $(".dropdown-data-window").css({
          padding: "0px",
          height: "200px",
        });
        $("#dropdown_data_type").val(type);
      },
    });
  } else {
    $(".dropdown-data-window").html("");
    $(".dropdown-data-window").css({
      padding: "0px",
      height: "0px",
    });
  }
}
