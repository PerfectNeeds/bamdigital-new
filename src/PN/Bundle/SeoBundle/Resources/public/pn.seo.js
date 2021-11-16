//inti ajax variable 
var currentRequest = null;

var ckEditorEditor = null;
var metaDescriptionInput = $('#' + seoFromId + '_metaDescription');
var focusKeywordInput = $('#' + seoFromId + '_focusKeyword');
var titleInput = $('#' + seoFromId + '_title');
var slugInput = $('#' + seoFromId + '_slug');
var stateHiddenInput = $('#' + seoFromId + '_state');

function strip(html) {
    var tmp = document.createElement("div");
    tmp.innerHTML = html;

    if (tmp.textContent == "" && typeof tmp.innerText == "undefined") {
        return "";
    }

    return tmp.textContent || tmp.innerText;
}
function countWords(text) {
    var normalizedText = text.
            replace(/(\r\n|\n|\r)/gm, " ").
            replace(/^\s+|\s+$/g, "").
            replace("&nbsp;", " ");

    normalizedText = strip(normalizedText);

    var words = normalizedText.split(/\s+/);

    for (var wordIndex = words.length - 1; wordIndex >= 0; wordIndex--) {
        if (words[wordIndex].match(/^([\s\t\r\n]*)$/)) {
            words.splice(wordIndex, 1);
        }
    }

    return (words.length);
}

CKEDITOR.on('instanceReady', function (e) {
    ckEditorEditor = CKEDITOR.instances[descriptionId];
    countTinyMCEWord(ckEditorEditor);
    e.editor.on('change', function (event) {
        var ed = CKEDITOR.instances[descriptionId];//Value of Editor
        ckEditorEditor = ed;
        countTinyMCEWord(ed);
    });
});


// remove extra spaces
function filterText(text) {
    return text.replace('  ', ' ').trim();
}

//show analysis item
function showAnalysisItem(elementId) {
    $('#' + elementId).removeClass('hidden');
    $('#' + elementId).insertBefore('#analysis li:eq(0)');
}

//hide analysis item
function hideAnalysisItem(elementId) {
    $('#' + elementId).addClass('hidden');
    $('#' + elementId).insertAfter('#analysis li:eq(-1)');
}

// reorder analysis item
function arrangeAnalysisItemItems() {
    $('#analysis li.border-warning').insertAfter($('#analysis li:last-child'));
    $('#analysis li.border-success').insertAfter($('#analysis li:last-child'));
}

// change color of an analysis item and reorder them
function changeAnalysisItemColor(elementId, color) {
    $('#' + elementId).removeClass('border- success border-warning border-danger').addClass('border-' + color);
    arrangeAnalysisItemItems();

    var success = $('#analysis li.border-success').length;
    var warning = $('#analysis li.border-warning').length;
    var danger = $('#analysis li').not('.hidden, .border-warning, .border-success').length;

    $('#stateColor').removeClass('text-danger text-warning text-success');
    if (success > warning && success > danger) {
        stateHiddenInput.val(3);
        $('#stateColor').addClass('text-success');
    } else if (warning > success && warning > danger) {
        $('#stateColor').addClass('text-warning');
        stateHiddenInput.val(2);
    } else {
        $('#stateColor').addClass('text-danger');
        stateHiddenInput.val(1);
    }
}

// change input text color
function changeInputTextColor(elementId, color) {
    if (color === '') {
        $('#' + elementId).removeClass('text-danger');
    } else {
        $('#' + elementId).removeClass('text-danger')
                .addClass(color);

    }
}

// compare between focus keyword and title
function focusKeywordVsTitle() {
    title = titleInput.val();
    focusKeyword = focusKeywordInput.val();
    if (title.length > 0 && focusKeyword.length > 0) {
        if (title.includes(focusKeyword)) {
            hideAnalysisItem('analysis-5');
            changeAnalysisItemColor('analysis-7', 'success');
            return false;
        }
    }
}
pageTitle.blur(function () {
    if (titleInput.prop('value') === '') {
        var value = $(this).val();
        var slug = convertToSlug(value);
        titleInput.val($(this).val());
        slugInput.val(slug);
        titleInput.trigger('keyup');
        slugInput.trigger('keyup');
    }
});

// compare between focus keyword and slug
function focusKeywordVsSlug() {
    slug = slugInput.val().replace(/[\W_]/g, ' ').replace(/\s+/g, '').trim().toLowerCase();
    focusKeyword = focusKeywordInput.val().replace(/[\W_]/g, ' ').replace(/\s+/g, '').trim().toLowerCase();
    include = slug.includes(focusKeyword);
    if (include !== false && focusKeyword !== '') {
        changeAnalysisItemColor('analysis-8', 'success');
    } else {
        changeAnalysisItemColor('analysis-8', 'warning');
    }
}

// convert text to slug
function convertToSlug(Text) {
    return Text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\u0100-\uFFFF\w\-]/g, '-') // Remove all non-word chars ( fix for UTF-8 chars )
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');
}

function decodeHtml(encodedString) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = encodedString;
    return textArea.value;
}
// count tinymce word and calculate the density
function countTinyMCEWord(ed) {
    //count tinymce word
    var wordCount = countWords(ed.getData());
    $('#seoContentCount').text(wordCount);
    if (wordCount >= 300) {
        changeAnalysisItemColor('analysis-4', 'success');
    } else if (wordCount > 0) {
        changeAnalysisItemColor('analysis-4', 'warning');
    } else if (wordCount === 0) {
        changeAnalysisItemColor('analysis-4', 'danger');
    }

// calculate the density
    var content = decodeHtml(ed.getData()).toLowerCase();
    var focusKeywordValue = focusKeywordInput.val().toLowerCase();
    var focusKeywordCount = content.split(focusKeywordValue).length - 1;
    var density = ((focusKeywordCount / wordCount) * 100);
    if (focusKeywordValue.length > 0 && wordCount > 0 && density > 0) {
        changeAnalysisItemColor('analysis-12', 'success');
        density = density.toFixed(2);
    } else {
        density = 0.00;
        focusKeywordCount = 0;
        changeAnalysisItemColor('analysis-12', 'danger');
    }
    $('#seoDensity').text(density);
    $('#seoDensityTime').text(focusKeywordCount);

    var html = $.parseHTML(ed.getData());
    if (html !== null) {
        $.each(html, function (i, el) {
            if (el.nodeName === 'P') {
                var checkFirstParagraph = el.innerText.includes(focusKeywordInput.val());
                if (checkFirstParagraph === true && focusKeywordInput.val()) {
                    changeAnalysisItemColor('analysis-11', 'success');
                } else {
                    changeAnalysisItemColor('analysis-11', 'danger');
                }
                return false;
            }
        });
    }
}

$('.countLength').keyup(function () {
    var lengthBadge = $(this).parent().find('.lengthBadge');
    var maxLength = $(this).data('max-length');
    var lengthBadge = $(this).parent().find('.lengthBadge');

    var inputValue = $(this).val();
    var length = inputValue.length;

    // An error appears if the entry contains a new line
    if (inputValue.includes("\n")) {
        $(this).addClass('text-danger');
    } else {
        $(this).removeClass('text-danger');
    }

    lengthBadge.removeClass('label-warning label-warning label-default label-danger');
    if (length === 0) {
        lengthBadge.addClass('label-default');
    } else if (length === maxLength) {
        lengthBadge.addClass('label-success');
    } else if (length > maxLength) {
        lengthBadge.addClass('label-danger');
    } else {
        lengthBadge.addClass('label-warning');
    }

    lengthBadge.find('.length').text(length);
});

function snippetPreview(element) {
    var previewElementId = element.data('preview');

    var inputValue = element.val();
    var length = inputValue.length;

    if (length > 0) {
        $('#' + previewElementId).text(inputValue);
    } else {
        var inputId = element.attr('id');
        if (inputId === seoFromId + '_title') {
            $('#' + previewElementId).text('[PAGE TITLE]');
        } else if (inputId === seoFromId + '_title') {
            $('#' + previewElementId).text('Please provide a meta description by editing the snippet below.');
        }
    }
}

// show in snippet preview
//$('#pn_bundle_productbundle_collection_seo_title, #pn_bundle_productbundle_collection_seo_metaDescription').keyup(function () {
//    var previewElementId = $(this).data('preview');
//
//    var inputValue = $(this).val();
//    var length = inputValue.length;
//
//    if (length > 0) {
//        $('#' + previewElementId).text(inputValue);
//    } else {
//        var inputId = $(this).attr('id');
//        if (inputId === seoFromId+'_title') {
//            $('#' + previewElementId).text('[PAGE TITLE]');
//        } else if (inputId === seoFromId+'_title') {
//            $('#' + previewElementId).text('Please provide a meta description by editing the snippet below.');
//        }
//    }
//});

// check seo title length
titleInput.keyup(function () {
    value = titleInput.val();
    length = value.length;
    focusKeywordVsTitle();
    if (length >= 50 && length <= 60) {
        changeAnalysisItemColor('analysis-10', 'success');
    } else {
        changeAnalysisItemColor('analysis-10', 'danger');
    }

    snippetPreview($(this));
});
// convert slug text to slugify format
slugInput.blur(function () {
    var value = filterText($(this).val());
    var slugify = convertToSlug(value);
    slugInput.val(slugify);
    focusKeywordVsSlug();
});

// Check if  the slug  is used before
slugInput.keyup(function () {
    var previewElementId = $(this).data('preview');
    var value = filterText($(this).val());
    var slugify = convertToSlug(value);
    $('#' + previewElementId).text(slugify);

    if (value.length > 0) {
        currentRequest = $.ajax({
            url: checkSlugIsUsedUrlAjax,
            data: {slug: slugify, seoId: seoId, seoBaseRouteId: seoBaseRouteId},
            beforeSend: function () {
                if (currentRequest !== null) {
                    currentRequest.abort();
                }
            }, success: function (result) {
                if (result == 0) {
                    slugInput.parent().removeClass('has-error');
                    slugInput.parent().find('.help-block').remove();
                } else {
                    slugInput.parent().addClass('has-error');
                    slugInput.parent().find('.help-block').remove();
                    var error = $('<span/>').addClass('help-block').text('This slug is used before');
                    slugInput.parent().append(error);
                }
            }
        });
    }
});

// remove extra spaces between words
metaDescriptionInput.blur(function () {
    var value = $(this).val();
    $(this).val(filterText(value));
});

// check meta description length
metaDescriptionInput.keyup(function () {
    var value = filterText($(this).val());
    var length = value.length;
    if (length === 0) {
        showAnalysisItem('analysis-2');
        hideAnalysisItem('analysis-3');
    } else {
        if (length < 120) {
            showAnalysisItem('analysis-3');
            hideAnalysisItem('analysis-6');
        } else if (length >= 120 && length < 320) {
            hideAnalysisItem('analysis-3');
            hideAnalysisItem('analysis-6');
        } else if (length === 320) {
            hideAnalysisItem('analysis-3');
            changeAnalysisItemColor('analysis-6', 'success');
        } else {
            changeInputTextColor($(this).attr('id'), 'text-danger');
            hideAnalysisItem('analysis-3');
            hideAnalysisItem('analysis-6');
        }
        hideAnalysisItem('analysis-2');
    }
    snippetPreview($(this));
});

focusKeywordInput.keyup(function () {
    var previewElementId = $(this).data('preview');
    var value = $(this).val().trim();
    if (value.length === 0) {
        showAnalysisItem('analysis-1');
        hideAnalysisItem('analysis-5');
        hideAnalysisItem('analysis-9');
    } else {
        showAnalysisItem('analysis-9');
        showAnalysisItem('analysis-5');
        hideAnalysisItem('analysis-1');
    }
    $('.copyFocusKeyword').text(value);
    //slugify
    slugify = convertToSlug(value);
    $('#' + previewElementId).text(slugify);

    currentRequest = $.ajax({
        url: focusKeywordUrlAjax,
        data: {focusKeyword: value, seoId: seoId},
        beforeSend: function () {
            if (currentRequest !== null) {
                currentRequest.abort();
            }
        }, success: function (result) {
            if (result == 0) {
                changeAnalysisItemColor('analysis-9', 'success');
            } else {
                changeAnalysisItemColor('analysis-9', 'danger');
            }
        }
    });
    if (ckEditorEditor !== null) {
        countTinyMCEWord(ckEditorEditor);
    }
    focusKeywordVsTitle();
    focusKeywordVsSlug();
});
// toggle Edit snippet  button
$("button#seoSnippetEditBtn").click(function () {
    $("#seoSnippetEdit").slideToggle();
});
function init() {
//    autosize(metaDescriptionInput);
    $('#' + seoFromId + '_title').trigger('keyup');
    $('#' + seoFromId + '_metaDescription').trigger('keyup');
    $('#' + seoFromId + '_slug').trigger('keyup');
    $('#' + seoFromId + '_focusKeyword').trigger('keyup');
    arrangeAnalysisItemItems();
}

$(document).ready(function () {
    init();
}
);