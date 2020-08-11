(function(window, jQuery) {
  window.Paginator = function($wrapper) {

    this.$wrapper = $wrapper;

    this.paginationData = this.$wrapper.find('#js-pagination').data();
    this.params = null;

    this.pagination = this.$wrapper
      .find('#js-pagination')
      .bootpag({
        total: this.paginationData.totalPage, // total pages
        page: this.paginationData.page, // default page
        maxVisible: this.paginationData.maxVisible, // visible pagination
        leaps: this.paginationData.leaps // next/prev leaps through maxVisible
      });

    this.pagination.on(
      "page",
      this.onPaging.bind(this)
    );

    this.$wrapper.on(
      'click',
      '#js-clear-paginator',
      this.onClearFilter.bind(this)
    );

    this.$wrapper.on(
      'click',
      'table th a',
      this.onOrdering.bind(this)
    );

    this.$wrapper.on(
      'click',
      '#js-delete-multiple-records',
      this.onDeleteMultiple.bind(this)
    );

    this.$wrapper.on(
      'click',
      '#js-select-all',
      this.onToggleCheckbox.bind(this)
    );

    this.$wrapper.on(
      'submit',
      'form',
      this.onFilterResult.bind(this)
    );

    this.$wrapper.on(
      'click',
      '[data-toggle="card-collapse"]',
      this.onCardCollapse.bind(this)
    );

    /** Initialize dates */
    this.$wrapper.find('.js-datepicker').daterangepicker({
      autoUpdateInput: false,
      locale: {
        cancelLabel: 'Clear'
      }
    });

    if (this.$wrapper.find('.js-select-list').length > 0) {
      this.$wrapper.find('.js-select-list').each(function() {
        $(this).selectize();
      });
    }

    this.$wrapper.find('.js-datepicker').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    /** Initialize tooltips */
    this.$wrapper.find('[data-toggle="tooltip"]').tooltip();

    /** Initialize popovers */
    this.$wrapper.find('[data-toggle="popover"]').popover({
      html: true
    });

  };

  $.extend(window.Paginator.prototype, {
    onCardCollapse: function(e) {
      var $card = $(e.currentTarget).closest('div.card');

      $card.toggleClass('card-collapsed');

      e.preventDefault();
      return false;
    },
    onDeleteMultiple: function(e) {
      e.preventDefault();
      var ids = jQuery.map(this.$wrapper.find('.js-select-ids'), function(checkbox) {
        if ($(checkbox).is(':checked')) {
          return $(checkbox).val();
        }
      });
      var self = this;
      if (ids.length > 0) {
        alertify.confirm($(e.currentTarget).data('title'), $(e.currentTarget).data('msg'),
          function() {
            $.ajax({
              url: $(e.currentTarget).data('href'),
              method: "DELETE",
              data: {
                'data': ids,
                'token': $(e.currentTarget).data('token')
              },
              beforeSend: function() {
                self.$wrapper.find('#js-card-table .card-body').css('opacity', '0.2');
                self.$wrapper.find('#js-card-table .card-body').append('<div class="loader"></div>');
              },
              success: function(json) {
                if (json.success) {
                  alertify.success(json.msg);
                  self.$wrapper.find("#js-clear-paginator").trigger("click");
                  self.$wrapper.find("#js-select-all").prop('checked', false);
                } else {
                  alertify.error(json.msg);
                }
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                self.$wrapper.find('#js-card-table tbody').css('opacity', '1');
                self.$wrapper.find('#js-card-table tbody .loader').remove();
                alertify.error("something went wrong sorry about that. please try again later.", 5);
              }
            });
          },
          function() {
            console.log('Cancel');
          });
      }

    },
    onClearFilter: function(e) {
      e.preventDefault();
      this.onLoadData({
        'page': 1
      }, this);
    },
    onOrdering: function(e) {
      e.preventDefault();
      // clear sortable
      this.$wrapper.find('#js-card-table thead th a').removeClass().addClass('sortable');

      if (typeof $(e.currentTarget).attr('direction') !== typeof undefined && $(e.currentTarget).attr('direction') == 'asc') {
        $(e.currentTarget).removeClass().addClass('desc');
        $(e.currentTarget).attr('direction', 'desc');
      } else {
        $(e.currentTarget).removeClass().addClass('asc');
        $(e.currentTarget).attr('direction', 'asc');
      }

      this.onLoadData($.extend(this.params, {
        'sort': $(e.currentTarget).attr('field'),
        'direction': $(e.currentTarget).attr('direction')
      }), this);
    },
    onPaging: function(event, num) {
      this.onLoadData($.extend(this.params, {
        'page': num
      }), this);
    },
    onFilterResult: function(e) {
      e.preventDefault();
      var $form = $(e.currentTarget).closest('form');
      
      var formSerialize = $form.serializeArray();
      // convert date format 'DD/MM/YYYY' -> 'MM/DD/YYYY' to available for filter
      formSerialize.forEach(function(element, index){
        var inputName = element.name;
        var startDate, endDate;
        if ($('input[name="' + inputName + '"]').hasClass('js-datepicker')) {
          if (element.value != "") {
            if ($('input[name="' + inputName + '"]').data('datepicker') != undefined) {
              formSerialize[index].value = $('input[name="' + inputName + '"]').val();
            }
            else {
              startDate = $('input[name="' + inputName + '"]').data('daterangepicker').startDate.format('MM/DD/YYYY HH:mm:ss');
              endDate = $('input[name="' + inputName + '"]').data('daterangepicker').endDate.format('MM/DD/YYYY HH:mm:ss');
              formSerialize[index].value = startDate + ' - ' + endDate;
            }
          }
        }
      });
      this.onLoadData(formSerialize, this);
    },
    onToggleCheckbox: function(e) {
      if ($(e.currentTarget).is(':checked')) {
        // Iterate each checkbox
        this.$wrapper.find('.js-select-ids').each(function() {
          if (!this.disabled) {
            this.checked = true;
          }
        });
      } else {
        this.$wrapper.find('.js-select-ids').each(function() {
          if (!this.disabled) {
            this.checked = false;
          }
        });
      }
    },
    onLoadData: function(data, self) {
      $.ajax({
        url: self.paginationData.route,
        method: "GET",
        data: data,
        cache: true,
        async: true,
        beforeSend: function() {
          self.$wrapper.find('#js-card-table tbody').css('opacity', '0.2');
          self.$wrapper.find('#js-card-table tbody').append('<div class="loader"></div>');
        },
        success: function(json) {
          self.$wrapper.find('#js-card-table tbody').css('opacity', '1');
          self.$wrapper.find('#js-card-table tbody').empty();
          self.$wrapper.find('#js-card-table tbody').append(json.html);

          self.params = json.params;
          self.pagination.bootpag({
            total: parseInt(json.total_page) <= 0 ? 1 : parseInt(json.total_page),
            page: parseInt(json.page) <= 0 ? 1 : parseInt(json.page)
          });

          /** Initialize tooltips */
          self.$wrapper.find('[data-toggle="tooltip"]').tooltip();

          /** Initialize popovers */
          self.$wrapper.find('[data-toggle="popover"]').popover({
            html: true
          });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          self.$wrapper.find('#js-card-table tbody').css('opacity', '1');
          self.$wrapper.find('#js-card-table tbody .loader').remove();
          alertify.error("something went wrong sorry about that. please try again later.", 5);
        }
      });
    }
  });
})(window, jQuery);
