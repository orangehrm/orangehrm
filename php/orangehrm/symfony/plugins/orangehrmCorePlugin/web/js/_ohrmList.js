$(document).ready(function() {
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
});


