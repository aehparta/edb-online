
var edb_SelectedItems = { articles: {}, categories: {}, };

function edb_OnReadyBrowserBundle() {
    $(document).on('mouseenter', '.edb-article-attachment', function() {
        $(this).find('.edb-article-attachment-tools').show();
    });

    $(document).on('mouseleave', '.edb-article-attachment', function() {
        $(this).find('.edb-article-attachment-tools').hide();
    });

//     window.History.Adapter.bind(window,'statechange',function(){
//         var state = window.History.getState();
//         if (state.data.articleId !== undefined && $('#edb-form-article').length < 1) {
//             $('[edb-article-id="'+state.data.articleId+'"]').click();
//         } else if (state.data.articleId === undefined && $('#edb-form-article').length > 0) {
//             $('.site-form-close').click();
//         }
//     });

    /* do paste */
    $('body').keypress(function(e) {
        if (e.which == 118 && e.ctrlKey) {
            var category_id = $('#edb-browser').attr('edb-category-id');
            for (id in edb_SelectedItems.articles) {
                var url = edb_SelectedItems.articles[id];
                $.get(url, function(data) {
                    if (data.success === true) {
                        data.category_id = category_id;
                        $.ajax({ type: 'PUT', data: data, url: url });
                    }
                });
            }
            for (id in edb_SelectedItems.categories) {
                var url = edb_SelectedItems.categories[id];
                $.get(url, function(data) {
                    if (data.success === true) {
                        data.parent = category_id;
                        $.ajax({ type: 'PUT', data: data, url: url });
                    }
                });
            }
            edb_SelectedItems = { articles: {}, categories: {}, };
        }
    });

    $('body').on('click', '.edb-tools-article-edit', edb_EnableEditArticle);
    $('body').on('click', '.edb-tools-article-stop', edb_DisableEditArticle);
    $('body').on('click', '.edb-tools-article-close', edb_OnArticleHide);
    $('body').on('click', '#edb-article-stock-add', function() {
        $('#edb-article-stock-empty-item').clone().appendTo('#edb-article-stock').attr('id', '').addClass('edb-article-stock-item').show();
    });
    
    $('body').on('click', '#edb-article-stock-update', function() {
        var url = $('#edb-article-stock').attr('site-form-url');
        $('.edb-article-stock-item').each(function() {
            var item = {
                name: $(this).find('#edb-article-stock-item-name').val(),
                quantity: $(this).find('#edb-article-stock-item-qty').val(),
                package: $(this).find('#edb-article-stock-item-package').val(),
                note: $(this).find('#edb-article-stock-item-note').val(),
                storage: $(this).find('#edb-article-stock-item-storage').val(),
                remove: $(this).find('#edb-article-stock-item-remove').attr('checked') == 'checked' ? true : false,
            };
            $.ajax({ type: 'PUT', data: item, url: url });
        });
    });

    $('body').on('click', '.edb-tools-article-delete', function(e) {
        e.preventDefault();
        var url = $(this).attr('site-form-url');
        var urlAction = $(this).attr('site-form-action');
        $.siteform({
            href: url,
            formAction: urlAction,
            onFormDelete: edb_OnArticleAddDeleteComplete,
        });
        return false;
    });

    $('body').on('click', '.edb-article-attachment-delete', function() {
        console.log('att delete');
        if (confirm('Are you sure you want to delete this attachment?')) {
            var attachment = $(this).parents('.edb-article-attachment');
            if (!attachment)
                return;

            var url = $(attachment).attr('edb-attachment-mod-url');
            $.ajax({ type: 'DELETE', url: url, context: this, }).done(function (data) {
                $(attachment).remove();
            });
        }
    });
    
    $('body').on('click', '.edb-tools-category-close', edb_OnCategoryHide);

    edb_SetupAloha('#page-content');
    $('.editable-enabled').aloha();

    var url = $('#edb-browser').attr('edb-controller');
    edb_ReloadBrowser(url);
};


function edb_SetupAloha(element)
{
    $(element).find('.edb-tools-article-stop').on('click', function() {
        edb_EditableDone();
    });

    $(element).find('.edb-tools-article-edit').on('click', function() {
        $('.editable').aloha();
        $('.editable').addClass('editable-enabled');
        $('.editable-tools-mode-start').hide();
        $('.editable-tools-mode-edit').show();
        $('.editable-edit-title').show();
    });
}

function edb_OnArticleSaved()
{
    return false;
}

function edb_EditableDone()
{
    $('.editable').mahalo();
    $('.editable').removeClass('editable-enabled');
    $('.editable').removeClass('aloha-editable-highlight');
    $('.editable-tools-mode-start').show();
    $('.editable-tools-mode-edit').hide();
    $('.editable-edit-title').hide();
}

function edb_ReloadBrowser(url) {
    $('#edb-browser-path li').hide();
    $('#edb-browser-path .edb-path-root').show();
    $('.edb-articles').fadeOut('fast').promise().done(function() {
        $.get(url, function(data) {
            $('#edb-browser').replaceWith(data);
            edb_BindArticlesOnBrowser();
        });
    });
}

function edb_DoSearchArticle() {
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
            $('.edb-article').edb_editor({
                root: $('#page-content'),
                onLoad: edb_OnArticleLoad,
                onVisible: edb_OnArticleLoaded,
                onSendComplete: edb_DisableEditArticle,
                onHide: edb_OnArticleHide,
            });
            $('.edb-articles').fadeIn('fast');
        });
    });
}

function edb_OnArticleLoad() {
    $('#edb-browser').hide();
    $('#edb-article-loading').show();
}

function edb_OnArticleLoaded() {
    var id = $(this).attr('edb-article-id');
    if (id == 0 || id === undefined) {
        edb_EnableEditArticle();
    } else {
        edb_SetupAlohaArticle(this);
        edb_DisableEditArticle();
    }

//    $(this).find('.edb-article-close').on('click', edb_OnArticleHide);
}

function edb_OnArticleAddDeleteComplete(data) {
    edb_OnArticleHide();
    var url = $('#edb-browser').attr('edb-controller');
    edb_ReloadBrowser(url);
}

function edb_OnArticleHide() {
    $('#edb-form-article').fadeOut('fast', function() {
        $('#edb-form-article').remove();
    //    var url = $('#edb-browser').attr('edb-browser-url');
    //     window.History.back();
    //     window.History.replaceState(null, null, url);
        $('#edb-article-loading').hide();
        $('#edb-browser').show();
        $('#page-content').fadeIn('fast');
    });
}

function edb_OnCategoryLoaded() {
    var id = $(this).attr('edb-category-id');
    if (id == 0 || id === undefined) {
        edb_EnableEditArticle();
    } else {
        edb_SetupAlohaArticle(this);
        edb_DisableEditArticle();
    }
}

function edb_OnCategoryAddDeleteComplete(data) {
}

function edb_OnCategoryHide() {
    $('#edb-form-category').fadeOut('fast', function() {
        $('#edb-form-category').remove();
        $('#edb-browser').show();
        $('#page-content').fadeIn('fast');
    });
}

function edb_SetupAlohaArticle(element)
{

    /* portrait display/edit */
    $('#edb-article-portrait-show').fancybox();
    $('#edb-article-portrait-edit').fancybox({
        onStart: function() {
            $('#edb-article-image-browser').show();
        },
        onComplete: function() {
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
        onClosed: function() {
            $('#edb-article-image-browser').hide();
        },
    });

    articleSetupUpload();

    var url = $(element).attr('edb-browser-url');
//     window.History.pushState({ articleId: articleId }, null, url);
}

function articleSetupUpload()
{
    var apikey = $('#edb-article-attachments').attr('apikey');
    var uploadUrl = $('#edb-article-attachments').attr('file-post-url');
    var articleId = $('#edb-form-article').attr('edb-article-id');

    $('#edb-form-article').find('#edb-article-attachment-upload').uploadify({
        'height': 20,
        'width': 120,
        'swf': '/js/uploadify-3.1.1/uploadify.swf',
        'uploader': uploadUrl,
        'buttonText': 'Upload attachment',
        'removeCompleted': true,
        'onUploadSuccess': articleOnUploadSuccess,
        'onQueueComplete': articleOnQueueComplete,
        'onUploadError': function(file, errorCode, errorMsg, errorString) { console.log(file+': '+errorCode+' / '+errorMsg+' / '+errorString); },
        'formData': { 'article_id': articleId, 'apikey': apikey },
    });
    $('#edb-form-article').find('#edb-article-attachment-upload').addClass('site-form-button').disableSelection();
    
    $('#edb-article-upload-button-icon').prependTo('#edb-article-attachment-upload-button');
}

function articleOnUploadSuccess(file, data, response)
{
    data = JSON.parse(data);
    if (data.success !== true) {
        var message = 'uknown error';
        if (data.message)
            message = data.message;
        alert('Upload failed, reason: '+message);
    } else {
    }
}

function articleOnQueueComplete()
{
    var url = $('#edb-article-div-attachments').attr('site-form-reload-url');
    $.get(url, function(data) {
        $('#edb-article-div-attachments').replaceWith(data);
        articleSetupUpload();
    });
}

function edb_DisableEditArticle()
{
    edb_EditableDone();
    if ($('#edb-article-portrait-show img').attr('src').length > 0)
        $('#edb-article-portrait-show').show();
    else
        $('#edb-article-portrait-show').hide();
    $('#edb-article-portrait-edit').hide();
    $('#edb-article-portrait').css('float', 'right');
    $('#edb-article-div-attachments').show();
    $('#edb-article-portrait-crop').off('click');
    $('.edb-article-edit-extras').hide();
}

function edb_EnableEditArticle()
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
    $('.edb-article-edit-extras').show();
}

function edb_BindArticlesOnBrowser()
{
    $('.edb-select').click(function(e) {
        e.preventDefault();
        $(this).parent().find('.edb-selected').show();
        $(this).hide();

        var elem = $(this).parent().parent();
        var url = elem.attr('edb-modify-url');
        if (elem.hasClass('edb-article')) {
            var id = elem.attr('edb-article-id');
            edb_SelectedItems.articles[id] = url;
        } else {
            var id = elem.attr('edb-category-id');
            edb_SelectedItems.categories[id] = url;
        }

        return false;
    });
    $('.edb-selected').click(function(e) {
        e.preventDefault();
        $(this).parent().find('.edb-select').show();
        $(this).hide();
        return false;
    });

    $('.edb-add-article').edb_editor({
        root: $('#page-content'),
        onVisible: edb_OnArticleLoaded,
        onSendComplete: edb_OnArticleAddDeleteComplete,
        onHide: edb_OnArticleHide,
    });
    $('.edb-add-category').edb_editor({
        root: $('#page-content'),
        onVisible: edb_OnCategoryLoaded,
        onSendComplete: edb_OnCategoryAddDeleteComplete,
        onHide: edb_OnCategoryHide,
    });
    $('.edb-article').edb_editor({
        root: $('#page-content'),
        onLoad: edb_OnArticleLoad,
        onVisible: edb_OnArticleLoaded,
        onSendComplete: edb_DisableEditArticle,
        onHide: edb_OnArticleSaved,
    });
    $('.edb-category').click(function() {
        var url = $(this).attr('edb-category-url');
        edb_ReloadBrowser(url);
    });

    /* search */
    $('#edb-browser-search-query').keypress(function(e) {
        if (e.which == 13)
            edb_DoSearchArticle();
    });
    $('#edb-browser-search-send').click(edb_DoSearchArticle);

    var url = $('#edb-browser').attr('edb-browser-url');
    if (url) {
        var articleId = window.location.pathname.substring(url.length + 1);
//         window.History.pushState({ }, null, url);
        if (articleId > 0) {
            $('[edb-article-id="'+articleId+'"]').click();
        }
    }
}
