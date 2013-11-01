$(document).ready(function($) {
//     $(document).on('mouseenter', '.editable-container', function() {
//         $('.editable-tools').fadeIn('fast');
//     });
//
//     $(document).on('mouseleave', '.editable-container', function() {
//         $('.editable-tools').fadeOut('fast');
//     });

    $('body').on('click', '.site-form-open', function(e) {
        e.preventDefault();
        var url = $(this).attr('site-form-url');
        $.siteform({ href: url });
        return false;
    });

    edb_OnReadyBrowserBundle();
});
