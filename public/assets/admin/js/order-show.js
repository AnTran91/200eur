(function(window, jQuery) {
  window.Order = function($wrapper) {

    this.$wrapper = $wrapper;

    this.$wrapper.on(
      'click',
      '.js-picture-detail-param',
      this.showParams.bind(this)
    );

    alertify.genericDialog || alertify.dialog('genericDialog', this.genericDialog.bind(this));

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
    showParams: function(e) {
      e.preventDefault();
      var self = this;
      var msg = {};
      var retouchSelected = $(e.currentTarget).closest('.js-picture-detail-section').find('select option:selected');

      //if (!self.$wrapper.find('#js-wallet-modal' + $(e.currentTarget).data('param-id') + retouchSelected.val()).length) {
      $.ajax({
        url: Routing.generate('admin_view_param_form', {
          id_picture_detail: $(e.currentTarget).data('picture-detail-id')
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
    }
  });

  $('div#js-picture-content').find('div.card-options > a').click(function(e) {
    e.preventDefault(); // Prevent browser's default download stuff...

    var url = $(this).prop("href");              // Anchor href 
    var downloadName = $(this).attr("download"); // Anchor download name

    var img = document.createElement("img");   // Create in-memory image
    img.addEventListener("load", () => {
        const a = document.createElement("a");   // Create in-memory anchor
        a.href = img.src;                        // href toward your server-image
        a.download = downloadName;               // :)
        a.click();                               // Trigger click (download)
    });
    img.src = window.location.origin + '/admin/order/load-external-image?url='+ url;       // Request image from your server
    return false;
  });
  // $('a.btn-primary').click(function(){
  //   console.log('simatai');
  //   var element = document.createElement('a');
  //   var clearUrl = '/uploads/files/trung-hau.nguyen-244e-4c25-8736-529ebfbb76c4/f89a64d5-a0c5-47c9-8a87-fbb9d37fc4be/92f2cb97-c983-49c3-89f5-1a241797704a/pc2.jpg';

  //   // element.setAttribute('href', 'data:attachment/image' + base64);
  //   element.setAttribute('href', 'data:application/octet-stream;base64,' + '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');
  //   element.setAttribute('download', 'filename.jpg');
  //   document.body.appendChild(element);
  //   element.click();
  //   document.body.removeChild(element)
  //   return false;
  // }) 
})(window, jQuery);
