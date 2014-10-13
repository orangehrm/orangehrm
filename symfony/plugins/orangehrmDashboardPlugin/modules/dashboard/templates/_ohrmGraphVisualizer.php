<!--[if lte IE 8]><?php echo javascript_include_tag(plugin_web_path('orangehrmDashboardPlugin', 'js/flot/excanvas.min.js')); ?><![endif]-->
<?php echo javascript_include_tag(plugin_web_path('orangehrmDashboardPlugin', 'js/flot/jquery.flot.min.js')) ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmDashboardPlugin', 'js/flot/jquery.flot.pie.min.js')) ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmDashboardPlugin', 'js/flot/JUMFlot.min.js')) ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmDashboardPlugin', 'js/graph-visualizer/' . $chart->getType() . '.js')) ?>

<?php
$chartProperties = $chart->getProperties();
?>

<?php
if ($chart->hasData() || $chart->showEmptyGraph()):
    $divId = 'div_graph_display_' . $chart->getChartNumber();
    $metaDataObject = $chart->getMetaDataObject();
    $legend = $metaDataObject->getLegend();
    ?>
    <div id="<?php echo $divId; ?>" style="<?php echo $chart->getStyleString(); ?>"></div>
    <?php if ($legend->getUseSeparateContainer()) { ?>   
        <div id="<?php echo $divId; ?>_legend" style="width:100px;display:block;float:left;"></div>
    <?php } ?>
    <br class="clear" />
    <div id="<?php echo 'hover_' . $divId; ?>" style="width:auto;"></div>    

    <script type="text/javascript">
        $(document).ready(function () {

            var data = <?php echo json_encode($chart->getData()->getRawValue()); ?>;
            var graph = <?php echo json_encode($metaDataObject->getRawValue()); ?>;


            var properties = <?php echo json_encode($chartProperties->getRawValue()); ?>;

            visualize<?php echo $chart->getChartFunction() . "(data, graph, properties, '" . $divId . "');"; ?>
        });
    </script>   
<?php endif; ?>