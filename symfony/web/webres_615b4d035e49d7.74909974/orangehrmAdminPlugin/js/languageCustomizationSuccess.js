$(document).ready(function () {

    var isTranslatedTextsChanged = false;
    var changedTextsIds = [];

    $('#btnCancel').hide();
    $('#btnSave').val(lang_edit);
    $('#btnSave').show();

    $('#searchBtn').click(function () {
        $('#searchTranslationLanguage_reset').val(false);
        $('#frmTranslateLanguageSearch').submit();
    });

    $('#resetBtn').click(function () {
        $('#searchTranslationLanguage_sourceText').val('');
        $('#searchTranslationLanguage_translatedText').val('');
        $('#searchTranslationLanguage_reset').val(true);
        $('#frmTranslateLanguageSearch').submit();
    });

    $('#btnSave').click(function () {
        var action = $('#btnSave').val();
        if (action == lang_edit) {
            editTranslations();
        } else {
            saveTranslations();
        }
    });

    $('#btnCancel').click(function () {
        // location.reload() caching textarea values on Firefox
        // https://developer.mozilla.org/en-US/docs/Mozilla/Firefox/Releases/1.5/Using_Firefox_1.5_caching
        window.location = window.location;
    });

    function editTranslations() {
        $('.translated-textarea').prop("disabled", false);
        $('#btnSave').val(lang_save);
        $('#btnCancel').show();
    }

    function saveTranslations() {
        $(window).unbind('beforeunload', beforeunloadHandler);

        // TODO:: validate placeholder changes

        changedTextsIds.forEach(function (id) {
            var translatedTextareaId = '#translatedTextarea_' + id;
            var translatedTextId = '#translatedText_' + id;
            var defaultValue = $(translatedTextareaId).prop('defaultValue');
            var changedText = $(translatedTextareaId).val();

            if (defaultValue != changedText) {
                var changedInputName = 'changedTranslatedText[' + id + ']';
                $(translatedTextId).prop('name', changedInputName)
                // Remove newline from end of string
                changedText = changedText.replace(/(\r\n|\n|\r)$/gm, "");
                // Remove newlines middle of string and replace with space
                changedText = changedText.replace(/(\r\n|\n|\r)/gm, " ");
                $(translatedTextId).val(changedText);
            }
        });
        $('#frmList_ohrmListComponent').submit();
    }

    var beforeunloadHandler = function () {
        return 'Changes that you made may not be saved.';
    };

    $('.translated-textarea').change(function () {
        var name = $(this).prop('name');
        if (!changedTextsIds.includes(name)) {
            changedTextsIds.push(name);
        }

        if (!isTranslatedTextsChanged) {
            $(window).bind('beforeunload', beforeunloadHandler);
        }
        isTranslatedTextsChanged = true;
    });

    $("#frmTranslateLanguageSearch").validate({
        rules: {
            'searchTranslationLanguage[sourceText]': {
                maxlength: 250
            },
            'searchTranslationLanguage[translatedText]': {
                maxlength: 250
            },
        },
        messages: {
            'searchTranslationLanguage[sourceText]': {
                maxlength: lang_LengthExceeded
            },
            'searchTranslationLanguage[translatedText]': {
                maxlength: lang_LengthExceeded
            },
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});
