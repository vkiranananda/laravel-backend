(function ($) {
    'use strict';
        



    console.log($.trumbowyg);

       $.trumbowyg.imgDblClickHandler = function (trumbowyg)  {
            var t = this;
return
            return function () {
                var $img = $(this),
                    src = $img.attr('src'),
                    base64 = '(Base64)';

                if (src.indexOf('data:image') === 0) {
                    src = base64;
                }

                var options = {
                    url: {
                        label: 'URL2',
                        value: src,
                        required: true
                    },
                    alt: {
                        label: t.lang.description,
                        value: $img.attr('alt')
                    }
                };

                if (t.o.imageWidthModalEdit) {
                    options.width = {
                        value: $img.attr('width') ? $img.attr('width') : ''
                    };
                }

                t.openModalInsert(t.lang.insertImage, options, function (v) {
                    if (v.url !== base64) {
                        $img.attr({
                            src: v.url
                        });
                    }
                    $img.attr({
                        alt: v.alt
                    });

                    if (t.o.imageWidthModalEdit) {
                        if (parseInt(v.width) > 0) {
                            $img.attr({
                                width: v.width
                            });
                        } else {
                            $img.removeAttr('width');
                        }
                    }

                    return true;
                });
                return false;
            };
        }



    // Plugin default options
    var defaultOptions = {
        url: {
            label: 'URL',
            required: true
        },
        alt: { label: 'Alt' },
        title: { label: 'Title' },
        width: { label: 'Width' }
    };

    $.extend(true, $.trumbowyg, {
        // Register plugin in Trumbowyg
        plugins: {
            insertImage: {
                // Code called by Trumbowyg core to register the plugin
                init: function (trumbowyg) {
                    var t = trumbowyg;

                    t.saveRange();
                    var btnDef = {
                        fn: function () {
                            t.openModalInsert(t.lang.insertImage, defaultOptions, function (v) { // v are values
                                t.execCmd('insertImage', v.url, false, true);
                                var $img = $('img[src="' + v.url + '"]:not([alt])', t.$box);
                                $img.attr('alt', v.alt);
                                $img.attr('title', v.title );
                                $img.attr('width', v.width );

                                t.syncCode();
                                t.$c.trigger('tbwchange');

                                return true;
                            });
                        }
                    }
                    
                    function initResizable() {
                            trumbowyg.$ed.find('img:not(.resizable)').unbind('dblclick').on('dblclick', function(){
                            console.log('123')
                        });
                    }

                    // trumbowyg.$c.on('tbwinit', initResizable);

                    // trumbowyg.addBtnDef('insertImage', btnDef);
                },
            },
        },

 



    })
})(jQuery);
