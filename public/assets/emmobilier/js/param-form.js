(function(window, jQuery) {
  window.ParamDynamicForm = function($wrapper, options) {

    this.$wrapper = $wrapper;

    this.options = $.extend({
      new_param_form_url: null
    }, options);

    this.$wrapper.on(
      'click',
      '.js-collapse-params',
      this.handleCollapsibleArea.bind(this)
    );

    this.$wrapper.on(
      'show.bs.collapse',
      '.accordion',
      this.handleRetouchCollapse.bind(this)
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

    // Set the defaut HTML
    this.$wrapper
      .find('.js-picture-settings input')
      .prop("disabled", true)
      .addClass("disabled");

    this.$wrapper
      .find('[data-toggle="popover"]')
      .popover();
  };

  $.extend(window.ParamDynamicForm.prototype, {
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
    handleRetouchCollapse: function(e) {
      this.updateParamsForm($(e.currentTarget).data("uuid"), $(e.currentTarget).data("id"), $(e.currentTarget).find('.accordion-content'));
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
        .find('input')
        .attr("disabled", true)
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
  });
})(window, jQuery);
