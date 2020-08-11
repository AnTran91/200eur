var $days = [];
$.ajax({
  url: Routing.generate('holidays_list'),
  method: "GET",
  async: false,
  cache: false,
  beforeSend: function(jqXHR, settings ) {
    alertify.message('<div class="partial-loader"></div>', 0);
  },
  success: function(json) {
     alertify.dismissAll();
    var $daysRow = [];
    $(json).each(function(i, rowData) {
      $days = $days.concat(rowData.Days);
    });
  },
  error: function(xhr, status, error) {
    alertify.dismissAll();
    var json = eval("(" + xhr.responseText + ")");
    alertify.notify(error, 'error');
  }
});
$('#datepicker').datepicker({
  language: 'fr',
  daysOfWeekHighlighted: [0,6],
  datesDisabled: $days
});
