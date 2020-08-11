(function(window, jQuery) {
    window.Registration = function($wrapper, options) {

        this.options = $.extend({
            tel_script_url: null,
            default_country: 'FR'
        }, options);

        this.$wrapper = $wrapper;

        this.$telInput = this.$wrapper.find(".js-phone-validation");
        this.$errorMsg = this.$wrapper.find("#js-phone-error-validation");
        this.$validMsg = this.$wrapper.find("#js-phone-success-validation");
        this.$submitButton = this.$wrapper.find("#js-registration-button");
        this.$countryInput = this.$wrapper.find(".js-billing-address-country");

        this.resetTelInput();
        this.initTelInput();
        this.initTVAInput();

        // on keyup / change flag: reset
        this.$wrapper.on(
            'keyup change',
            '.js-phone-validation',
            this.resetTelInput.bind(this)
        );

        // on blur: validatez
        this.$wrapper.on(
            'blur change keypress keyup',
            '.js-phone-validation',
            this.validateTelInput.bind(this)
        );

        this.$wrapper.on(
            'blur',
            '.js-phone-validation',
            this.onChangeCountry.bind(this)
        );

        // if the user is not from france show the message to explain that the VAT is required
        this.$wrapper.on(
            'change',
            '.js-billing-address-country',
            this.onChangeCountry.bind(this)
        );
    };

    $.extend(window.Registration.prototype, {
        initTelInput: function () {
            this.$telInput.intlTelInput({
                utilsScript: this.options.tel_script_url,
                hiddenInput: this.$telInput.attr("name"),
                initialCountry: this.options.default_country,
                customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                    return selectedCountryPlaceholder.replace(/[^a-zA-Z ]/g, "x");
                }
            });
            this.$telInput.removeAttr("name");
        },
        initTVAInput: function() {
            // hide the message when the page is loaded, and set the default value of country to France
            if (this.$countryInput.val() === "FR"){
                this.$wrapper.find("#req-tva").hide();
            }else{
                this.$wrapper.find("#req-tva").show();
            }
        },
        onChangeCountry: function(e) {
            if (this.$countryInput.val() === "FR"){
                this.$wrapper.find("#req-tva").hide();
            }else{
                this.$wrapper.find("#req-tva").show();
            }
        },
        resetTelInput : function() {
            this.$telInput.removeClass("is-valid");
            this.$telInput.removeClass("is-invalid");
            this.$errorMsg.addClass("hide");
            this.$validMsg.addClass("hide");
            this.$submitButton.attr("disabled", false);
        },
        validateTelInput: function () {
            this.resetTelInput();

            if ($.trim(this.$telInput.val())) {
                if (this.$telInput.intlTelInput("isValidNumber")) {
                    this.$telInput.removeClass("is-invalid");
                    this.$telInput.addClass("is-valid");
                    this.$validMsg.removeClass("hide");
                    $("input[name='" + this.$telInput.attr("name") + "']").val(this.$telInput.intlTelInput("getNumber"));
                    this.$submitButton.attr("disabled", false);
                } else {
                    this.$telInput.removeClass("is-valid");
                    this.$telInput.addClass("is-invalid");
                    this.$errorMsg.removeClass("hide");
                    this.$submitButton.attr("disabled", true);
                }
            }
        }
    });
})(window, jQuery);
