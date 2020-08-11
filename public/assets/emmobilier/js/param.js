(function(window, jQuery) {
    window.Uploader = function($wrapper, options) {

        this.$wrapper = $wrapper;

        this.options = $.extend({
            new_picture_form_url: null,
            param_list_url: null,
            new_param_form_url: null
        }, options);

        this.$wrapper.on(
            'change',
            '#js-picture-retouch input',
            this.updatePictureRetouch.bind(this)
        );

        this.$wrapper.on(
            'click',
            '#js-next-step',
            this.validateCurrentStep.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-collapse-params',
            this.handleCollapsibleArea.bind(this)
        );

        this.$wrapper.on(
            'change',
            '.js-checkbox-uploader',
            this.handlePictureCheck.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-check-all-uploader',
            this.handleCheckAllPicture.bind(this)
        );

        this.$wrapper.on(
            'change',
            'input[type="file"]',
            this.handleImagePreview.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-clear-input-file',
            this.clearInputFile.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-placement-button',
            this.handlePlacement.bind(this)
        );

        this.$wrapper.on(
            'show.bs.collapse',
            '.accordion',
            this.handleRetouchCollapse.bind(this)
        );

        this.$wrapper.on(
            'submit',
            '.js-picture-detail',
            this.handleParamFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'show.bs.collapse',
            '.js-param-toggle',
            this.paramShowCollapse.bind(this)
        );

        this.$wrapper.on(
            'hide.bs.collapse',
            '.js-param-toggle',
            this.paramHideCollapse.bind(this)
        );

        this.$wrapper.on(
            'change',
            '[data-onchange] input',
            this.onChangeParamFields.bind(this)
        );

        this.$wrapper.on(
            'change',
            '.js-renovation-choices',
            this.handleImagePicker.bind(this)
        );

        this.$wrapper.on(
            'change',
            '.js-picture-detail',
            this.onChangeParamFormFields.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-delete-file',
            this.onDeleteParamFile.bind(this)
        );

        $(document).on(
            'ON_UPLOAD_FILE', {},
            this.onUploadFile.bind(this)
        );

        $(document).on(
            'ON_DELETE_UPLOADED_FILE', {},
            this.onDeleteFile.bind(this)
        );

        // Set the defaut HTML
        this.$wrapper
            .find('.js-picture-settings input')
            .prop("disabled", true)
            .addClass("disabled");

        this.$wrapper
            .find('[data-toggle="popover"]')
            .popover();
    };

    $.extend(window.Uploader.prototype, {
        onDeleteParamFile: function(e){
            e.preventDefault();

            $.ajax({
                url: $(e.currentTarget).data('href'),
                data: {
                    '_token': $(e.currentTarget).data('token'),
                    'file_path': $(e.currentTarget).data('file')
                },
                method: "POST",
                success: function(responseJSON) {
                    if (responseJSON.success){
                        $(e.currentTarget).closest('.fileuploader-items').remove();
                        alertify.success(responseJSON.msg, 5);
                    }else {
                        alertify.error(responseJSON.msg, 5);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    var json = eval("(" + XMLHttpRequest.responseText + ")");
                    alertify.error(textStatus, 5);
                }
            });
        },
        onChangeParamFormFields: function(e) {
            if ($(e.currentTarget).find('.js-retouch-form:checked').fieldValue().includes("3")) {
                var self = this;
                $.ajax({
                    url: Routing.generate(this.options.update_param_form_url, {
                        uuid: $(e.currentTarget).closest('.js-picture-detail').data('uuid'),
                        id: "3"
                    }),
                    method: "GET",
                    cache: true,
                    success: function(json) {
                        if (json["params"]["field_renovation"] !== null) {
                            if (json["params"]["field_renovation"][1] !== "" ||
                                json["params"]["field_renovation"][2] !== "") {
                                $(e.currentTarget).find('.js-save-settings').prop("disabled", false);
                            }
                        } else if (
                            ($(e.currentTarget).find('#_field_renovation_1').val() !== "" &&
                                $(e.currentTarget).find('#_field_renovation_1').val() !== undefined)
                            ||
                            ($(e.currentTarget).find('#_field_renovation_2').val() !== "" &&
                                $(e.currentTarget).find('#_field_renovation_2').val() !== undefined)
                            ) {
                            $(e.currentTarget).find('.js-save-settings').prop("disabled", false);
                        } else {
                            $(e.currentTarget).find('.js-save-settings').prop("disabled", true);
                        }
                    },
                    error: function(xhr, status, error) {
                        var json = eval("(" + XMLHttpRequest.responseText + ")");
                        alertify.error(json.error, 5);
                    }
                });
            } else if  (
                    (
                        $(e.currentTarget).find(".js-retouch-form:checked").fieldValue().length > 0
                        &&
                        $(e.currentTarget).find(".js-retouch-form:checked").fieldValue().includes("3") === false
                    )
                    ||
                    (
                        $(e.currentTarget).find(".js-retouch-form:checked").fieldValue().includes("3")
                        &&
                        (
                            $(e.currentTarget).find('#_field_renovation_1').val() !== "" &&
                            $(e.currentTarget).find('#_field_renovation_1').val() !== undefined
                        )
                        ||
                        (
                            $(e.currentTarget).find('#_field_renovation_2').val() !== "" &&
                            $(e.currentTarget).find('#_field_renovation_2').val() !== undefined
                        )
                    )
                )
            {
                $(e.currentTarget).find('.js-save-settings').prop("disabled", false);
            } else {
                $(e.currentTarget).find('.js-save-settings').prop("disabled", true);
            }
        },
        onChangeParamFields: function(event) {
            var $form = $(event.currentTarget).closest('.accordion-content');
            if ($(event.currentTarget).val() != $(event.currentTarget).closest("[data-onchange]").data("value")) {
                console.log("disabled false");
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"]').prop("disabled", false);
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"] input').prop("disabled", false);
            } else {
                console.log("disabled true");
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"]').prop("disabled", true);
                $form.find('[data-dependon="' + $(event.currentTarget).closest("[data-onchange]").data("onchange") + '"] input').prop("disabled", true);
            }
        },
        paramShowCollapse: function(event) {
            var $input = $(event.currentTarget).find("i.fas");
            $input.removeClass("fa-angle-right");
            $input.addClass("fa-angle-down");
            console.log("show");
        },
        paramHideCollapse: function(event) {
            var $input = $(event.currentTarget).find("i.fas");
            $input.removeClass("fa-angle-down");
            $input.addClass("fa-angle-right");
            console.log("hide");
        },
        onUploadFile: function(event, id, picture_id) {
            var self = this;
            $.ajax({
                url: Routing.generate(this.options.new_picture_form_url, {
                    'id': picture_id
                }),
                data: {
                    'id': id
                },
                method: "GET",
                success: function(html) {
                    self.$wrapper.find('.js-pictrue-detail-section').append(html);
                    self.initHtml();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    var json = eval("(" + XMLHttpRequest.responseText + ")");
                    alertify.error(textStatus, 5);
                }
            });
        },
        onDeleteFile: function(event, uuid) {
            $("#js-uuid-" + uuid).remove();
        },
        handlePlacement: function(e) {
            var $placementList = $(e.currentTarget).parents('div .js-placement-list');
            if (!$placementList.find(':input').is(':disabled')) {
                $placementList.find(".pink-r-button").removeClass("pink-r-button");
                $placementList.find(':input').val($(e.currentTarget).data("value"));

                $(e.currentTarget).addClass("pink-r-button");
            }
        },
        handleRetouchCollapse: function(e) {
            this.updateParamsForm($(e.currentTarget).data("uuid"), $(e.currentTarget).data("id"), $(e.currentTarget).find('.accordion-content'));
        },
        clearInputFile: function(e){
            e.preventDefault();

            var file = $(e.currentTarget).closest('.fileuploader-items').closest(".image-uploader-ctn").find('input[type="file"]');
            $(e.currentTarget).closest('.fileuploader-items').remove();

            console.log(file);
            file.val('');

            var $imgTag = file.parents(".js-param-col").next().find(".js-img-preview");

            console.log($imgTag);
            console.log(file.data('default'));
            $imgTag.attr('src', file.data('default'));
        },
        handleImagePreview: function(e) {
            var file = $(e.currentTarget)[0].files[0];
            if ($(e.currentTarget).parents(".js-param-col").find(".fileuploader-items").length > 0){
                $(e.currentTarget).parents(".js-param-col").find(".fileuploader-items").empty();
            }

            $(e.currentTarget).parents(".js-param-col").find(".image-uploader-ctn").append('<div class="fileuploader-items">\n' +
                '            <ul class="fileuploader-items-list">\n' +
                '                <li class="fileuploader-item file-has-popup file-type-image file-ext-png">\n' +
                '                    <div class="columns">\n' +
                '                        <div class="column-thumbnail">\n' +
                '                            <div class="fileuploader-item-image fileuploader-no-thumbnail">\n' +
                '                                <div style="background-color: #fab6c0" class="fileuploader-item-icon">\n' +
                '                                    <i>'+ file.name.split('.').pop().toUpperCase() +'</i>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <span class="fileuploader-action-popup"></span>\n' +
                '                        </div>\n' +
                '                        <div class="column-title">\n' +
                '                            <div title="'+ file.name +'">'+ file.name +'</div>\n' +
                '                            <span>'+ file.size.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') +' KB</span></div>\n' +
                '                        <div class="column-actions">\n' +
                '                            <a href="#" class="fileuploader-action fileuploader-action-remove js-clear-input-file"><i class="fas fa-trash"></i></a>\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                    <div class="progress-bar2">\n' +
                '                        <span></span>\n' +
                '                    </div>\n' +
                '                </li>\n' +
                '            </ul>\n' +
                '        </div>');

            var $imgTag = $(e.currentTarget).parents(".js-param-col").next().find(".js-img-preview");

            if ($(e.currentTarget)[0] && $(e.currentTarget)[0].files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $imgTag.attr('src', e.target.result);
                }
                reader.readAsDataURL($(e.currentTarget)[0].files[0]);
            }
        },
        handleCollapsibleArea: function(e) {
            if ($(e.currentTarget).attr("aria-expanded") == "true") {
                $(e.currentTarget).find("i.fas")
                    .removeClass("fa-angle-down")
                    .addClass("fa-angle-right");
            } else {
                $(e.currentTarget).find("i.fas")
                    .removeClass("fa-angle-right")
                    .addClass("fa-angle-down");
            }
        },
        handleCheckAllPicture: function(e) {
            e.preventDefault();
            this.$wrapper.find(".js-checkbox-uploader").trigger("change");
        },
        handlePictureCheck: function(e) {
            e.preventDefault();
            if (this.$wrapper.find(".js-checkbox-uploader:checked").fieldValue().length === 0) {
                this.$wrapper
                    .find('.js-picture-settings input')
                    .prop("disabled", true)
                    .addClass("disabled");
            } else {
                this.$wrapper
                    .find('.js-picture-settings input')
                    .prop("disabled", false)
                    .removeClass("disabled");

            }
        },
        updatePictureRetouch: function(e) {
            e.preventDefault();

            var $form = $(e.currentTarget).closest('form');
            var $uuid = $(e.currentTarget).closest('.js-picture-detail').data('uuid');
            var self = this;

            $.ajax({
                url: Routing.generate(this.options.param_list_url, {
                    'id': $uuid
                }),
                method: "POST",
                data: $form.serialize(),
                cache: true,
                beforeSend: function() {
                    self.$wrapper.find('#js-params-' + $uuid).html('<div class="lt-px-auto-small animated fadeIn"><div class="partial-loader"></div></div>');
                },
                success: function(html) {
                    self.$wrapper.find('#js-params-' + $uuid).html(html);
                    self.$wrapper.find('#js-params-' + $uuid)
                        .closest('.js-picture-detail')
                        .find('#js-save-setting-icon')
                        .removeClass("fas fa-check-circle");

                    self.initHtml();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    var json = eval("(" + XMLHttpRequest.responseText + ")");
                    alertify.error(json.error, 5);
                }
            });
        },
        getTheDisabledData: function($form) {
            $form.find('input:disabled').addClass("disabled");
            $form.find('.disabled').prop("disabled", false);
            var data = $form.find('.disabled').serializeArray();
            $form.find('.disabled').prop("disabled", true);
            $form.find('input:disabled').removeClass("disabled");
            return data;
        },
        handleParamFormSubmit: function(e) {
            e.preventDefault();
            var self = this;
            var $form = $(e.currentTarget).closest("form");
            var msg = {};
            // submit the form
            $form.ajaxSubmit({
                forceSync: true,
                beforeSend: function(jqXHR, settings) {
                    msg = alertify.message('<div class="partial-loader-section"><p class="partial-loader-text">00 %</p><div class="partial-loader"></div></div>', 0);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    msg.setContent('<div class="partial-loader-section"><p class="partial-loader-text">' + percentComplete + ' %</p><div class="partial-loader"></div></div>', 0);
                },
                data: self.getTheDisabledData($form),
                success: function(json) {
                    $form.find('.js-save-settings')
                        .prop("disabled", true)
                    $form.find('#js-save-setting-icon')
                        .addClass("fas fa-check-circle");
                    alertify.dismissAll();
                    if (json.success) {
                        alertify.success(json.msg, 5).dismissOthers();
                        var $retouchs_section = self.$wrapper.find(".qq-file-id-" + $form.data("id")).find(".js-retouchs-list-group");

                        $retouchs_section.empty();
                        $form.find(".js-retouch-form:checked").each(function(i, retouch) {
                            var retouch_title = $(retouch).parent(".js-retouch-checkbox").find(".js-retouch-label").html();
                            // add node
                            $retouchs_section.append('<span class="tag"><a><i class="fas fa-tag"></i> ' + retouch_title + '</a></span>');
                        });
                        $form.find('input[type="file"]').clearFields();
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
                                alertify.error(msg, 5);
                            }
                        }
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alertify.error(errorThrown, 5).dismissOthers();
                }
            });
        },
        handleImagePicker: function(event) {
            $(event.currentTarget).closest('.js-renovation-section').find('.js-renovation-img-thumb').attr('src', $(event.currentTarget).find(':selected').data("img-src"));
        },
        initHtml: function() {
            this.$wrapper.find('.js-disabled-block').each(function() {
                var $imgTag = $(this).parents(".js-param-col").next().find(".js-img-preview");
                $imgTag.attr('src', $(this).data('src'));
            });

            this.$wrapper.find('input[type="file"]').each(function() {
                var $imgTag = $(this).parents(".js-param-col").next().find(".js-img-preview");
                $imgTag.attr('src', $(this).data('src'));
            });

            this.$wrapper.find('.js-renovation-choices').each(function() {
                var $imgTag = $(this).closest('.js-renovation-section').find('.js-renovation-img-thumb');
                if ($(this).find(':selected').data("img-src") !== "") {
                    $imgTag.attr('src', $(this).find(':selected').data("img-src"));
                }
            });

            this.$wrapper.find('.js-renovation-choices').imagepicker({
                hide_select: true,
                show_label: false
            });

            // Set the defaut HTML
            this.$wrapper
                .find('.js-picture-settings input')
                .prop("disabled", true)
                .addClass("disabled");

            this.$wrapper
                .find('[data-toggle="popover"]')
                .popover();
        },
        updateParamsForm: function($uuid, $id, $elem) {
            var self = this;
            if ($elem.children().length === 0) {
                $.ajax({
                    url: Routing.generate(this.options.new_param_form_url, {
                        uuid: $uuid,
                        id: $id
                    }),
                    method: "GET",
                    cache: true,
                    beforeSend: function() {
                        $elem.html('<div class="lt-px-auto-small animated fadeIn"><div class="partial-loader"></div></div>');
                    },
                    success: function(html) {
                        $elem.html(html);
                        self.initHtml();
                    },
                    error: function(xhr, status, error) {
                        var json = eval("(" + XMLHttpRequest.responseText + ")");
                        alertify.error(json.error, 5);
                    }
                });
            }
        },
        handlePictureDelete: function(e) {
            var $id = $(e.currentTarget).data('id');
            this.galleryUploader.deleteFile($id);
        },
        validateCurrentStep: function(e) {
            e.preventDefault();
            var self = this;
            var $msg = $(e.currentTarget).data('msg');
            var $error = true;
            $(".js-picture-detail").each(function(index) {
                console.log($(this).find(".js-retouch-form:checked").val());
                if ($(this).find(".js-retouch-form:checked").val() === undefined || $(this).find(".js-retouch-form:checked").val() === null || $(this).find("#js-save-setting-icon").prop('class')  === undefined || $(this).find("#js-save-setting-icon").prop('class')  === '') {
                    alertify.error($msg + " " + $(this).data('picture-name'), 5);
                    $error = false;
                }
            });
            if ($error) {
                // similar behavior as an HTTP redirect
                window.location.assign($(e.currentTarget).data('href'));
            }
        }
    });
})(window, jQuery);
