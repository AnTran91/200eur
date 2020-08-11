(function(window, $) {
  $.fn.extend({
    animateCss: function(animationName, callback) {
      var animationEnd = (function(el) {
        var animations = {
          animation: 'animationend',
          OAnimation: 'oAnimationEnd',
          MozAnimation: 'mozAnimationEnd',
          WebkitAnimation: 'webkitAnimationEnd',
        };

        for (var t in animations) {
          if (el.style[t] !== undefined) {
            return animations[t];
          }
        }
      })(document.createElement('div'));

      this.addClass('animated ' + animationName).one(animationEnd, function() {
        $(this).removeClass('animated ' + animationName);

        if (typeof callback === 'function') callback();
      });
      return this;
    },
  });

  function validatePromoCode($form) {
    $.ajax({
      url: $form.attr('action'),
      type: $form.attr('method'),
      data: $form.serializeArray(),
      success: function(json) {
        var msg = $.isArray(json.msg) ? json.msg.join('</br>') : json.msg;
        if (json.success) {

          $form.find('.js-code-promo-input')
            .removeClass('is-invalid state-invalid')
            .addClass('is-valid state-valid');
          $form.find('.js-code-promo-txt')
            .empty()
            .append('<div class="valid-feedback d-block">' +
              '<span>' + msg + '</span>' +
              '</div>');
        } else {

          $form.find('.js-code-promo-input')
            .removeClass('is-valid state-valid')
            .addClass('is-invalid state-invalid');
          $form.find('.js-code-promo-txt')
            .empty()
            .append('<div class="invalid-feedback d-block">' +
              '<span>' + msg + '</span>' +
              '</div>');
        }

        updateOrderPrice($(document).find('#js-recap-form'));
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        var json = eval("(" + XMLHttpRequest.responseText + ")");
        alertify.error(json.error);
      }
    });
  }

  function updateOrderPrice($form) {
    var height = $(document).find('#js-recap-order-table').height();
    $.ajax({
      url: $form.attr('action'),
      type: $form.attr('method'),
      data: $form.serializeArray(),
      beforeSend: function() {
        $(document).find('#js-recap-order-table tr').css('opacity', '0.1');
        $(document).find('#js-recap-order-actions')
              .addClass('disabled')
              .css('opacity', '0.1');
        $(document).find('#js-recap-order-table').find('.table-loader').remove();
        $(document).find('#js-recap-order-table').append('<div class="table-loader" style="opacity: 1;"></div>');
      },
      success: function(json) {
        $(document).find('#js-recap-order-table').html(json.response);
        $(document).find('#js-recap-order-actions').html(json.actions);
        $(document).find('#js-recap-order-table').css('opacity', '1');
        $(document).find('#js-recap-order-actions').css('opacity', '1');
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alertify.error("Quelque chose d'inattendu s'est produit");
      }
    });
  }

  $(document).on('change', '.js-order-delivery-time', function(e) {
    e.preventDefault();
    var $form = $(this).closest('form');
    updateOrderPrice($form);
  });

  $(document).on('submit', '#js-recap-form', function(e) {
    e.preventDefault();
    var $form = $(this).closest('form');
    updateOrderPrice($form);
  });

  $(document).on('submit', '#js-promo-code-form', function(e) {
    e.preventDefault();
    var $form = $(this).closest('form');
    validatePromoCode($form);
  });
})(window, jQuery);
