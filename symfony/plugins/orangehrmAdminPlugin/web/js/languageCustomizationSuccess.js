$(document).ready(function () {

    $('#searchBtn').click(function () {
        $('#searchTranslationLanguage_reset').val(false);
        $('#frmTranslateLanguageSearch').submit();
    });

    $('#resetBtn').click(function () {
        $('#searchTranslationLanguage_reset').val(true);
        $('#frmTranslateLanguageSearch').submit();
    });
});
