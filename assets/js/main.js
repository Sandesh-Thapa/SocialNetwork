$(document).ready(function () {
  $("#searchLive").on("click", function () {
    document.search_form.submit();
  });
});

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

function getLiveSearchUsers(value, user) {
  $.post(
    "includes/handlers/ajax_search.php",
    { query: value, userLoggedIn: user },
    function (data) {
      if ($(".search_results_footer_empty")[0]) {
        $(".search_results_footer_empty").toggleClass("search_results_footer");
        $(".search_results_footer_empty").toggleClass(
          "search_results_footer_empty"
        );
      }

      $(".search_results").html(data);
      $(".search_results_footer").html(
        "<a href='search.php?q=" + value + "'>See All Results</a>"
      );

      if ((data = "")) {
        $(".search_results_footer").html("");
        $(".search_results_footer").toggleClass("search_results_footer_empty");
        $(".search_results_footer").toggleClass("search_results_footer");
      }
    }
  );
}
