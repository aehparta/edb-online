
function onReadyBrowserBundle() {
    $(document).on('mouseenter', '.edb-article-attachment', function() {
        $(this).find('.edb-article-attachment-tools').show();
    });

    $(document).on('mouseleave', '.edb-article-attachment', function() {
        $(this).find('.edb-article-attachment-tools').hide();
    });

    window.History.Adapter.bind(window,'statechange',function(){
        var state = window.History.getState();
        if (state.data.articleId !== undefined && $('#edb-form-article').length < 1) {
            $('[edb-article-id="'+state.data.articleId+'"]').click();
        } else if (state.data.articleId === undefined && $('#edb-form-article').length > 0) {
            $('.site-form-close').click();
        }
    });

    var url = $('#edb-browser').attr('edb-controller');
    reloadBrowser(url);
};

function reloadBrowser(url) {
    $('#edb-browser-path li').hide();
    $('#edb-browser-path .edb-path-root').show();
    $('.edb-articles').fadeOut('fast').promise().done(function() {
        $.get(url, function(data) {
            $('#edb-browser').replaceWith(data);
            bindArticlesOnBrowser();
        });
    });
}

function doSearchArticle() {
    var query = $('#edb-browser-search-query').val();
    if (query.length < 1)
        return;

    $('.edb-articles').fadeOut('fast').promise().done(function() {
        var url = $('#edb-browser-search-query').attr('edb-search-url');
        var data = {
            query: query,
        };
        $.ajax({ type: 'GET', data: data, url: url }).done(function (data) {
            $('.edb-articles').replaceWith(data);
            $('.edb-article').siteform({
                root: $('#page-content'),
                onLoad: onLoadArticle,
                onVisible: onVisible,
                onSendComplete: onSendCompleteArticle,
                onHide: onHideArticle,
            });
            $('.edb-articles').fadeIn('fast');
            console.log('loaded');
        });
    });
}

function onLoadArticle() {
    $('#edb-browser').hide();
}

function onVisible() {
    var id = $(this).attr('edb-article-id');
    if (id == 0 || id === undefined) {
        enableEditArticle();
    } else {
        setupAlohaArticle(this);
        disableEditArticle();
    }

    $('.edb-article-attachment-delete').click(function() {
        var attachment = $(this).parents('.edb-article-attachment');

        if (!attachment)
            return;

        var url = $(attachment).attr('edb-attachment-mod-url');
        $.ajax({ type: 'DELETE', url: url, context: this, }).done(function (data) {
            $(attachment).remove();
        });
    });
}

function onSendComplete(data) {
    var url = $('#edb-browser').attr('edb-controller');
    reloadBrowser(url);
}

function onSendCompleteArticle(data) {
    disableEditArticle();
    return false;
}

function onHideArticle() {
    $('#edb-form-category').remove();
    $('#edb-form-article').remove();
    var url = $('#edb-browser').attr('edb-browser-url');
    window.History.back();
    window.History.replaceState(null, null, url);
    $('#edb-browser').show();
}

function setupAlohaArticle(element)
{
    $(element).find('.editable-tools-stop').on('click', disableEditArticle);

    $(element).find('.editable-tools-delete').on('click', function() {
        var url = $(element).attr('site-form-url');
        $.ajax({ type: 'DELETE', url: url, }).done(function (data) {
            $(element).find('.site-form-close').click();
            onSendComplete(data);
        });
    });

    $(element).find('.editable-tools-edit').on('click', enableEditArticle);

    var uploadUrl = $('#edb-article-attachments').attr('file-post-url');
    var articleId = $(element).attr('edb-article-id');
    $(element).find('#edb-article-attachment-upload').uploadify({
        height: 20,
        width: 120,
        swf: '/js/uploadify/uploadify.swf',
        uploader: uploadUrl,
        buttonText: 'Upload attachment',
        removeCompleted: true,
        onUploadSuccess: onUploadSuccess,
        onUploadError: function(file, errorCode, errorMsg, errorString) { console.log(file+': '+errorCode+' / '+errorMsg+' / '+errorString); },
        formData: { 'article_id': articleId, },
    });
    $(element).find('#edb-article-attachment-upload').addClass('site-form-button').disableSelection();

    var url = $(element).attr('edb-browser-url');
    window.History.pushState({ articleId: articleId }, null, url);
}

function onUploadSuccess(file, data, response)
{
//    console.log(data);
    data = JSON.parse(data);
    $('#edb-article-attachments').append('<li id="'+data.id+'" class="edb-article-attachment"><a target="_blank" href="'+data.url+'">'+data.title+'</a></li>');
}

function disableEditArticle()
{
    editableDone();
    $('#edb-article-portrait-show').show();
    $('#edb-article-portrait-edit').hide();
    $('#edb-article-portrait').css('float', 'right');
    $('#edb-article-div-attachments').show();
    $('#edb-article-portrait-crop').off('click');
}

function enableEditArticle()
{
    $('.editable').aloha();
    $('.editable').addClass('editable-enabled');
    $('.editable-tools-mode-start').hide();
    $('.editable-tools-mode-edit').show();
    $('.editable-edit-title').show();
    $('#edb-article-portrait-show').hide();
    $('#edb-article-portrait-edit').show();
    $('#edb-article-portrait').css('float', 'none');
    $('#edb-article-div-attachments').hide();
}

function bindArticlesOnBrowser()
{
    $('.edb-add-category').siteform({
        root: $('#page-content'),
        onVisible: onVisible,
        onSendComplete: onSendComplete,
        onHide: onHideArticle,
    });
    $('.edb-add-article').siteform({
        root: $('#page-content'),
        onVisible: onVisible,
        onSendComplete: onSendComplete,
        onHide: onHideArticle,
    });
    $('.edb-article').siteform({
        root: $('#page-content'),
        onLoad: onLoadArticle,
        onVisible: onVisible,
        onSendComplete: onSendCompleteArticle,
        onHide: onHideArticle,
    });
    $('.edb-category').click(function() {
        var url = $(this).attr('edb-category-url');
        reloadBrowser(url);
    });

    /* portrait display/edit */
    $('#edb-article-portrait-show').fancybox();
    $('#edb-article-portrait-edit').fancybox({
        afterLoad: function() {
            $('#edb-article-image-select').show();
            $('#edb-article-image-crop').hide();
/*            $('#edb-article-image-select').find('img').click(function() {
                var url = $(this).attr('src');
                $('#edb-article-image-select').hide();
                $('#edb-article-image-crop').show();
                $('#edb-article-image-crop').find('img').attr('src', url);
                $.fancybox.update();
                $('#edb-article-image-crop').Jcrop();
            });*/
            $('.site-image-browser').find('img').click(function() {
                var url = $(this).attr('src');
                $('#edb-article-portrait').find('img').attr('src', url);
                $('#edb-article-portrait-show').attr('href', url);
                $.fancybox.close();
            });
        },
    });

    /* search */
    $('#edb-browser-search-query').keypress(function(e) {
        if (e.which == 13)
            doSearchArticle();
    });
    $('#edb-browser-search-send').click(doSearchArticle);

    var url = $('#edb-browser').attr('edb-browser-url');
    if (url) {
        var articleId = window.location.pathname.substring(url.length + 1);
        window.History.pushState({ }, null, url);
        if (articleId > 0) {
            $('[edb-article-id="'+articleId+'"]').click();
        }
    }
}
