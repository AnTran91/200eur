(function($) {
  /* MESSAGE BOX */
  $(document).on("click", '.mb-control', function() {
    var box = $($(this).data("box"));
    if (box.length > 0) {
      box.toggleClass("open");
    }
    return false;
  });
  $(document).on("click", '.mb-logout', function() {
    var box = $($(this).data("box"));
    if (box.length > 0) {
      box.toggleClass("open");
      $('.dropdown-toggle').dropdown('toggle');
    }
    return false;
  });
  $(document).on("click", ".js-mb-control-close", function() {
    $(this).parents(".message-box").removeClass("open");
    return false;
  });
  $(document).on("click", ".js-trigger-alert", function(e) {
    e.preventDefault();
    alertify.alert($(this).data('title'), $(this).data('msg')).set({transition: 'fade'});
  });
})(jQuery);
