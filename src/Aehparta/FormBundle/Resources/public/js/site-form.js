/**
 * Popup/form handler jQuery plugin. Like fancyboxes etc, just my way.
 * Also handles automatic data parsing and sending.
 */
(function($) {
    var busy = false;
    var options = {};

    $.fn.siteform = function(options) {
    };

    $.fn.siteform.defaults = {
        hideOnOverlayClick: false,
        overlayColor: '#222',
        onComplete: _siteform_onComplete,
        formAction: false,
    };

    $.siteform = function(opts) {
        options = $.extend($.fn.fancybox.defaults, $.fn.siteform.defaults, opts);

        if (typeof(options.href) == 'string') {
            $.fancybox(options);
        }
    };

    function _siteform_onComplete() {
        $('.site-form-save').click(_siteform_onSave);
        $('.site-form-delete').click(_siteform_onDelete);
        $('.site-form-cancel').click(_siteform_onCancel);

        if (options.formAction === false) {
            var url = $('.site-form').attr('site-form-action');
            if (url !== undefined) {
                options.formAction = url;
            }
        }
    };

    function _siteform_onSave() {
        var form = $(this).closest('.site-form');

        /* find and parse data fields */
        var data = {};
        var error = false;
        form.find('[name]').each(function() {
            var field = $(this).attr('name');
            var value = '';
            var required = $(this).hasClass('site-form-required');

            if ($(this).is('input')) {
                value = $(this).val();
            } else if ($(this).is('div') || $(this).is('p') || $(this).is('h1')) {
                value = $(this).html();
            } else if ($(this).is('img')) {
                value = $(this).attr('src');
            } else if ($(this).is('a')) {
                value = $(this).attr('href');
            }

            if (required && value.length < 1) {
                _siteform_displayError(form, 'Missing mandatory field');
                error = true;
            }

            data[field] = value;
        });

        if (error) {
            return;
        }

        if (options.formAction) {
            $.ajax({ type: 'PUT', url: options.formAction, data: data, context: form, }).done(_siteform_onDoneSend);
        }

        if (options.onFormSave !== undefined) {
            options.onFormSave();
        }
    };

    function _siteform_onDelete() {
        var form = $(this).closest('.site-form');

        if (options.formAction) {
            $.ajax({ type: 'DELETE', url: options.formAction, context: form, }).done(_siteform_onDoneSend);
        }

        if (options.onFormDelete !== undefined) {
            options.onFormDelete();
        }
    };

    function _siteform_onCancel() {
        $.fancybox.close();

        if (options.onFormCancel !== undefined) {
            options.onFormCancel();
        }
    };

    function _siteform_displayError(form, message) {
        form.find('.site-form-error').show().html(message);
        return false;
    };

    function _siteform_onDoneSend(data) {
        if (data.success === true) {
            var relem = $(this).attr('site-form-reload-element');
            if (relem !== undefined) {
                var url = $(relem).attr('site-form-reload-url');
                if (url !== undefined) {
                    $('.site-form-loading').show();
                    $('.site-form-hide-when-loading').hide();
                    $.get(url, function(data) {
                        $(relem).replaceWith(data);
                        $('.site-form-loading').hide();
                        $('.site-form-hide-when-loading').show();
                    });
                }
            }
            $.fancybox.close();
        } else {
            var message = 'Something failed, data returned:<br />'+data;
            if (data.message)
                message = data.message;
            _siteform_displayError(this, message);
        }
    };

})(jQuery);
