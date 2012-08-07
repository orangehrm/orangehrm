$(document).ready(function(){

    /* Fix for IE9 white space issue in tables: Begins */

    var whiteSpaceExpr = new RegExp('>[ \t\r\n\v\f]*<', 'g');

    $('table').each(function(){
        var tableHtml = $(this).html();
        $(this).html(tableHtml.replace(whiteSpaceExpr, '><'));
    });

    /* Fix for IE9 white space issue in tables: Ends */        

});

