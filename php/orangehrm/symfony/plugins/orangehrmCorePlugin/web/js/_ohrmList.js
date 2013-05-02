function ohrmList_init() {

    $('#ohrmList_chkSelectAll').click(function() {
        
        var valueToSet = false;
        
        if ($(this).attr('checked') == 'checked') {
            valueToSet = true;
        }        
        
        $('table.table input[id^="ohrmList_chkSelectRecord_"]').attr('checked', valueToSet);
        
    });

    $('table.table input[id^="ohrmList_chkSelectRecord_"]').click(function() {
        var selectorCheckboxes = $('table.table input[id^="ohrmList_chkSelectRecord_"]');
        var isAllChecked = (selectorCheckboxes.size() == selectorCheckboxes.filter(':checked').size());
        $('#ohrmList_chkSelectAll').attr('checked', isAllChecked);
    });
};

/**
 * Used in pagination links
 * TODO: Rename with a proper method once paging_links_js partial is replaced
 */
function submitPage(pageNumber) {
    var baseUrl = location.href;
    var urlSuffix = '';
    
    if (baseUrl.match(/index\.php\/\w{1,}$/)) {
        baseUrl += '/index/';
    }

    if (baseUrl.match(/pageNo\/\d{1,}/)) {
        baseUrl = baseUrl.replace(/pageNo\/\d{1,}/, 'pageNo/' + pageNumber);
    } else {
        urlSuffix = (baseUrl.match(/\/$/) ? '' : '/') + 'pageNo/' + pageNumber;
    }
    
    location.href = baseUrl + urlSuffix;
}
