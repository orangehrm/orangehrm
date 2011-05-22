function ohrmList_init() {
    $('.data-table tbody tr').hover(function() {  // highlight on mouse over
        $(this).removeClass();
        $(this).addClass("trHover");
    });

    $('.data-table tbody tr').mouseout(function() { // redraw table raws with alternate colors
       var even = true;
       $('.data-table tbody tr').each(function() {
           $(this).addClass((even) ? 'odd' : 'even');
           even = !even;
        });
    });

    $('#ohrmList_chkSelectAll').click(function() {
        $('.data-table input[id^="ohrmList_chkSelectRecord_"]').attr('checked', $(this).attr('checked'));
    });

    $('.data-table input[id^="ohrmList_chkSelectRecord_"]').click(function() {
        var selectorCheckboxes = $('.data-table input[id^="ohrmList_chkSelectRecord_"]');
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
