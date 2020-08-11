(function($, alertify) {
  $(document).on('click', '#js-wallet-show-modal', function(e) {
    e.preventDefault();
    if (!$(document).find('#js-wallet-modal').length) {
      $.ajax({
        url: Routing.generate('wallet_new'),
        method: "GET",
        async: false,
        cache: true,
        beforeSend: function(jqXHR, settings ) {
          alertify.message('<div class="partial-loader"></div>', 0);
        },
        success: function(html) {
           alertify.dismissAll();
          $(document).find('body').append(html);
        },
        error: function(xhr, status, error) {
          alertify.dismissAll();
          var json = eval("(" + xhr.responseText + ")");
          alertify.notify(error, 'error');
        }
      });
    }

    $(document).find('#js-wallet-modal').modal('show');
  });

  $(document).on("click", ".radio-box", function(e) {
    e.preventDefault();
    $(this).parents('#check-wallet-list').find(".checked").removeClass("checked");
    $(this).parents('#check-wallet-list').find(".checked").attr("checked", false);
    $(this).addClass("checked");
    $(this).find('input').attr("checked", true);

    if ($(this).data('toggle') === 'amount-section') {
      $(this).parents('#check-wallet-list').find("#js-custom-amount-section").removeClass("hide");
    }else{
      $(this).parents('#check-wallet-list').find("#js-custom-amount-section").addClass("hide");
    }
  });

  $(document).on("change", "#js-custom-amount-field", function(e) {
    // the value must be greater than 0
    if(this.value < 1)
      this.value = 1;
    e.preventDefault();
    console.log(this.value);
    $(this).parents('#check-wallet-list').find(".js-custom-amount").val(this.value);
  });
})(jQuery, alertify);
