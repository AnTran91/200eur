(function($) {
  var fineUploader = new FineUploader({
    $wrapper: $("#js-upload-section"),
    element: $("#js-fine-uploader-gallery")[0],
    template: 'qq-template-gallery',
    extra_buttons_element: $("#fine-uploader-folders")[0],
    translations: $('#js-uploader-script').data(),
    init_url: $('#js-uploader-script').attr('init-url'),
    upload_url: $('#js-uploader-script').attr('upload-url'),
    chunking_url:$('#js-uploader-script').attr('chunking-url'),
    delete_url: $('#js-uploader-script').attr('delete-url'),
    mutiple_delete_url: $('#js-uploader-script').attr('mutiple-delete-url'),
  });

  var uploader = new Uploader($("#js-upload-section"), {
      new_picture_form_url: $('#js-uploader-script').attr('picture-new-form-url'),
      param_list_url: $('#js-uploader-script').attr('param-list-url'),
      new_param_form_url: $('#js-uploader-script').attr('param-new-form-url'),
      update_param_form_url: $('#js-uploader-script').attr('param-update-form-url'),
  });
})(jQuery);
