(function(window, $) {
  window.OrderVerify = function(options) {
    // The default options.
    this.settings = $.extend({
      $wrapper: null,
      // These are the defaults.
      validate_img_url: "",
      refuse_img_url: "",
      show_img_url: ""
    }, options);

    this.settings.$wrapper.on(
      'click',
      '.js-confirm-picture',
      this.onConfirmPicture.bind(this)
    );

    this.settings.$wrapper.on(
      'click',
      '.js-refuse-picture',
      this.onRefusePicture.bind(this)
    );

    this.settings.$wrapper.on(
      'submit',
      '.js-refuse-form',
      this.onSubmitRefusePicture.bind(this)
    );

    this.settings.$wrapper.on(
      'ON_UPDATE_PICTURE',
      '.js-picture-card',
      this.onUpdatePicture.bind(this)
    );

    this.settings.$wrapper.find('.js-img-popup').magnificPopup({
      type: 'image',
      tLoading: 'Loading image #%curr%...'
    });

    this.settings.$wrapper.find('.popup-vimeo').magnificPopup({
      disableOn: 700,
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,
      tLoading: 'Loading image #%curr%...',
      fixedContentPos: false,
      closeOnContentClick: true
    });
  }
  $.extend(window.OrderVerify.prototype, {
    onRefusePicture: function(e) {
      e.preventDefault();
      this.settings.$wrapper.find('.js-refuse-form').attr('action', Routing.generate(this.settings.refuse_img_url, {
        'id': $(e.currentTarget).data('picture-detail-id')
      }));
      this.settings.$wrapper.find('.js-refuse-form').attr('picture-detail-id', $(e.currentTarget).data('picture-detail-id'));
      this.settings.$wrapper.find('#js-refuse-picture-modal').modal('show');
    },
    onConfirmPicture: function(e) {
      e.preventDefault();
      var self = this;
      $.ajax({
        url: Routing.generate(self.settings.validate_img_url, {
          'id': $(e.currentTarget).data('picture-detail-id')
        }),
        method: 'POST',
        data: {
          '_token': $(e.currentTarget).data('token')
        },
        async: false,
        cache: true,
        beforeSend: function(jqXHR, settings) {
          alertify.message('<div class="partial-loader"></div>', 0);
        },
        success: function(json) {
          alertify.dismissAll();
          if (json.success) {
            alertify.success(json.msg, 5);
            $(e.currentTarget).closest('.js-picture-card').replaceWith(json.orderDetail);
            self.settings.$wrapper.find('#js-order-header').replaceWith(json.order);
          } else {
            alertify.error(json.msg, 5);
          }
        },
        error: function(xhr, status, error) {
          alertify.dismissAll();
          var json = eval("(" + xhr.responseText + ")");
          alertify.notify(error, 'error');
        }
      });
    },
    onSubmitRefusePicture: function(e) {
      e.preventDefault();
      if ($.trim($(e.currentTarget).find('.js-commentary').val()).length === 0) {
        return false;
      }
      var self = this;
      var pictureDetail = $(e.currentTarget).attr('picture-detail-id');
      $.ajax({
        url: $(e.currentTarget).attr('action'),
        method: $(e.currentTarget).attr('method'),
        data: $(e.currentTarget).serializeArray(),
        async: false,
        cache: true,
        beforeSend: function(jqXHR, settings) {
          alertify.message('<div class="partial-loader"></div>', 0);
        },
        success: function(json) {
          alertify.dismissAll();
          if (json.success) {
            alertify.success(json.msg, 5);
            self.settings.$wrapper.find('#js-picture-card-' + pictureDetail).replaceWith(json.orderDetail);
            self.settings.$wrapper.find('#js-order-header').replaceWith(json.order);
            self.settings.$wrapper.find('#js-refuse-picture-modal').modal('hide');
            $(e.currentTarget)[0].reset();
          } else {
            alertify.error(json.msg, 5);
          }
        },
        error: function(xhr, status, error) {
          alertify.dismissAll();
          var json = eval("(" + xhr.responseText + ")");
          alertify.notify(error, 'error');
        }
      });
    },
    onUpdatePicture: function(e, id) {
      e.preventDefault();
      var self = this;
      $.ajax({
        url: Routing.generate(self.settings.show_img_url, {
          'id': id
        }),
        method: 'GET',
        async: false,
        cache: true,
        success: function(json) {
          $(e.currentTarget).replaceWith(json.orderDetail);
          self.settings.$wrapper.find('#js-order-header').replaceWith(json.order);
        },
        error: function(xhr, status, error) {
          alertify.dismissAll();
          var json = eval("(" + xhr.responseText + ")");
          alertify.notify(error, 'error');
        }
      });
    }
  });
})(window, jQuery);
