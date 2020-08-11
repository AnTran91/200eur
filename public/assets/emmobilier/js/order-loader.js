(function(window, $) {
    /* LOADER */
    // WHEN PAY LATER BUTTON IS CLICKED
    $(document).on("click", ".js-create-order", function(e) {
        e.preventDefault();
        var _self = this;
        var href = $(_self).data('href');
        $.ajax({
            url: href,
            method: "POST",
            data: {
                '_token': $(_self).data('token')
            },
            cache: true,
            async: true,
            beforeSend: function() {
                $('body').css("height", "100%");
                $('#js-loader').removeAttr("style");
                $('#js-body-content').css("display", "none");
            },
            success: function(json) {
                if (json.success) {
                    window.location.replace(json.targetURL);
                } else {
                    $('body').removeAttr("style");
                    $('#js-loader').css("display", "none");
                    $('#js-body-content').removeAttr("style");

                    alertify.error(json.msg);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('body').removeAttr("style");
                $('#js-loader').css("display", "none");
                $('#js-body-content').removeAttr("style");

                alertify.error(window.deleteError);
            }
        });
    });
    /* END LOADER */
})(window, jQuery);
