(function (window, $) {
    window.DeliveryTime = function ($wrapper) {

        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'change',
            '.js-update-the-form',
            this.onChange.bind(this)
        )

        this.$wrapper.on(
            'change',
            '.js-app-type',
            this.onChange.bind(this)
        )
    };

    $.extend(window.DeliveryTime.prototype, {
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
