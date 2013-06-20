if (!String.prototype.trim) {
    String.prototype.trim = function () { return this.replace(/^\s+|\s+$/g, ''); };
    String.prototype.ltrim = function () { return this.replace(/^\s+/, ''); };
    String.prototype.rtrim = function () { return this.replace(/\s+$/, ''); };
    String.prototype.fulltrim = function () { return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' '); };
}

var illarra = illarra || {};

illarra.parser = {
    tags: {},
    stack: {},
    debug: false,
    openTag: '((',
    closeTag: '))',
    log: function (txt) {
        if (this.debug) {
            console.log(txt);
        }
    },
    registerTag: function (tag, callback) {
        this.tags[tag]  = callback;
        this.stack[tag] = [];
    },
    parseElement: function (i, el, that) {
        // COMMENT NODE
        if (el.nodeType == 8) {
            var tokens = el.nodeValue.trim().split(' ')
              , id
              , data
              , lastBlock
              ;

            // Check OPEN tag may exist
            if (typeof tokens[0] == "undefined") {
                return;
            } 

            // Check if it's an OPEN tag
            if (tokens[0] == that.openTag) {
                // Check ID tag may exist
                if (typeof tokens[1] == "undefined") {
                    return;
                } 

                // Loop trough registered tags
                for (tag in that.tags) {
                    // Check if the tag fits on the token, jump to next tag if it doesn't
                    if (tokens[1].length <= (tag.length + 1)) {
                        continue;
                    }

                    // If token starts with "tag:" add tag to stack
                    if (tokens[1].substring(0, tag.length + 1) == (tag + ':')) {
                        id = tokens[1].substring(tag.length + 1, tokens[1].length);

                        if (typeof tokens[2] != "undefined") {
                            data = eval('(' + tokens[2] + ')');
                        } else {
                            data = {};
                        }

                        that.stack[tag].push({
                            'id': id,
                            'data': data,
                            'content': [],
                            'bbox': {
                                'top'   :  999999999,
                                'bottom': -999999999,
                                'left'  :  999999999,
                                'right' : -999999999
                            }
                        });

                        that.log(that.openTag + ' '+ tag +':' + id);
                    }
                }

                return;
            }

            // Check CLOSE tag may exist
            if (typeof tokens[1] == "undefined") {
                return;
            }

            // Check if it's a CLOSE tag
            if (tokens[1] == that.closeTag) {
                // Loop trough registered tags
                for (tag in that.tags) {
                    // Check if the tag fits on the token, jump to next tag if it doesn't
                    if (tokens[0].length <= (tag.length + 1)) {
                        continue;
                    }

                    // If token start with "tag:" close and run tag callback
                    if (tokens[0].substring(0, tag.length + 1) == (tag + ':')) {
                        id = tokens[0].substring(tag.length + 1, tokens[0].length);

                        // Check if stack is empty
                        // Check if last opened group is the same
                        if (that.stack[tag].length == 0 || that.stack[tag][that.stack[tag].length - 1].id != id) {
                            throw '"' + el.nodeValue.trim() + '" closing without opening?';
                        }

                        // Get last stack element
                        lastBlock = that.stack[tag].pop();

                        // Run the tag callback
                        that.tags[tag](lastBlock.id, lastBlock.data, lastBlock.content.join(' '), lastBlock.bbox);
                        that.log(tag +':' + id + ' ' + that.closeTag);
                    }
                }

                return;
            }
        } else {
            for (tag in that.tags) {
                if (that.stack[tag].length != 0) {
                    lastBlock = that.stack[tag][that.stack[tag].length - 1];

                    switch (el.nodeType) {
                        // ELEMENT_NODE
                        case 1:
                            $(el).contents().each(function (i, el) {
                                that.parseElement(i, el, that);
                            });
                        break;
                        
                        // TEXT_NODE
                        case 3:
                            // Bounding box trick
                            var span = document.createElement('span');
                            el.parentNode.insertBefore(span, el);
                            span.appendChild(el);
                            var rect = span.getBoundingClientRect();

                            // If element has contents save bounding box + contents
                            if (el.nodeValue.trim() != '') {
                                if (rect.top < lastBlock.bbox.top) {
                                    lastBlock.bbox.top = rect.top;
                                }

                                if (rect.bottom > lastBlock.bbox.bottom) {
                                    lastBlock.bbox.bottom = rect.bottom;
                                }

                                if (rect.left < lastBlock.bbox.left) {
                                    lastBlock.bbox.left = rect.left;
                                }

                                if (rect.right > lastBlock.bbox.right) {
                                    lastBlock.bbox.right = rect.right;
                                }

                                // Save contents
                                lastBlock.content.push(el.nodeValue.trim());
                            }
                        break;

                        default:
                        break;
                    }
                }
            }
        }
    },
    // Parse DOM
    parse: function () {
        var that = this;

        $("*").contents().each(function (i, el) {
            that.parseElement(i, el, that);
        });
    }
}

$(function () {
    var editUrl = $('#illarra-admin-content-edit-url').val()
      , newUrl = $('#illarra-admin-content-new-url').val()
      ;

    illarra.parser.registerTag('content', function (id, data, content, bbox) {
        var grow = 6
          , url
          ;

        if (data.id == null) {
            url = newUrl + '?tag='+ id;
        } else {
            url = editUrl.replace(/12345678901337/, data.id);
        }

        $('.illarra-admin-toolbar').
        append(
            '<div ' +
            'data-tag="' + id + '" ' +
            'data-top="' + (bbox.top - grow) + '" ' +
            'data-left="' + (bbox.left - grow) + '" ' +
            'data-width="' + (bbox.right - bbox.left + (2 * grow)) + '" ' +
            'data-height="' + (bbox.bottom - bbox.top + (2 * grow)) + '" ' +
            'data-has-content="' + (!!content.length ? 1 : 0) + '" ' +
            'style="margin:5px;background-color:#eee; padding: 5px">'+
                '<a href="'+ url +'" target="_blank"><strong>' + id + '</strong><br>' + 
                content.substring(0, 50) + '</a>' +
            '</div>'
        );
    });

    illarra.parser.parse();

    var flyer = $('.illarra-admin-toolbar-flyer').hide();

    $('body').delegate('.illarra-admin-toolbar div', 'mouseenter', function (e) {
        var el = $(this);

        // Don't do anything if element doesn't have any content
        if (!el.data('has-content')) {
            return;
        }

        flyer.css({
            'top'   : el.data('top'),
            'left'  : el.data('left'),
            'width' : el.data('width'),
            'height': el.data('height'),
        }).show();

        $('html, body').stop().animate({ scrollTop: el.data('top') - 20 }, 100);
    }).delegate('.illarra-admin-toolbar div', 'mouseleave', function (e) {
        flyer.hide();
    });
});