(function(window, $, qq) {
  window.FineUploader = function(options) {
    // The default options.
    this.settings = $.extend({
      $wrapper: null,
      // These are the defaults.
      element: document.getElementById("js-fine-uploader-gallery"),
      template: 'qq-template-gallery',
      extra_buttons_element: document.getElementById("fine-uploader-folders"),
      translations: [],
      init_url: "/uploader/init",
      upload_url: "/uploader/upload",
      chunking_url: "/uploader/chunking",
      delete_url: "/uploader/delete"
    }, options);

    this.galleryUploader = new qq.FineUploader({
      debug: false,
      element: this.settings.element,
      template: this.settings.template,
      session: {
        endpoint: this.settings.init_url
      },
      request: {
        endpoint: this.settings.upload_url,
        requireSuccessJson: true
      },
      messages: {
        emptyError: this.settings.translations.emptyerror,
        minSizeError: this.settings.translations.minsizeerror,
        noFilesError: this.settings.translations.nofileserror,
        onLeave: this.settings.translations.onLeave,
        retryFailTooManyItemsError: this.settings.translations.retryfailtoomanyitemserror,
        sizeError: this.settings.translations.sizeerror,
        tooManyItemsError: this.settings.translations.toomanyitemserror,
        typeError: this.settings.translations.typeerror,
        unsupportedBrowserIos8Safari: this.settings.translations.unsupportedbrowserios8safari
      },
      deleteFile: {
        enabled: true,
        method: 'POST',
        endpoint: this.settings.delete_url,
        forceConfirm: true
      },
      chunking: {
        enabled: true,
        concurrent: {
          enabled: true
        },
        success: {
          endpoint: this.settings.chunking_url,
        }
      },
      resume: {
        enabled: true
      },
      retry: {
        enableAuto: true,
        maxAutoAttempts: 3,
        showButton: true
      },
      validation: {
        allowedExtensions: this.settings.translations.allowedextensions,
        itemLimit: this.settings.translations.itemlimit,
        sizeLimit: this.settings.translations.sizelimit
      },
      showMessage: this.showMessage.bind(this),
      showPrompt: this.showPrompt.bind(this),
      showConfirm: this.showConfirm.bind(this),
      callbacks: {
        onComplete: this.onComplete,
        onSessionRequestComplete: this.onSessionRequestComplete,
        onDeleteComplete: this.onDeleteComplete,
      }
    });

    this.settings.$wrapper.on(
      'click',
      '.qq-upload-toggle-all-selector',
      this.handleCheckAllPicture.bind(this)
    );

    this.settings.$wrapper.on(
      'click',
      '.js-delete-one-picture',
      this.deleteOnePicture.bind(this)
    );

    this.settings.$wrapper.on(
      'click',
      '.qq-upload-delete-all-selector',
      this.deleteAll.bind(this)
    );

  }

  $.extend(window.FineUploader.prototype, {
    handleCheckAllPicture: function(e) {
      e.preventDefault();
      this.settings.$wrapper.find('.js-checkbox-uploader').each(function() {
        if ($(this).is(':checked')) {
          $(this).prop("checked", false);
        } else {
          $(this).prop("checked", true);
        }
      });
    },
    deleteAll: function(e) {
      var self = this;
      var uuids = $.map(this.settings.$wrapper.find(".js-checkbox-uploader"), function(input) {
        return input.value;
      });
      if (uuids.length > 0) {
        alertify.confirm(this.settings.translations.confirm, this.settings.translations.multipledeletemsg, function() {
          self.multipleDelete(uuids);
        }, function() {
          console.log("nope");
        }).set('labels', {
          ok: this.settings.translations.ok,
          cancel: this.settings.translations.cancel
        });
      }
    },
    deleteOnePicture: function(e) {
      var self = this;
      alertify.confirm(this.settings.translations.confirm, this.settings.translations.deletemsg, function() {
        self.SingleDelete($(e.currentTarget).data('uuid'), $(e.currentTarget).data('id'));
        $(document).trigger("ON_DELETE_UPLOADED_FILE", [$(e.currentTarget).data('uuid')]);
      }, function() {
        console.log("nope");
      }).set('labels', {
        ok: this.settings.translations.ok,
        cancel: this.settings.translations.cancel
      });
    },
    SingleDelete: function(uuid, id) {
      var self = this;
      $.ajax({
        url: self.settings.delete_url,
        method: "POST",
        data: {
          "qquuid": uuid
        },
        cache: true,
        beforeSend: function() {
          $(self.galleryUploader.getItemByFileId(id)).find(".qq-upload-spinner-selector").removeClass("qq-hide");
        },
        success: function(json) {
          if (json.success) {
            alertify.success(json.msg, 5);
            qq(self.galleryUploader.getItemByFileId(id)).remove();
          } else {
            alertify.error(json.msg.join("</br>"), 5);
          }
        },
        error: function(xhr, status, error) {
          var json = eval("(" + XMLHttpRequest.responseText + ")");
          alertify.error(json.error, 5);

          $(self.galleryUploader.getItemByFileId(id)).find(".qq-upload-spinner-selector").removeClass("qq-hide");
        }
      });
    },
    multipleDelete: function(uuids) {
      var self = this;
      $.ajax({
        url: self.settings.mutiple_delete_url,
        method: "DELETE",
        data: {
          "qqmultipleuuid": uuids
        },
        cache: true,
        beforeSend: function() {
          self.settings.$wrapper.find(".js-checkbox-uploader:checked").each(function() {
            $(self.galleryUploader.getItemByFileId($(this).data('id'))).find(".qq-upload-spinner-selector").removeClass("qq-hide");
          });
        },
        success: function(json) {
          if (json.success) {
            alertify.success(json.msg, 5);
            self.settings.$wrapper.find(".js-checkbox-uploader:checked").each(function() {
              qq(self.galleryUploader.getItemByFileId($(this).data('id'))).remove();
              $(document).trigger("ON_DELETE_UPLOADED_FILE", [$(this).val()]);
            });
          } else {
            alertify.error(json.msg.join("</br>"), 5);
          }
        },
        error: function(xhr, status, error) {
          self.settings.$wrapper.find(".js-checkbox-uploader:checked").each(function() {
            $(self.galleryUploader.getItemByFileId($(this).data('id'))).find(".qq-upload-spinner-selector").addClass("qq-hide");
          });
          var json = eval("(" + XMLHttpRequest.responseText + ")");
          alertify.error(json.error, 5);
        }
      });
    },
    showMessage: function(message) {
      return alertify.alert(this.settings.translations.confirm, message);
    },
    showPrompt: function(message, defaultValue) {
      var promise;
      promise = new qq.Promise();
      alertify.prompt(this.settings.translations.confirm, message, function(result, inStr) {
        return promise.success(inStr);
      }, function() {
        return promise.failure(inStr);
      }).set('labels', {
        ok: this.settings.translations.ok,
        cancel: this.settings.translations.cancel
      });
      return promise;
    },
    showConfirm: function(message) {
      var promise;
      promise = new qq.Promise();
      alertify.confirm(this.settings.translations.confirm, this.settings.translations.deletemsg, function() {
        return promise.success();
      }, function() {
        return promise.failure();
      }).set('labels', {
        ok: this.settings.translations.ok,
        cancel: this.settings.translations.cancel
      });
      return promise;
    },
    onComplete: function(id, name, responseJSON, xhr) {

      if (responseJSON.success) {
        alertify.success(responseJSON.msg, 5);
        var $checkbox = qq(this.getItemByFileId(id)).getByClass('js-checkbox-uploader')[0];
        $checkbox.setAttribute("value", responseJSON.newUuid);
        $checkbox.setAttribute("data-id", id);

        $(document).trigger("ON_UPLOAD_FILE", [id, responseJSON.newUuid]);

      } else {
        alertify.error(responseJSON.error, 5);
      }
    },
    onSessionRequestComplete: function(response, success, xhrOrXdr) {
      if (success) {
        var self = this;
        $(response).each(function(i, responseJSON) {

          var $checkbox = qq(self.getItemByFileId(i)).getByClass('js-checkbox-uploader')[0];
          $checkbox.setAttribute("value", responseJSON.uuid);
          $checkbox.setAttribute("data-id", i);

          var $retouchs_section = qq(self.getItemByFileId(i)).getByClass('js-retouchs-list-group')[0];

          $(responseJSON.retouch).each(function(i, retouch) {
            // create node
            var parentNode = document.createElement("span");
            var node = document.createElement("a");

            var textnode = document.createTextNode(" " + retouch.title.toUpperCase());
            var icon = document.createElement("i");

            icon.setAttribute("class", "fas fa-tag");

            node.appendChild(icon);
            node.appendChild(textnode);
            node.setAttribute("href", "#");

            parentNode.appendChild(node);
            parentNode.setAttribute("class", "tag");
            // add node
            $retouchs_section.appendChild(parentNode);

          });

          $(document).trigger("ON_UPLOAD_FILE", [i, responseJSON.uuid]);

        });
      }
    },
    onDeleteComplete: function(id, xhr, isError) {
      if (!isError) {
        var responseJSON = eval("(" + xhr.responseText + ")");

        alertify.notify(responseJSON.msg, 'success', 5);

        $(document).trigger("ON_DELETE_UPLOADED_FILE", [responseJSON.uuid]);
      } else {
        alertify.notify(responseJSON.msg, 'error', 5);
      }
    }
  });
})(window, jQuery, qq);
