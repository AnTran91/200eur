(function (window, $) {
    $(document).on("change", ".js-order-status", function (e) {
        $(this).closest('form').submit();
    });

    /* LOADER */
    $(document).on("click", ".js-delete-order", function (e) {
        e.preventDefault();
        var _self = this;

        var href = $(_self).data('href');
        var token = $(_self).data('token');
        alertify.confirm(window.confirmText, window.deleteMsg, function () {
            $.ajax({
                url: href,
                method: "DELETE",
                data: {
                    '_token': token
                },
                cache: true,
                async: true,
                beforeSend: function () {
                    $('body').css("height", "100%");
                    $('#js-loader').removeAttr("style");
                    $('#js-body-content').css("display", "none");
                },
                success: function (json) {
                    $('body').removeAttr("style");
                    $('#js-loader').css("display", "none");
                    $('#js-body-content').removeAttr("style");

                    if (json.success) {
                        $(_self).parents('article').remove();

                        if (0 === $(document).find('article').length) {
                            $('.js-no-result').css('display', 'block');
                        }
                        alertify.success(json.msg);
                    } else {
                        alertify.error(json.msg);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $('body').removeAttr("style");
                    $('#js-loader').css("display", "none");
                    $('#js-body-content').removeAttr("style");

                    alertify.error(window.deleteError);
                }
            });
        }, function () {
            console.log("nope");
        }).set('labels', {
            ok: window.ok,
            cancel: window.cancel
        });

    });

})(window, jQuery);