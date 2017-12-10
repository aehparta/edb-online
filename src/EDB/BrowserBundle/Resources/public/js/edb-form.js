/**
 * EDB article editor
 */
(function($) {
    var func = false;

    var showError = false;

    var defaults = {
        root: false,
        onOk: false,
        onClose: false,
        onSend: false,
        onSendComplete: false,
        onVisible: false,
        onHide: false,
        onLoad: false,
        onAltSelect: false,
    };

    var methods = {
        init: function(options) {
            var cfg = this.data('edb_editor');
            if (cfg)
                return;
            cfg = $.extend({}, defaults, options);

            /* bind data to object */
            this.data('edb_editor', cfg);

            /* setup activator */
            this.on('click', onShow);

            /* some callbacks */
            this.showError = displayError;
        },
        show: function() {
            onShow(this);
        },
        hide: function() {
            onHide(this);
        },
    };

    $.fn.edb_editor = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method '+method+' does not exist on jQuery.edb_editor');
        }
    };

    function onShow(e) {
        var cfg = $(this).data('edb_editor');
        var form = $(this);
        var show = true;

        if (e.altKey) {
            form.func = cfg.onAltSelect;
            if (form.func)
                return form.func();
            return false;
        }

        form.func = cfg.onLoad;
        if (form.func)
            show = form.func();

        if (show !== false) {
            var url = $(this).attr('form-url');
            $.ajax({ type: 'GET', url: url, context: this, }).done(onShowLoaded);
        }
    };

    function onShowLoaded(data) {
        var form = $(data);
        var cfg = $(this).data('edb_editor');
        form.data('edb_editor', cfg);

        cfg.root.parent().append(form);

        /* setup buttons */
        form.find('.edb-editor-button').on('click', form, onButtonClick);

        form.find('.edb-editor-message').hide();

        if (cfg.root) {
            //form.width(cfg.root.width());
            cfg.root.fadeOut('fast', function() {
                form.fadeIn('fast');
            });
        } else {
            form.fadeIn('fast');
        }

        /* form is now visible */
        form.func = cfg.onVisible;
        if (form.func)
            form.func();

        return false;
    };

    function onHide(e) {
//         var form = false;
//         if ($(e).data('edb_editor')) {
//             form = e;
//         } else {
//             e.preventDefault();
//             form = e.data;
//         }
//         var cfg = form.data('edb_editor');
//
//         form.func = cfg.onHide;
//         if (form.func)
//             form.func();
//
//         if (cfg.root) {
//             form.fadeOut('fast', function() {
//                 cfg.root.fadeIn('fast');
//             });
//         } else {
//             form.fadeOut('fast');
//         }
        return false;
    };

    function onButtonClick(e) {
        e.preventDefault();
        var form = e.data;
        var cfg = form.data('edb_editor');
        form.func = false;

        if ($(this).hasClass('edb-editor-close')) {
            form.func = cfg.onClose;
        } else if ($(this).hasClass('edb-editor-ok')) {
            form.func = cfg.onOk;
        } else if ($(this).hasClass('edb-editor-send')) {
            form.func = cfg.onSend;
            var ret = true;
            if (form.func)
                ret = form.func();
            if (ret !== false)
                return onSendForm(e);
            if (ret !== false)
                return onHide(e);
            return false;
        } else {
            return false;
        }

        if (form.func) {
            if (form.func() !== false)
                return onHide(e);
        } else {
            return onHide(e);
        }

        return false;
    };

    function onSendForm(e) {
        e.preventDefault();
        var form = e.data;
        var cfg = form.data('edb_editor');

        console.log("moi");
        
        var method = form.attr('edb-editor-method');
        if (!method)
            method = 'get';
        method = method.toUpperCase();

        var url = form.attr('edb-editor-url');
        if (!url) {
            form.showError = displayError;
            return form.showError(form, 'edb-editor-url not defined!');
        }

        /* parse data fields */
        var data = {};
        form.find('[name]').each(function() {
            var field = $(this).attr('name');
            var value = false;

            if ($(this).is('input')) {
                if ($(this).attr('type') == 'checkbox') {
                    value = $(this).attr('checked') == 'checked' ? true : false;
                } else {
                    value = $(this).val();
                }
            } else if ($(this).is('div') || $(this).is('p')) {
                value = $(this).html();
            } else if ($(this).is('h1')) {
                value = $(this).html();
            } else if ($(this).is('h2')) {
                value = $(this).html();
            } else if ($(this).is('img')) {
                value = $(this).attr('src');
            } else if ($(this).is('a')) {
                value = $(this).attr('href');
            }
            
            console.log(field+": "+value);

            if (value)
                data[field] = value;
        });

        $.ajax({ type: method, url: url, data: data, context: form, }).done(onDoneSend);

        return false;
    };

    function displayError(message) {
        $(this).find('.edb-editor-error').show().html(message);
        return false;
    };

    function onDoneSend(data) {
        if (data.success === true) {
            var hide = true;
            var cfg = this.data('edb_editor');
            if (cfg.onSendComplete) {
                this.func = cfg.onSendComplete;
                hide = this.func(data);
            }
            if (hide !== false)
                onHide(this);
        } else {
            var message = 'Something failed';
            if (data.message)
                message = data.message;
            this.showError = displayError;
            this.showError(message);
        }
    };

})(jQuery);
