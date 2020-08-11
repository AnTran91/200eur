(function(window, alertify, jQuery) {
    window.Order = function($wrapper, deliveryTimeID) {

        this.$wrapper = $wrapper;
        this.deliveryTimeID = deliveryTimeID;

        this.$wrapper.find('.js-file-upload').each(this.uploadImg);

        this.$wrapper.on(
            'change',
            '.js-retouch-options',
            this.updatePrice.bind(this)
        );

        this.$wrapper.on(
            'change',
            '.js-retouch-options',
            this.updateTitle.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-picture-detail-param',
            this.showParams.bind(this)
        );

        $(document).on(
            'change',
            '.js-renovation-choices',
            this.handleImagePicker.bind(this)
        );

        $(document).on(
            'change',
            'input[type="file"]',
            this.handleImagePreview.bind(this)
        );

        $(document).on(
            'change',
            '[data-onchange] input',
            this.onChangeParamFields.bind(this)
        );

        $(document).on(
            'click',
            '.js-placement-button',
            this.handlePlacement.bind(this)
        );

        alertify.genericDialog || alertify.dialog('genericDialog', this.genericDialog.bind(this));

        this.$wrapper.find('.js-pictures-collection').collection({
            prefix: 'pictures',
            allow_up: false,
            allow_down: false,
            children: [{
                selector: '.js-child-collection',
                prefix: 'picture-detail',
                allow_up: false,
                allow_down: false,
                add_at_the_end: true,
                add: '<a class="mt-5 btn btn-icon btn-dark float-right" href="#"><span class="fas fa-plus"></span></a>',
                min: 1,
                after_add: function(collection, element) {
                    $(element).find('.js-retouch-options').trigger('change');
                    var $uploadElem = $(element).find('.js-disabled-file-upload');
                    $uploadElem.kendoUpload({
                        multiple: false,
                        localization: {
                            select: $uploadElem.data('label')
                        }
                    });
                    $uploadElem.data("kendoUpload").disable();
                },
                after_remove: function(collection, element) {
                    if ($(element).data('id') !== undefined) {
                        $.ajax({
                            url: $(element).data('href'),
                            method: "DELETE",
                            data: {
                                'data': $(element).data('id'),
                                '_token': $(element).data('token')
                            },
                            success: function(json) {
                                if (json.success) {
                                    alertify.success(json.msg);
                                } else {
                                    alertify.error(json.msg);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alertify.error("something went wrong sorry about that. please try again later.", 5);
                            }
                        });
                    }
                }
            }]
        });

    };

    $.extend(window.Order.prototype, {
        genericDialog: function() {
            var self = this;
            return {
                main: function(content) {
                    this.setContent(content);
                    return this;
                },
                hooks: {
                    // triggered when the dialog is shown, this is seperate from user defined onshow
                    onshow: function() {
                        var $form = $(this.elements.body.querySelector("form"));
                        $form.submit(function(event) {
                            event.preventDefault();
                            var msg = {};
                            // submit the form
                            $form.ajaxSubmit({
                                forceSync: true,
                                beforeSend: function(jqXHR, settings) {
                                    msg = alertify.message('<div class="partial-loader-section"><div class="partial-loader"></div><p class="partial-loader-text">00%</p></div>', 0);
                                },
                                uploadProgress: function(event, position, total, percentComplete) {
                                    msg.setContent('<div class="partial-loader-section"><div class="partial-loader"></div><p class="partial-loader-text">' + percentComplete + ' %</p></div>', 0);
                                },
                                data: function() {
                                    $form.find('input:disabled').addClass("disabled");
                                    $form.find('.disabled').prop("disabled", false);
                                    var data = $form.find('.disabled').serializeArray();
                                    $form.find('.disabled').prop("disabled", true);
                                    $form.find('input:disabled').removeClass("disabled");
                                    return data;
                                },
                                success: function(json) {
                                    alertify.dismissAll();
                                    if (json.success) {
                                        alertify.success(json.msg, 5).dismissOthers();
                                    } else {
                                        if (jQuery.type(json.msg) === "string") {
                                            alertify.error(json.msg, 5).dismissOthers();
                                        } else {
                                            for (var i in json.msg) {
                                                var msg = "";
                                                msg += "<p>" + i + "</p>";
                                                for (var j in json.msg[i]) {
                                                    var key = '';
                                                    if (j.length > 0) {
                                                        key = j + " : ";
                                                    }
                                                    msg += "<p>" + key + json.msg[i][j] + "</p>";
                                                }
                                                alertify.error(msg);
                                            }
                                        }
                                    }
                                },
                                error: function(XMLHttpRequest, textStatus, errorThrown) {
                                    alertify.error(errorThrown, 5).dismissOthers();
                                }
                            });
                        });

                        $(this.elements.body).find('.js-disabled-block').each(function() {
                            var $imgTag = $(this).parents(".js-param-col").next().find(".js-img-preview");
                            $imgTag.attr('src', $(this).data('src'));
                        });

                        $(this.elements.body).find('input[type="file"]').each(function() {
                            var $imgTag = $(this).parents(".js-param-col").next().find(".js-img-preview");
                            $imgTag.attr('src', $(this).data('src'));
                        });

                        $(this.elements.body).find('.js-renovation-choices').each(function() {
                            var $imgTag = $(this).closest('.js-renovation-section').find('.js-renovation-img-thumb');
                            if ($(this).find(':selected').data("img-src") !== "") {
                                $imgTag.attr('src', $(this).find(':selected').data("img-src"));
                            }
                        });

                        $(this.elements.body).find('.js-renovation-choices').imagepicker({
                            hide_select: true,
                            show_label: false
                        });

                        // Set the defaut HTML
                        $(this.elements.body)
                            .find('.js-picture-settings input')
                            .prop("disabled", true)
                            .addClass("disabled");

                        $(this.elements.body)
                            .find('[data-toggle="popover"]')
                            .popover();
                    },
                    // triggered when the dialog is closed, this is seperate from user defined onclose
                    onclose: function() {
                        console.log('onclose');
                    },
                    // triggered when a dialog option gets updated.
                    // IMPORTANT: This will not be triggered for dialog custom settings updates ( use settingUpdated instead).
                    onupdate: function() {
                        console.log('onupdate');
                    }
                },
                setup: function() {
                    return {
                        focus: {
                            element: function() {
                                return this.elements.body.querySelector(this.get('selector'));
                            },
                            select: true
                        },
                        prepare: function() {
                            self.initHtml(this.elements.body);
                        },
                        options: {
                            basic: true,
                            'resizable': false,
                            'startMaximized': true,
                            transition: 'zoom'
                        }
                    };
                },
                settings: {
                    selector: undefined
                }
            };
        },
        handleImagePreview: function(e) {
            var $imgTag = $(e.currentTarget).parents(".js-param-col").next().find(".js-img-preview");
            if ($imgTag.length === 0) {
                $(e.currentTarget).parents(".js-param-col").find(".js-file-label").html($(e.currentTarget)[0].files[0].name);
            }
            if ($(e.currentTarget)[0] && $(e.currentTarget)[0].files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $imgTag.attr('src', e.target.result);
                }
                reader.readAsDataURL($(e.currentTarget)[0].files[0]);
            }
        },
        handlePlacement: function(e) {
            var $placementList = $(e.currentTarget).parents('div .js-placement-list');
            if (!$placementList.find(':input').is(':disabled')) {
                $placementList.find(".pink-r-button").removeClass("pink-r-button");
                $placementList.find(':input').val($(e.currentTarget).data("value"));

                $(e.currentTarget).addClass("pink-r-button");
            }
        },
        onChangeParamFields: function(event) {
            var $form = $(event.currentTarget).closest('form');
            if ($(event.currentTarget).val() != $(event.currentTarget).closest("[data-onchange]").data("value")) {
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"]').prop("disabled", false);
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"] input').prop("disabled", false);
            } else {
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"]').prop("disabled", true);
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"] input').prop("disabled", true);
            }
        },
        showParams: function(e) {
            e.preventDefault();
            var self = this;
            var msg = {};
            var retouchSelected = $(e.currentTarget).closest('.js-picture-detail-section').find('select option:selected');
            console.log(retouchSelected.val());
            var id_picture_detail = $(e.currentTarget).data('picture-detail-id');
            if(id_picture_detail === undefined) {
                id_picture_detail = $($(".js-picture-detail-param")[0]).data('picture-detail-id');
            }
            //if (!self.$wrapper.find('#js-wallet-modal' + $(e.currentTarget).data('param-id') + retouchSelected.val()).length) {
            $.ajax({
                url: Routing.generate('admin_render_param_form', {
                    id_picture_detail: id_picture_detail,
                    id_retouch: retouchSelected.val()
                }),
                method: "GET",
                async: false,
                cache: true,
                beforeSend: function(jqXHR, settings) {
                    msg = alertify.message('<div class="partial-loader"></div>', 0);
                },
                success: function(html) {
                    msg.dismiss();
                    alertify.genericDialog(html).set('selector', 'form');
                },
                error: function(xhr, status, error) {
                    msg.dismiss();
                    alertify.error("something went wrong sorry about that. please try again later.", 5);
                }
            });
            //}
            //self.$wrapper.find('#js-param-modal' + $(e.currentTarget).data('param-id') + retouchSelected.val()).modal('show');
        },
        updatePrice: function(e) {
            var price = 0;
            if ($(e.currentTarget).find('option:selected')[0].hasAttribute('data-standard')) {
                price = $(e.currentTarget).find('option:selected').data('standard');
            } else {
                price = $(e.currentTarget).find('option:selected').data(this.deliveryTimeID);
            }
            $(e.currentTarget).closest('.js-picture-detail-section').find('.js-retouch-price').val(parseFloat(price).toFixed(2));
        },
        updateTitle: function(e) {
            var option_text = $(e.currentTarget).find('option:selected').text();
            $(e.currentTarget).closest('div.card').find('.card-header > h3.card-title').text(option_text);
        },
        handleImagePicker: function(event) {
            $(event.currentTarget).closest('.js-renovation-section').find('.js-renovation-img-thumb').attr('src', $(event.currentTarget).find(':selected').data("img-src"));
        },
        initHtml: function(html) {
            $(html).find('.js-disabled-block').each(function() {
                var $imgTag = $(this).parents(".js-param-col").next().find(".js-img-preview");
                $imgTag.attr('src', $(this).data('src'));
            });

            $(html).find('input[type="file"]').each(function() {
                var $imgTag = $(this).parents(".js-param-col").next().find(".js-img-preview");
                $imgTag.attr('src', $(this).data('src'));
            });

            $(html).find('.js-renovation-choices').each(function() {
                var $imgTag = $(this).closest('.js-renovation-section').find('.js-renovation-img-thumb');
                if ($(this).find(':selected').data("img-src") !== "") {
                    $imgTag.attr('src', $(this).find(':selected').data("img-src"));
                }
            });

            $(html).find('.js-renovation-choices').imagepicker({
                hide_select: true,
                show_label: false
            });

            // Set the defaut HTML
            $(html)
                .find('.js-picture-settings input')
                .prop("disabled", true)
                .addClass("disabled");

            $(html)
                .find('[data-toggle="popover"]')
                .popover();
        },
        uploadImg: function() {
            $(this).kendoUpload({
                multiple: false,
                async: {
                    chunkSize: $(this).data('chunk-size'),
                    saveUrl: $(this).data('upload-url'),
                    removeUrl: $(this).data('delete-url'),
                    autoUpload: true
                },
                validation: {
                    allowedExtensions: $(this).data('allowed-extensions'),
                    maxFileSize: $(this).data('size-limit')
                },
                files: $(this).data('init-files'),
                localization: {
                    select: $(this).data('label')
                },
                success: onSuccess,
                error: onError
            });

            function onError(e) {
                alertify.error(e.response.error, 5);
            }

            function onSuccess(e) {
                if (e.operation == "upload") {
                    e.files[0].name = e.response.fileName;
                    $(this.element).closest('.js-picture-detail-section').find('.k-file-name').html(e.response.fileName);
                    $(this.element).closest('.js-picture-detail-section').find('.js-file-name').val(e.response.fileName);
                }
                alertify.success(e.response.msg, 5);
            }
        },
    });
})(window, alertify, jQuery);
