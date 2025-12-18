jQuery(document).ready(function ($) {
  "use strict";

  //Contact
  $("form.contactForm").submit(function () {
    var f = $(this).find(".form-group"),
      ferror = false,
      emailExp = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;

    f.children("input").each(function () {
      // run all inputs

      var i = $(this); // current input
      var rule = i.attr("data-rule");

      if (rule !== undefined) {
        var ierror = false; // error flag for current input
        var pos = rule.indexOf(":", 0);
        if (pos >= 0) {
          var exp = rule.substr(pos + 1, rule.length);
          rule = rule.substr(0, pos);
        } else {
          rule = rule.substr(pos + 1, rule.length);
        }

        switch (rule) {
          case "required":
            if (i.val() === "") {
              ferror = ierror = true;
            }
            break;

          case "minlen":
            if (i.val().length < parseInt(exp)) {
              ferror = ierror = true;
            }
            break;

          case "email":
            if (!emailExp.test(i.val())) {
              ferror = ierror = true;
            }
            break;

          case "checked":
            if (!i.is(":checked")) {
              ferror = ierror = true;
            }
            break;

          case "regexp":
            exp = new RegExp(exp);
            if (!exp.test(i.val())) {
              ferror = ierror = true;
            }
            break;
        }
        i.next(".validation")
          .html(
            ierror
              ? i.attr("data-msg") !== undefined
                ? i.attr("data-msg")
                : "wrong Input"
              : ""
          )
          .show("blind");
      }
    });
    f.children("textarea").each(function () {
      // run all inputs

      var i = $(this); // current input
      var rule = i.attr("data-rule");

      if (rule !== undefined) {
        var ierror = false; // error flag for current input
        var pos = rule.indexOf(":", 0);
        if (pos >= 0) {
          var exp = rule.substr(pos + 1, rule.length);
          rule = rule.substr(0, pos);
        } else {
          rule = rule.substr(pos + 1, rule.length);
        }

        switch (rule) {
          case "required":
            if (i.val() === "") {
              ferror = ierror = true;
            }
            break;

          case "minlen":
            if (i.val().length < parseInt(exp)) {
              ferror = ierror = true;
            }
            break;
        }
        i.next(".validation")
          .html(
            ierror
              ? i.attr("data-msg") != undefined
                ? i.attr("data-msg")
                : "wrong Input"
              : ""
          )
          .show("blind");
      }
    });
    if (ferror) return false;
    else var str = $(this).serialize();
    var action = $(this).attr("action");
    if (!action) {
      action = "contactform/contactform.php";
    }
    $.ajax({
      type: "POST",
      url: action,
      data: str,
      success: function (msg) {
        // show a visible Bootstrap-like alert above the form
        var $form = $(".contactForm");
        var $alert = $form.prev(".cf-alert");
        if ($alert.length === 0) {
          $alert = $('<div class="cf-alert" />').insertBefore($form);
        }
        $alert
          .stop(true, true)
          .hide()
          .removeClass("alert-success alert-danger");

        if (msg == "OK") {
          $alert
            .addClass("alert-success")
            .html("<strong>Success!</strong> Your message has been sent.")
            .fadeIn();
          $("#sendmessage").addClass("show");
          $("#errormessage").removeClass("show");
          $form.find("input, textarea").val("");
          // fade out after 6s
          setTimeout(function () {
            $alert.fadeOut();
          }, 6000);
        } else {
          $alert
            .addClass("alert-danger")
            .html("<strong>Error:</strong> " + msg)
            .fadeIn();
          $("#sendmessage").removeClass("show");
          $("#errormessage").addClass("show").html(msg);
        }
      },
    });
    return false;
  });
});
