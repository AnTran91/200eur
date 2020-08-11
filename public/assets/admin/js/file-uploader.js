(function(window, $) {

  window.ChunkedFileUploader = function(options) {

    this.settings = $.extend({
      $wrapper: null,
      // These are the defaults.
      slice_size: "",
      file_uuid: "ec05c3f8-7ee4-493f-97ae-045b08cd980c",
      chunking_url: "/uploader/chunking",
      method: "POST"
    }, options);


    // Initialize our vars
    this.reader = {};
    this.image = {};
    this.index = {};
    this.uploadUuid = this.settings.file_uuid;
    this.$wrapper = this.settings.$wrapper;
    this.slice_size = 1000 * 1024;
    this.chunking_url = this.settings.chunking_url;
    this.method = this.settings.method;

    $(document).on(
      'ON_UPLOAD_FILE', {},
      this.fileUploader.bind(this)
    );

  };

  $.extend(window.ChunkedFileUploader.prototype, {
    fileUploader: function(event, file) {
      event.preventDefault();
      var reader = new FileReader();
      var image = file;
      var index = 0;
      var totalFailures = 0;
      var self = this;

      var canDismiss = false;
      var notification = alertify.success('<i class="fas fa-spinner fa-spin p-1 float-right"></i>' + '0%');
      notification.ondismiss = function() {
        return canDismiss;
      };

      fileHandler(index);

      function fileHandler(start) {

        var next_slice = start + self.slice_size + 1;
        var file = image.asBlob();
        var blob = file.slice(start, next_slice);
        var totalChunks = Math.ceil(file.size / self.slice_size);

        reader.onloadend = function(event) {
          if (event.target.readyState !== FileReader.DONE) {
            return;
          }
          $.ajax({
            url: self.chunking_url,
            type: self.method,
            cache: false,
            async: true,
            data: {
              metadata: JSON.stringify({
                'fileName': image.suggestedFileName(),
                'chunkIndex': index,
                'uploadUid': self.uploadUuid,
                'totalChunks': totalChunks,
                'totalSize': blob.size
              }),
              base64: event.target.result,
            },
            error: function(jqXHR, textStatus, errorThrown) {
              if ((totalFailures++) <= 3) {
                reader.readAsDataURL(blob);
              } else { // if file upload is failed 4th time
                notification.dismiss();
              }
            },
            success: function(data) {
              index += 1;
              var size_done = start + self.slice_size;
              var percent_done = Math.floor((size_done / file.size) * 100);

              if (next_slice < file.size) {
                // Update upload progress
                notification.setContent('<i class="fas fa-spinner fa-spin p-1 float-right"></i>' + percent_done + '%');

                // More to upload, call function recursively
                fileHandler(next_slice);
              } else {
                // Update upload progress
                notification.setContent('<i class="fas fa-check-circle p-1 float-right"></i> 100%');
                canDismiss = true;
              }
            }
          });
        };
        reader.readAsDataURL(blob);
      }
    }
  });
})(window, jQuery);
