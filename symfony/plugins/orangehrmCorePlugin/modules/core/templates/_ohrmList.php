<?php echo use_stylesheet(plugin_web_path('orangehrmCorePlugin', 'css/_ohrmList.css')); ?>
<?php
if ($tableWidth == 'auto') {
    $outboxWidth = 0;
    foreach ($columns as $header) {
        $outboxWidth = $outboxWidth + $header->getWidth();
    }
    $outboxWidth .= 'px';
} else {
    $outboxWidth = 'auto';
}

function renderActionBar($buttons, $condition = true) {
    if ($condition && count($buttons) > 0) {
?>

        <?php
        foreach ($buttons as $key => $buttonProperties) {
            $button = new Button();
            $button->setProperties($buttonProperties);
            $button->setIdentifier($key);
            echo $button->__toString(), "\n";
        }
        ?>

 <?php } ?>
<?php
}

function printAssetPaths($assets, $plugin = '') {

    if (count($assets) > 0) {

        foreach ($assets as $key => $asset) {
            $assetType = substr($asset, strrpos($asset, '.') + 1);

            if ($plugin == '') {
                echo javascript_include_tag($asset);
            } elseif ($assetType == 'js') {
                echo javascript_include_tag(plugin_web_path($plugin, 'js/' . $asset));
            } elseif ($assetType == 'css') {
                echo stylesheet_tag(plugin_web_path($plugin, 'css/' . $asset));
            } else {
                echo $assetType;
            }
        }
    }
}

function printButtonEventBindings($buttons) {
    foreach ($buttons as $key => $buttonProperties) {
        $button = new Button();
        $button->setProperties($buttonProperties);
        $button->setIdentifier($key);
        if (!empty($buttonProperties['function'])) {
            echo "\t\$('#{$button->getId()}').click({$buttonProperties['function']});", "\n";
        }
    }
}

function getHeaderCellClassHtml($isSortable, $sortOrder) {
    
    if ($isSortable) {
        
        $headerCellClassHtml = ' class="header"';
    
        if ($sortOrder == 'ASC') {
            $headerCellClassHtml = ' class="header headerSortUp"';
        } elseif ($sortOrder == 'DESC') {
            $headerCellClassHtml = ' class="header headerSortDown"';
        }
        
        return $headerCellClassHtml;
    
    }
    
    return '';
    
}                            

?>
<div class="box<?php echo empty($title)?' noHeader':''; ?>" id="search-results">
    
    <?php if (!empty($title)) { ?>
        <div class="head"><h1><?php echo __($title); ?></h1></div>
    <?php } ?>
    
    <div class="inner">
    
<?php if (!empty($title)) { ?>
        
        
<?php if ($partial != null): ?>
        <div style="padding-left: 5px; padding-top: 5px;">
<?php
        include_partial($partial, $sf_data->getRaw('params'));
?>
    </div>
<?php endif; ?>

<?php } ?>

    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'widget-panel-1')) ?>
    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'widget-panel-2')) ?>
        
    <?php
        if ($formAction == '#') {
            $formUrl = '#';
        } else if (strpos($formAction, 'index.php') === FALSE) {
            $formUrl = url_for($formAction);
        } else {
            $formUrl = public_path($formAction);
        }
    ?>
        
    <form method="<?php echo $formMethod; ?>" action="<?php echo $formUrl; ?>" name="frmList_ohrmListComponent" id="frmList_ohrmListComponent">
        <?php echo $listForm->render() ?>
<?php if ((count($buttons) > 0 && $buttonsPosition === ohrmListConfigurationFactory::BEFORE_TABLE)  || isset($extraButtons) || $pager->haveToPaginate()) : ?>        
 <div class="top">          
        
<?php
    if (count($buttons) > 0) {
        renderActionBar($buttons, $buttonsPosition === ohrmListConfigurationFactory::BEFORE_TABLE);
        //echo "<br class=\"clear\" />";
    }

    if (isset($extraButtons)) {
        renderActionBar($extraButtons);
        //echo "<br class=\"clear\" />";
    }

    include_component('core', 'ohrmPluginPannel', array('location' => 'list-component-before-table-action-bar'));
    
    if ($pager->haveToPaginate()) {
        include_partial('global/paging_links_js', array('pager' => $pager, 'location' => 'top'));
    }    
    
?>
</div> <!-- top --> 

<?php endif; ?>         

        <?php include_partial('global/flash_messages'); ?>
         
        <div id="helpText" class="helpText"></div>

        <div id="scrollWrapper">
            <div id="scrollContainer">
            </div>
        </div>        
        <div id="tableWrapper">
        <table class="table hover" id="resultTable">
            
                    <?php
                    
                    $headerRow1 = '';
                    $headerRow2 = '';
                    
                    if ($hasSelectableRows) {
                        $selectAllCheckbox = new Checkbox();
                        $selectAllCheckbox->setProperties(array(
                            'id' => 'ohrmList_chkSelectAll',
                            'name' => 'chkSelectAll'
                        ));
                        
                        $selectAllCheckbox->setIdentifier('Select_All');                                            
                        $selectAllRowspan = $showGroupHeaders ? 2 : 1;     
                        
                        $headerRow1 .= content_tag('th', $selectAllCheckbox->__toString(),
                                                   array('rowspan' => $selectAllRowspan, 'class' => 'checkbox-col')) . "\n";
                    }


                    foreach ($headerGroups as $group) {
                        
                        $rowspan = 1;

                        if ($showGroupHeaders) {
                            if ($group->showHeader()) {
                                
                                $headerCell = new HeaderCell();
                                $headerCell->setProperties(array(
                                    'label' => __($group->getName()),
                                        )
                                );
                                
                                $groupColspan = $group->getHeaderCount();
                                $headerRow1 .= content_tag('th', $headerCell->__toString(),
                                               array('style' => 'text-align: center',
                                                     'colspan' => $groupColspan)) . "\n";
                                

                            } else {
                                
                                // If we are displaying group headers and this is a
                                // group without a header, set rowspan = 2.                                
                                $rowspan = 2;
                            }
                        }
                        
                        foreach ($group->getHeaders() as $header) {
                            
                            $sortOrderStyle = 'null';
                            
                            if ($header->isSortable()) {
                                $nextSortOrder = ($currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
                                $nextSortOrder = ($currentSortField == $header->getSortField()) ? $nextSortOrder : 'ASC';

                                $sortOrderStyle = ($currentSortOrder == '') ? 'null' : $currentSortOrder;
                                $sortOrderStyle = ($currentSortField == $header->getSortField()) ? $sortOrderStyle : 'null';

                                $currentModule = sfContext::getInstance()->getModuleName();
                                $currentAction = sfContext::getInstance()->getActionName();

                                $sortUrl = public_path("index.php/{$currentModule}/{$currentAction}?sortField={$header->getSortField()}&sortOrder={$nextSortOrder}", true);

                                $headerCell = new SortableHeaderCell();
                                $headerCell->setProperties(array(
                                    'label' => __($header->getName()),
                                    'sortUrl' => $sortUrl,
                                    'currentSortOrder' => $sortOrderStyle,
                                ));
                            } else {
                                $headerCell = new HeaderCell();
                                $headerCell->setProperties(array(
                                    'label' => __($header->getName()),
                                        )
                                );
                            }
                            
                            $headerCellClassHtml = getHeaderCellClassHtml($header->isSortable(), $sortOrderStyle);
                            $headerCellHtml = '<th rowspan="' . $rowspan . '" style="width:' . $header->getWidth() . '"' . $headerCellClassHtml . '>' . $headerCell->__toString() . "</th>\n";                            
                            
                            if ($group->showHeader()) {
                                $headerRow2 .= $headerCellHtml;
                            } else {
                                $headerRow1 .= $headerCellHtml;
                            }
                        } 
                    }
                    ?>
            <thead>
                <tr><?php echo $headerRow1;?></tr>            
                <?php if (!empty($headerRow2)) { ?>
                <tr><?php echo $headerRow2;?></tr>
                <?php } ?>
            </thead>

            <tbody>
                <?php
                    if (is_object($data) && $data->count() > 0) {
                        $rowCssClass = 'even';

                        foreach ($data as $object) {
                            
                            $rowCssClass = ($rowCssClass === 'odd') ? 'even' : 'odd';
                ?>
                            <tr class="<?php echo $rowCssClass; ?>">
                    <?php
                            if ($hasSelectableRows) {
                                $idValue = ($object instanceof sfOutputEscaperArrayDecorator) ? $object[$idValueGetter] : $object->$idValueGetter();
                                
                                if (in_array($idValue, $unselectableRowIds->getRawValue())) {
                                    $selectCellHtml = '&nbsp;';
                                } else {
                                    $selectCheckobx = new Checkbox();
                                    $selectCheckobx->setProperties(array(
                                        'id' => "ohrmList_chkSelectRecord_{$idValue}",
                                        'value' => $idValue,
                                        'name' => 'chkSelectRow[]'
                                    ));

                                    $selectCellHtml = $selectCheckobx->__toString();
                                }

                                echo content_tag('td', $selectCellHtml);
                            }

                            foreach ($columns as $header) {
                                $cellHtml = '';
                                $cellClass = ucfirst($header->getElementType()) . 'Cell';
                                $properties = $header->getElementProperty();

                                $cell = new $cellClass;
                                $cell->setProperties($properties);
                                $cell->setDataObject($object);
                                $cell->setHeader($header);

                                if ($hasSummary && $header->getName() == $summary['summaryField']) {
                                    ohrmListSummaryHelper::collectValue($cell->toValue(), $summary['summaryFunction']);
                                }
                                
                                $verticalStyle = '';
                                if (isset($properties['isValueList']) && $properties['isValueList']) {
                                    $verticalStyle = "style='vertical-align:top;'";
                                }
                    ?>
                                <td class="<?php echo $header->getTextAlignmentStyle(); ?>" <?php echo $verticalStyle;?>><?php echo $cell->__toString(); ?></td>
                    <?php
                            }
                    ?>
                        </tr>
                <?php
                        }
                    } else {
                        $colspan = count($columns);
                        if ($hasSelectableRows) {
                            $colspan++;
                        }
                ?>
                        <tr>
                            <td colspan="<?php echo $colspan; ?>"><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                        </tr>
                <?php
                    }
                ?>
                </tbody>
            <?php if ($hasSummary && $data->count() > 0) {
 ?>
                        <tfoot>
                            <tr class="total">
                    <?php
                        if ($hasSelectableRows) {
                            echo "<td>&nbsp;</td>";
                        }                      
                        $firstHeader = true;
                        foreach ($columns as $header) {
                            if ($header->getName() == $summary['summaryField']) {
                                $aggregateValue = ohrmListSummaryHelper::getAggregateValue($summary['summaryFunction'], $summary['summaryFieldDecimals']);
                                if ($firstHeader) {
                                    $aggregateValue = $summary['summaryLabel'] . ':' . $aggregateValue;
                                    $firstHeader = false;
                                }
                                echo "<td class='right'>" . $aggregateValue . '</td>';
                            } else {
                                $tdValue = '&nbsp;';
                                
                                if ($firstHeader) {
                                    $tdValue = $summary['summaryLabel'];
                                    $firstHeader = false;                                    
                                }
                                echo "<td>" . __($tdValue) . '</td>';
                            }
                        }
                    ?>
                    </tr>
                </tfoot>
<?php } ?>
                </table>
        </div> <!-- tableWrapper -->
        <?php if ((count($buttons) > 0 && $buttonsPosition === ohrmListConfigurationFactory::AFTER_TABLE) || $pager->haveToPaginate()) : ?>
            <div class="bottom">
                <?php renderActionBar($buttons, $buttonsPosition === ohrmListConfigurationFactory::AFTER_TABLE); ?>
                
                <?php 
                    if ($pager->haveToPaginate()) {
                        include_partial('global/paging_links_js', array('pager' => $pager, 'location' => 'bottom'));
                    }
                ?>
            </div>
        <?php endif;?>
        </form> <!-- frmList_ohrmListComponent --> 
                
<?php if ($footerPartial != null): ?>
        <div style="padding-left: 5px; padding-top: 5px;">
<?php
        include_partial($footerPartial, $sf_data->getRaw('params'));
?>
    </div>
<?php endif; ?>
        
    </div> <!-- inner -->
        
</div> <!-- search-results -->

<?php echo javascript_include_tag(plugin_web_path('orangehrmCorePlugin', 'js/_ohrmList.js')); ?>

<?php

                    if (isset($assets)) {
                        printAssetPaths($assets, $pluginName);
                    }

                    if (isset($extraAssets)) {
                        printAssetPaths($extraAssets);
                    }
?>

                    <script type="text/javascript">

                        var rootPath = '<?php echo public_path('/'); ?>';

                        $(document).ready(function() {
                            ohrmList_init();

<?php
                    foreach ($jsInitMethods as $methodName) {
                        echo "\t{$methodName}();", "\n";
                    }

                    if (isset($buttons)) {
                        printButtonEventBindings($buttons);
                    }

                    if (isset($extraButtons)) {
                        printButtonEventBindings($extraButtons);
                    }
?>
                        });
                                              
<?php if (isset($enableTopScrolling) && $enableTopScrolling === true) : ?>

    $("#scrollContainer").css('width', $("#resultTable").width() + 'px');

    $("#scrollWrapper").scroll(function(){
        $("#tableWrapper")
            .scrollLeft($("#scrollWrapper").scrollLeft());
    });
    $("#tableWrapper").scroll(function(){
        $("#scrollWrapper")
            .scrollLeft($("#tableWrapper").scrollLeft());
    });
        
<?php endif; ?>

</script>
