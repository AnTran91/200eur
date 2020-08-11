$(document).on("click", "#submit_billing_address", function(event) {
  event.preventDefault();
  var $form = $(this).closest('form');
  // submit the form
  $form.ajaxSubmit({
    forceSync: true,
    beforeSend: function(jqXHR, settings ) {
      alertify.message('<div class="partial-loader"></div>', 0);
    },
    success: function(json) {
      if (json.success) {
        alertify.success(json.msg).dismissOthers();
        $("#billing_address_modal").modal('hide');
      } else {
        alertify.error(json.msg).dismissOthers();
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alertify.error(textStatus).dismissOthers();
    }
  });
});
