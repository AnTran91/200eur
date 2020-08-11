(function (window, $) {
    window.Retouch = function ($wrapper) {

        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'change',
            '.js-update-the-form',
            this.onChange.bind(this)
        )

        this.$wrapper.on(
            'change',
            '.js-retouch-type',
            this.onChange.bind(this)
        )

        this.init.bind(this);

        this.init();
    };

    $.extend(window.Retouch.prototype, {
        init: function () {
            if (this.$wrapper.find('.js-collection').length > 0) {
                this.$wrapper.find('.js-collection').collection({
                    allow_up: false,
                    allow_down: false,
                    add_at_the_end: true,
                    min: 1,
                    after_init: function(collection) {
                        $(collection).find(".js-delivery-time").chained(".js-retouch-type");
                    },
                    after_add: function(collection, element) {
                        $(element).find(".js-delivery-time").chained(".js-retouch-type");
                    },
                });
            }

            this.$wrapper.find('.js-wyswyg-selector').trumbowyg();
        },
        handleSubmit: function ($form) {
            var self = this;
            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method'),
                data: $form.serializeArray(),
                beforeSend: function () {
                    self.$wrapper.find('#dynamic-body').css('opacity', '0.2');
                    self.$wrapper.find('#dynamic-body').append('<div class="loader"></div>');
                },
                success: function (html) {
                    var $content = $(html).find('#dynamic-body');
                    $content.find('.form-group').removeClass('is-invalid');
                    $content.find('.form-control').removeClass('is-invalid');
                    $content.find('.form-group').each(function () {
                        $(this).find('.invalid-feedback').remove();
                    });
                    self.$wrapper.find('#dynamic-body').replaceWith($content);
                    self.init();
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    self.$wrapper.find('#dynamic-body').css('opacity', '1');
                    self.$wrapper.find('#dynamic-body .loader').remove();
                    alertify.error("something went wrong sorry about that. please try again later.", 5);
                }
            });
        },
        onChange: function (e) {
            e.preventDefault();
            this.handleSubmit($(e.currentTarget).closest('form'));
        }
    });
})(window, jQuery);
