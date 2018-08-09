<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
    $steps = array('dbInfo' => __('Database Info'), 'sysCheck' => __('System Check'), 'verInfo' => __('Version Info'), 'dbChange' => __('Database Changes'), 'confInfo' => __('Configuration Info'), 'completion' => __('Completion'));
    $currScreen = $sf_user->getAttribute('currentScreen');
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php $sf_response->setTitle(__('OrangeHRM Web Upgrade Wizard'))?>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <?php use_stylesheet('style.css') ?>
  </head>
  <body>
      <div id="body">
        <div id="logoContainer">
            <?php echo image_tag('orange3.png');?>
        </div>
        <div id="outerBox">
            <table border="0" cellpadding="0" cellspacing="0">
              <tr>
                <?php
                    $tocome = '';
                    foreach ($steps as $key => $step) {
                        if ($currScreen == $key) {
                            $tabState = 'Active';
                        } else {
                            $tabState = 'Inactive';
                        }
                ?>
                <td nowrap="nowrap" class="left_<?php echo $tabState?>">&nbsp;</td>
                <td nowrap="nowrap" class="middle_<?php echo $tabState.$tocome?>"><?php echo $steps[$key]?></td>
                <td nowrap="nowrap" class="right_<?php echo $tabState?>">&nbsp;</td>
                <?php
                    if ($tabState == 'Active') {
                        $tocome = '_tocome';
                    }
                }
                ?>
              </tr>
            </table>
            <div name="content" id="content">
                <?php echo $sf_content ?>
            </div>
        </div>
        
        <div id="footer">
        <?php include_once(sfConfig::get('sf_root_dir') . "/../symfony/apps/orangehrm/templates/_copyright.php");?>
        </div>
    </div>
</body>
</html>

