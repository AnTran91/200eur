(function($) {
    // USE STRICT
    "use strict";

    $(document).ready(function() {
        $(document).find('#js-loader').addClass('hide-content');
        $(document).find('#js-content').removeClass('hide-content');
    });

})(jQuery);

(function($) {
    // USE STRICT
    "use strict";

    $(document).ready(function() {
        fab.init();
    });

    var easing_swiftOut = [.35, 0, .25, 1];
    var fab = {
        init: function() {
            fab.fab_speed_dial(),
                fab.fab_toolbar(),
                fab.fab_sheet()
        },
        fab_speed_dial: function() {
            function e(e) {
                e.closest(".md-fab-wrapper").addClass("md-fab-active");
            }

            function i(e) {
                e.closest(".md-fab-wrapper").removeClass("md-fab-active");
            }
            $(".md-fab-speed-dial,.md-fab-speed-dial-horizontal").each(function() {
                var a, t = $(this),
                    n = t.children(".md-fab").append('<i class="material-icons md-fab-action-close" style="display:none">&#xE5CD;</i>');
                t.is("[data-fab-hover]") ? t.on("mouseenter", function() {
                    t.addClass("md-fab-over"),
                        clearTimeout(a),
                        setTimeout(function() {
                            e(n)
                        }, 100)
                }).on("mouseleave", function() {
                    i(n),
                        a = setTimeout(function() {
                            t.removeClass("md-fab-over")
                        }, 500)
                }) : n.on("click", function() {
                    var a = $(this);
                    a.closest(".md-fab-wrapper").hasClass("md-fab-active") ? i(a) : e(a)
                }).closest(".md-fab-wrapper").find(".md-fab-small").on("click", function() {
                    i(n)
                })
            })
        },
        fab_toolbar: function() {
            var e = $(".md-fab-toolbar");
            e && (e.children("i").on("click", function(i) {
                i.preventDefault();
                var a = e.children(".md-fab-toolbar-actions").children().length;
                e.addClass("md-fab-animated");
                var t = e.hasClass("md-fab-small") ? 24 : 16,
                    n = e.hasClass("md-fab-small") ? 44 : 64;
                setTimeout(function() {
                    e.width(a * n + t)
                }, 140),
                    setTimeout(function() {
                        e.addClass("md-fab-active")
                    }, 420)
            }),
                $(document).on("click scroll", function(i) {
                    e.hasClass("md-fab-active") && ($(i.target).closest(e).length || (e.css("width", "").removeClass("md-fab-active"),
                        setTimeout(function() {
                            e.removeClass("md-fab-animated")
                        }, 140)))
                }))
        },
        fab_sheet: function() {
            var e = $(".md-fab-sheet");
            e && (e.children("i").on("click", function(i) {
                i.preventDefault();
                var a = e.children(".md-fab-sheet-actions").children("a").length;
                e.addClass("md-fab-animated"),
                    setTimeout(function() {
                        e.width("240px").height(40 * a + 8)
                    }, 140),
                    setTimeout(function() {
                        e.addClass("md-fab-active")
                    }, 280)
            }),
                $(document).on("click scroll", function(i) {
                    e.hasClass("md-fab-active") && ($(i.target).closest(e).length || (e.css({
                        height: "",
                        width: ""
                    }).removeClass("md-fab-active"),
                        setTimeout(function() {
                            e.removeClass("md-fab-animated")
                        }, 140)))
                }))
        },
    };
})(jQuery);

(function($) {
    // USE STRICT
    "use strict";

    // Scroll Bar
    try {
        var jscr1 = $('.js-scrollbar1');
        if (jscr1[0]) {
            ps1 = new PerfectScrollbar('.js-scrollbar1', {
                suppressScrollX: true,
                wheelSpeed: 2,
                wheelPropagation: true,
                minScrollbarLength: 20
            });
        }

        var jscr2 = $('.js-scrollbar2');
        if (jscr2[0]) {
            ps2 = new PerfectScrollbar('.js-scrollbar2', {
                suppressScrollX: true,
                wheelSpeed: 2,
                wheelPropagation: true,
                minScrollbarLength: 20
            });

        }

    } catch (error) {
        console.log(error);
    }

})(jQuery);

(function($) {
    // USE STRICT
    "use strict";

    try {
        $('.js-toggle-menu').click(function() {
            $('#js-menu-sidebar-lg').toggleClass("background-none");
            $('#js-sidebar-lg').toggleClass("d-lg-none");
            $('#js-page-container-lg').toggleClass("p-0");
        });
    } catch (e) {
        console.log(e);
    }

    // Dropdown
    try {
        var menu = $('.js-item-menu');
        var sub_menu_is_showed = -1;

        for (var i = 0; i < menu.length; i++) {
            $(menu[i]).on('click', function(e) {
                e.preventDefault();
                $('.js-right-sidebar').removeClass("show-sidebar");
                if (jQuery.inArray(this, menu) == sub_menu_is_showed) {
                    $(this).toggleClass('show-dropdown');
                    sub_menu_is_showed = -1;
                } else {
                    for (var i = 0; i < menu.length; i++) {
                        $(menu[i]).removeClass("show-dropdown");
                    }
                    $(this).toggleClass('show-dropdown');
                    sub_menu_is_showed = jQuery.inArray(this, menu);
                }
            });
        }
        $(".js-item-menu, .js-dropdown").click(function(event) {
            event.stopPropagation();
        });

        $("body,html").on("click", function() {
            for (var i = 0; i < menu.length; i++) {
                menu[i].classList.remove("show-dropdown");
            }
            sub_menu_is_showed = -1;
        });

    } catch (error) {
        console.log(error);
    }

    var wW = $(window).width();
    // Right Sidebar
    var right_sidebar = $('.js-right-sidebar');
    var sidebar_btn = $('.js-sidebar-btn');

    sidebar_btn.on('click', function(e) {
        e.preventDefault();
        for (var i = 0; i < menu.length; i++) {
            menu[i].classList.remove("show-dropdown");
        }
        sub_menu_is_showed = -1;
        right_sidebar.toggleClass("show-sidebar");
    });

    $(".js-right-sidebar, .js-sidebar-btn").click(function(event) {
        event.stopPropagation();
    });

    $("body,html").on("click", function() {
        right_sidebar.removeClass("show-sidebar");

    });


    // Sublist Sidebar
    try {
        var arrow = $('.js-arrow');
        arrow.each(function() {
            var that = $(this);
            that.on('click', function(e) {
                e.preventDefault();
                that.find(".arrow").toggleClass("up");
                that.toggleClass("open");
                that.parent().find('.js-sub-list').slideToggle("250");
            });
        });

    } catch (error) {
        console.log(error);
    }


    try {
        // Hamburger Menu
        $('.hamburger').on('click', function() {
            $(this).toggleClass('is-active');
            $('.navbar-mobile').slideToggle('500');
        });
        $('.navbar-mobile__list li.has-dropdown > a').on('click', function() {
            var dropdown = $(this).siblings('ul.navbar-mobile__dropdown');
            $(this).toggleClass('active');
            $(dropdown).slideToggle('500');
            return false;
        });
    } catch (error) {
        console.log(error);
    }
})(jQuery);

$(document).ready(function() {
    /** Constant div card */
    var DIV_CARD = 'div.card';

    /** Initialize tooltips */
    $("#js-content").find('[data-toggle="tooltip"]').tooltip();

    /** Initialize popovers */
    $("#js-content").find('[data-toggle="popover"]').popover({
        html: true
    });

    /** Function for remove card */
    $("#js-content").on('click', '[data-toggle="card-remove"]', function(e) {
        var $card = $(this).closest(DIV_CARD);

        $card.remove();

        e.preventDefault();
        return false;
    });

    /** Function for collapse card */
    $("#js-content").on('click', '[data-toggle="card-collapse"]', function(e) {
        var $card = $(this).closest(DIV_CARD);

        $card.toggleClass('card-collapsed');

        e.preventDefault();
        return false;
    });

    /** Function for fullscreen card */
    $("#js-content").on('click', '[data-toggle="card-fullscreen"]', function(e) {
        var $card = $(this).closest(DIV_CARD);

        $card.toggleClass('card-fullscreen').removeClass('card-collapsed');

        e.preventDefault();
        return false;
    });
});