<style type="text/css">
    h1{
        padding-left: auto;
        padding-right: auto;
        font-size: 20px;
    }

    #sidenav{
        margin: 2px 0 0 !important;
    }

    .pimPane #authType h1 {
        background: #f3f3f3 url('<?php echo theme_path('images/h1-bg.png')?>') left bottom repeat-x;
        font-size:14px;
        color:#5d5d5d;
        padding:9px 15px;
        line-height:20px;
        border:1px solid #dedede;
        -moz-border-radius-topleft: 3px;
        -webkit-border-top-left-radius: 3px;
        border-top-left-radius: 3px;
        -moz-border-radius-topright: 3px;
        -webkit-border-top-right-radius: 3px;
        border-top-right-radius: 3px;
    }
</style>

<div id="sidebar">
    <div id="authType">
        <h1><?php echo __('Authentication Type'); ?></h1>
    </div>
    <ul id="sidenav">
        <?php
        foreach ($menuItems as $action => $properties):
            $label = $properties['label'];
            $listClass = ($action == $currentAction) ? ' class="selected"' : '';
            $url = url_for($properties['module'] . '/' . $action);
            ?>
            <li<?php echo $listClass; ?>><a href="<?php echo $url; ?>"><?php echo __($label); ?></a></li>
            <?php
        endforeach;
        ?>
    </ul>

</div>