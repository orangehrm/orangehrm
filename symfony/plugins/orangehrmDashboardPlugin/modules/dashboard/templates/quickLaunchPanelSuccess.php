<?php use_stylesheet(plugin_web_path('orangehrmDashboardPlugin', 'css/quicklaunch.css'));
use_javascript(plugin_web_path('orangehrmDashboardPlugin', 'js/jquery.easing.1.3.js'));
?>

<style type="text/css">
    .quickLinkText{
        display: block;
        text-align: center;
        color: black;
        font-weight:bold;
    }
    a:hover, a:visited, a:link, a:active{
        text-decoration: none;
    }
    div.quickLaunge{
        width: 100px;
        height: 80px;
        vertical-align:middle; 
        text-align:center
    }
    div.quickLaunge img{
        width: 50px;
        height: 50px;
    }
    table.quickLaungeContainer{
        width: auto;
    }
</style>

<div id="dashboard-quick-launch-panel-container">
    <div id="dashboard-quick-launch-panel-menu_holder">
        <table class="quickLaungeContainer">
            <?php
            $links = $links->getRawValue();
            if ($links) :
                $numLinks = count($links);
                $numCols = ceil($numLinks / $numRows);
                $linkNdx = 0;
                for ($rows = 0; ($rows < $numRows) && ($linkNdx < $numLinks); $rows++) :
                    ?>
                    <tr>
                        <?php
                        for ($cols = 0; ($cols < $numCols) && ($linkNdx < $numLinks); $cols++) :
                            $link = $links[$linkNdx];
                            $linkNdx++;
                            ?>
                            <td>
                                <div class="quickLaunge">
                                    <a href="<?php echo url_for($link['url']); ?>" target="<?php echo $link['target'] ?>" >
                                        <img src="<?php echo plugin_web_path($link['plugin'] , 'images/' . $link['image']) ?>"/>
                                        <span class="quickLinkText"><?php echo __($link['name']) ?></span>
                                    </a>
                                </div>                        
                            </td>                    
                    <?php endfor; ?>
                    </tr>
                <?php endfor;

            else :
                ?>
                <tr><td><?php echo __('No Links'); ?></td></tr>               
<?php endif;
?>

        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // hover color change effect
        $("#dashboard-quick-launch-panel-slider li").hover(function () {
            $(this).animate({opacity: 0.90}, 100, function () {
                $(this).animate({opacity: 1}, 0);
            });
        });
        // Trigger mouse move event over the 'menu_holder'.
        $("#dashboard-quick-launch-panel-menu_holder").mousemove(function (e) {
            // Enable scroll function only when the height of the 'slider' or menu is greater than the 'menu_holder'.
            if ($(this).height() < $("#dashboard-quick-launch-panel-slider").height()) {
                // Calculate the distance value from the 'menu_holder' y pos and page Y pos.
                var distance = e.pageY - $(this).offset().top;
                // Get the percentage value with respect to the Mouse Y on the 'menu_holder'.
                var percentage = distance / $(this).height();
                // Calculate the new Y position of the 'slider'. 
                var targetY = -Math.round(($("#dashboard-quick-launch-panel-slider").height() - $(this).height()) * percentage);
                // With jQuery easing funtion from easing plugin.
                $('#dashboard-quick-launch-panel-slider').animate({top: [targetY + "px", "easeOutCirc"]}, {queue: false, duration: 200});
                // Without easeing function. by default jQuery have 'swing'.
                //$('#slider').animate({top: [targetY+"px", "easeOutCirc"]}, { queue:false, duration:200 });
            }
        });
    });

</script>