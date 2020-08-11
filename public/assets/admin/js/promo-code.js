(function(window, jQuery, Math) {
  window.Promo = function($wrapper) {

    this.$wrapper = $wrapper;

    this.$wrapper.on(
      'click',
      '#js-promo-code-secure',
      this.generateUuid.bind(this)
    );

    this.$wrapper.on(
      'change',
      '.js-update-the-form',
      this.onChange.bind(this)
    )

    this.$wrapper.on(
      'change',
      '.js-promo-type',
      this.onChange.bind(this)
    )

    this.init.bind(this);

    this.init();
  };

  $.extend(window.Promo.prototype, {
    generateUuid: function(e) {
      $(e.currentTarget).closest('.input-group').find('input[type="text"]').val(this.uuid());
    },
    uuid: function() {
      var currentDate = new Date();
      return 'Cxxxxxxxxxxxx4xxx'.replace(/[xy]/g, function(cc) {
        var rr = Math.random() * 16 | 0;
        return (cc === 'x' ? rr : (rr & 0x3 | 0x8)).toString(16);
      }) + '-T' + moment().format("DDMMYYYY");
    },
    init: function() {
      this.$wrapper.find('.js-datepicker').each(function() {
        $(this).datetimepicker({
          locale: 'fr',
          format: 'L'
        });
      });

      if (this.$wrapper.find('.js-select').length > 0) {
        this.$wrapper.find('.js-select').each(function() {
          $(this).selectize();
        });
      }

      if (this.$wrapper.find('.js-collection').length > 0) {
        this.$wrapper.find('.js-collection').collection({
          allow_up: false,
          allow_down: false,
          add_at_the_end: true,
          min: 1
        });
      }
    },
    handleSubmit: function($form) {
      var self = this;
      $.ajax({
        url: $form.attr('action'),
        method: $form.attr('method'),
        data: $form.serializeArray(),
        beforeSend: function() {
          self.$wrapper.find('.card-body').css('opacity', '0.2');
          self.$wrapper.find('.card-body').append('<div class="loader"></div>');
        },
        success: function(html) {
          var $content = $(html).find('.card-body');
          $content.find('.form-group').removeClass('is-invalid');
          $content.find('.form-control').removeClass('is-invalid');
          $content.find('.form-group').each(function() {
            $(this).find('.invalid-feedback').remove();
          });
          self.$wrapper.find('.card-body').replaceWith($content);
          self.init();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          self.$wrapper.find('.card-body').css('opacity', '1');
          self.$wrapper.find('.card-body .loader').remove();
          alertify.error("something went wrong sorry about that. please try again later.", 5);
        }
      });
    },
    onChange: function(e) {
      e.preventDefault();
      this.handleSubmit($(e.currentTarget).closest('form'));
    }
  });
})(window, jQuery, Math);
