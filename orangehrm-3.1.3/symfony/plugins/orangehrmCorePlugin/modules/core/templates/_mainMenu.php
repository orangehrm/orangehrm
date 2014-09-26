<?php

function getSubMenuIndication($menuItem) {
    
    if (count($menuItem['subMenuItems']) > 0) {
        return ' class="arrow"';
    } else {
        return '';
    }
    
}

function getListItemClass($menuItem, $currentItemDetails) {
    
    $flag = false;
    
    if ($menuItem['level'] == 1 && $menuItem['id'] == $currentItemDetails['level1']) {
        return ' class="current"';
    } elseif ($menuItem['level'] == 2 && $menuItem['id'] == $currentItemDetails['level2']) {
        return ' class="selected"';
    }
    
    return '';
    
}

function getMenuUrl($menuItem) {
    
    $url = '#';
    
    if (!empty($menuItem['module']) && !empty($menuItem['action'])) {
        $url = url_for($menuItem['module'] . '/'. $menuItem['action']);
        $url = empty($menuItem['urlExtras'])?$url:$url . $menuItem['urlExtras'];
    }
    
    return $url;
    
}

function getHtmlId($menuItem) {
    
    $id = '';
    
    if (!empty($menuItem['action'])) {
        $id = 'menu_' . $menuItem['module'] . '_' . $menuItem['action'];
    } else {
        
        $module             = '';
        $firstSubMenuItem   = $menuItem['subMenuItems'][0];
        $module             = $firstSubMenuItem['module'] . '_';
        
        $id = 'menu_' . $module . str_replace(' ', '', $menuItem['menuTitle']);
        
    }
    
    return $id;
    
}

?>

<div class="menu">
    
    <ul>
        
        <?php foreach ($menuItemArray as $firstLevelItem) : ?>
            
        <li<?php echo getListItemClass($firstLevelItem, $currentItemDetails); ?>><a href="<?php echo getMenuUrl($firstLevelItem); ?>" id="<?php echo getHtmlId($firstLevelItem); ?>" class="firstLevelMenu"><b><?php echo __($firstLevelItem['menuTitle']) ?></b></a>
            
            <ul>
            <?php if (count($firstLevelItem['subMenuItems']) > 0) : ?>            
                    
                    <?php foreach ($firstLevelItem['subMenuItems'] as $secondLevelItem) : ?>
                    
                        <li<?php echo getListItemClass($secondLevelItem, $currentItemDetails); ?>><a href="<?php echo getMenuUrl($secondLevelItem); ?>" id="<?php echo getHtmlId($secondLevelItem); ?>"<?php echo getSubMenuIndication($secondLevelItem); ?>><?php echo __($secondLevelItem['menuTitle']) ?></a>
                        
                        <?php if (count($secondLevelItem['subMenuItems']) > 0) : ?>
                        
                            <ul>
                                
                                <?php foreach ($secondLevelItem['subMenuItems'] as $thirdLevelItem) : ?>
                                
                                    <li><a href="<?php echo getMenuUrl($thirdLevelItem); ?>" id="<?php echo getHtmlId($thirdLevelItem); ?>"><?php echo __($thirdLevelItem['menuTitle']) ?></a></li>
                                
                                <?php endforeach; ?>
                                
                            </ul> <!-- third level -->
                            
                        <?php endif; ?>
                            
                        </li>   
                    
                    <?php endforeach; ?>
            <?php else: 
                // Empty li to add an orange bar and maintain uniform look.
            ?>                        
                        <li></li>
            <?php endif; ?>
                
                </ul> <!-- second level -->                        
            </li>
            
        <?php endforeach; ?>
            
    </ul> <!-- first level -->
    
</div> <!-- menu -->