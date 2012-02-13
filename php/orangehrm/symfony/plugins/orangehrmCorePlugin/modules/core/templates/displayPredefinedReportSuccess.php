<div style="width: 100%;text-align: center;padding-top: 10px;font-size: 18px;"> <?php echo __('Report Name'); ?> : <?php echo $report->getName(); ?> </div>
<?php include_component('core', 'ohrmList', $parmetersForListComponent); ?>

<style type="text/css">
    th{
        border: 1px solid #BBBBBB;
        background-color: #FFFFFF;
        vertical-align: top;
    }

    .outerbox .top .middle{
        background: none repeat scroll 0 0 #FFFFFF;
        height: 2px;
        margin: 0 8px;
    }

    .outerbox .bottom .middle{
        background: none repeat scroll 0 0 #00ee00;
        height: 1px;
        margin: 0 8px;
    }

    .outerbox .top .left{
        background: none repeat scroll 0 0 #FFFFFF;
        height: 1px;
        margin: 0 8px;
    }

    .outerbox .top .right{
        background: none repeat scroll 0 0 #FFFFFF;
        height: 1px;
        margin: 0 8px;
    }

    .outerbox .maincontent{
        border-style: solid;
        border-color: #000000;
    }

    .outerbox table.data-table tbody {
        border: 1px solid #BBBBBB;
    }
    .outerbox table.data-table tbody tr td {
        vertical-align: top;
    }
    
    .outerbox table.data-table table.valueListCell td {
        padding-top: 0;
        vertical-align: top;
    }

    .outerbox table.data-table table.valueListCell tbody {
        border-width: 0;
    }
    
    .headerCell{
        font-weight: bold;
        font-size: 12px;
        color: #000000;
    }

</style>