$(function () {
    function nl2br(str, is_xhtml) {
      var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display
      
      return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }
    
    var i;

    // Foundation init
    $(document)
        .foundation('tooltips', {
            tipTemplate : function (selector, content) {
                return '<span data-selector="' + selector + '" class="'
                  + Foundation.libs.tooltips.settings.tooltipClass.substring(1)
                  + '">' + nl2br(content) + '<span class="nub"></span></span>';
            }
        })
        .foundation()
    ;
    
    // Chosen
    $('.js-chosen').chosen({ allow_single_deselect: true, width: '100%' });

    // Markedit
    $('.js-markdown').markedit({
        'preview' : 'toolbar',
        'toolbar': {
            'layout': 'bold italic link numberlist bulletlist quote',
            'buttons' : [
                {
                    'id': 'linkJS',
                    'css': 'link',
                    'tip': 'Insert Hyperlink',
                    'click': function () {
                        var state = $(this).markeditGetState()
                          , url
                          ;

                        // Make sure selection is clean and get selected Url
                        // This also adds state.selectedUrl and state.selectedUrlIndex to the state
                        state = MarkEdit.cleanSelection(state, MarkEdit.RegexLinkStart, MarkEdit.RegexLinkEnd);

                        // Ask for URL
                        url = prompt("URL", "http://");

                        if (url != null && url != "" && url != "http://") {
                            // IE will loose the selection state unless we re-apply it
                            $(this).markeditSetState(state);
                            $(this).markeditSetLinkOrImage(false, url);
                        }
                    }
                }
            ],
        }
    });

    $('.preview button').click(function () {
        if (!$(this).hasClass('preview')) {
            // Kinky fix
            $(this).parents('.markedit').find('button').removeAttr('disabled');
        }
    });

    // ------
    // TOGGLE
    // ------
    $('[data-toggle]').click(function (e) {
        var el              = $(this)
          , entity          = el.closest('[data-entity]')
          , icon            = el.find('i')
          , initialClass    = icon.attr('class')
          , loadingClass    = 'icon-refresh icon-spin'
          , togglingClass   = 'js-admin-toggling'
          ;

        e.preventDefault();

        if ($('html').hasClass(togglingClass)) {
            return;
        }

        $.ajax({
            'url': entity.data('toggle-url'),
            'type': 'POST',
            'data': {
                'id'        : entity.data('entity-id'),
                'namespace' : entity.data('entity-namespace'),
                'field'     : el.data('toggle-field')
            },
            'beforeSend': function () {
                // Block
                icon.removeClass();
                icon.addClass(loadingClass);
                $('html').addClass(togglingClass);
            },
            'success': function (data) {
                var group = el.data('toggle-group') || '';

                if (group !== '') {
                    icon = $('[data-toggle-group="'+ el.data('toggle-group') +'"]').find('i');
                }

                icon.removeClass();

                if (data.value) {
                    icon.addClass(el.data('toggle-on'));
                } else {
                    icon.addClass(el.data('toggle-off'));
                }
            },
            'complete': function (jqXHR, status) {
                // Unblock
                if (status != 'success') {
                    icon.removeClass();
                    icon.addClass(initialClass);
                }

                $('html').removeClass(togglingClass);
            }
        });
    });

    // --------
    // SORTABLE
    // --------
    var sortables = {
        'single'    : [],
        'connected' : {}
      }
      ;

    $('[data-sortable]').each(function (i, el) {
        var connectWith     = $(el).data('sortable-connect-with') || ''
          , hasConnection   = !!connectWith.length
          ;

        if (hasConnection) {
            if (!(connectWith in sortables.connected)) {
                sortables.connected[connectWith] = [];
            }

            sortables.connected[connectWith].push(el);
        } else {
            sortables.single.push(el);
        }
    });

    function sortableUpdate (e, ui) {
        // Run Once, trick for connected lists
        // http://stackoverflow.com/questions/3492828/jquery-sortable-connectwith-calls-the-update-method-twice
        if (this === ui.item.parent()[0]) {
            var prev            = ui.item.prev('[data-entity]')
              , prevId          = null
              , icon            = ui.item.find('[data-sortable-handle]').first()
              , initialClass    = icon.attr('class')
              , loadingClass    = 'icon-refresh icon-spin'
              , sortingClass    = 'js-admin-sorting'
              ;

            // Are we still sorting another element?
            if ($('html').hasClass(sortingClass)) {
                return;
            }

            // If there is a previous entity, use it for sorting reference
            if (!!prev.length) {
                prevId = prev.data('entity-id');
            }

            $.ajax({
                'url': ui.item.find('[data-sortable-handle]').data('sortable-url'),
                'type': 'POST',
                'data': {
                    'namespace' : ui.item.data('entity-namespace'),
                    'id'        : ui.item.data('entity-id'),
                    'prevId'    : prevId
                },
                'beforeSend': function () {
                    // Block
                    icon.removeClass();
                    icon.addClass(loadingClass);
                    $('html').addClass(sortingClass);
                },
                'error': function () {
                    // Move back the element to it's original position
                    if (!!ui.sender) {
                        ui.sender.sortable('cancel');
                    } else {
                        ui.item.closest('[data-sortable]').sortable('cancel');
                    }
                },
                'complete': function (jqXHR, status) {
                    // Unblock
                    icon.removeClass();
                    icon.addClass(initialClass);
                    $('html').removeClass(sortingClass);

                    if (status == "success") {
                        // Run onUpdate callback
                        if (!!ui.item.closest('[data-sortable]').data('sortable-on-update-cb')) {
                            var func = new Function(ui.item.closest('[data-sortable]').data('sortable-on-update-cb') + '.apply(undefined, arguments);')
                            func(ui.item);
                        }
                    }
                }
            });
        }
    }

    // Singles
    for (i = 0; i < sortables.single.length; i++) {
        $(sortables.single[i]).sortable({
            handle  : '[data-sortable-handle]',
            items   : '> [data-entity]',
            update  : sortableUpdate
        }).disableSelection();
    }

    // Connected
    for (group in sortables.connected) {
        $(group).sortable({
            handle  : '[data-sortable-handle]',
            items   : '> [data-entity]',
            connectWith: group,
            update: sortableUpdate
        }).disableSelection();
    }
    
    // -----
    // ADMIN
    // -----
    $('.js-check-all').click(function () {
        var mainCheck = $(this).children('i');
            initialClass = mainCheck.attr('class');
        
        $('.js-action-main, .js-action-check').toggle();
        
        mainCheck.toggleClass('icon-check-empty icon-check');
        $('.js-check').removeClass(initialClass).addClass(mainCheck.attr('class'));
    });
    
    $('.js-check').click(function () {
        $(this).toggleClass('icon-check-empty icon-check');
    });
    
    // -----------
    // LIST SWITCH
    // -----------
    var tableViewButtons = $('[data-table-view-actions]').find('a')
      ;

    $('[data-table-view-list]').click(function () {
        tableViewButtons.removeClass('pressed');
        $(this).addClass('pressed');

        if (!$('.js-table').hasClass('small-block-grid-1')) {
            $('.js-table').toggleClass('small-block-grid-1 large-block-grid-3');
        }
    });
    
    $('[data-table-view-block-grid]').click(function () {
        tableViewButtons.removeClass('pressed');
        $(this).addClass('pressed');

        if (!$('.js-table').hasClass('large-block-grid-3')) {
            $('.js-table').toggleClass('small-block-grid-1 large-block-grid-3');
        }
    });
});
