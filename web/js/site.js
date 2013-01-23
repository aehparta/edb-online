$(document).ready(function($) {
    $(document).on('mouseenter', '.editable-container', function() {
        $('.editable-tools').fadeIn('fast');
    });

    $(document).on('mouseleave', '.editable-container', function() {
        $('.editable-tools').fadeOut('fast');
    });

    setupAloha('#page-content');

    $('.editable-enabled').aloha();
    onReadyBrowserBundle();
});

function setupAloha(element)
{
    $(element).find('.editable-tools-save').on('click', function() {
        editableDone();

        $('.editable').each(function() {
            var href = $(this).attr('editable-resource');
            var data = {
                content: $(this).html(),
            };
            $.ajax({ type: 'PUT', url: href, data: data }).done(requestDone);
        });
    });

    $(element).find('.editable-tools-stop').on('click', function() {
        editableDone();
    });

    $(element).find('.editable-tools-edit').on('click', function() {
        $('.editable').aloha();
        $('.editable').addClass('editable-enabled');
        $('.editable-tools-mode-start').hide();
        $('.editable-tools-mode-edit').show();
        $('.editable-edit-title').show();
    });
}

function requestDone()
{
}

function editableDone()
{
    $('.editable').mahalo();
    $('.editable').removeClass('editable-enabled');
    $('.editable').removeClass('aloha-editable-highlight');
    $('.editable-tools-mode-start').show();
    $('.editable-tools-mode-edit').hide();
    $('.editable-edit-title').hide();
}

/*
Aloha.ready(function() {
});
*/