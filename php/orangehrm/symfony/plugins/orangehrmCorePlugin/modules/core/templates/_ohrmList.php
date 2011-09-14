<?php echo stylesheet_tag('../orangehrmCorePlugin/css/_ohrmList.css'); ?>
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
        <div class="actionbar">
            <div class="formbuttons">
        <?php
        foreach ($buttons as $key => $buttonProperties) {
            $button = new Button();
            $button->setProperties($buttonProperties);
            $button->setIdentifier($key);
            echo $button->__toString(), "\n";
        }
        ?>
    </div>

    <br class="clear" />

   
</div>
 <?php } ?>
<?php
}

function printAssetPaths($assets, $assestsPath = '') {

    if (count($assets) > 0) {

        foreach ($assets as $key => $asset) {
            $assetType = substr($asset, strrpos($asset, '.') + 1);

            if ($assestsPath == '') {
                echo javascript_include_tag($asset);
            } elseif ($assetType == 'js') {
                echo javascript_include_tag($assestsPath . 'js/' . $asset);
            } elseif ($assetType == 'css') {
                echo stylesheet_tag($assestsPath . 'css/' . $asset);
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
?>
<div class="outerbox" style="padding-right: 15px; width: <?php echo $outboxWidth; ?>">
<?php if (!empty($title)) { ?>
        <div class="mainHeading"><h2><?php echo __($title); ?></h2></div>

<?php if ($partial != null): ?>
        <div style="padding-left: 5px; padding-top: 5px;">
<?php
        include_partial($partial, $sf_data->getRaw('params'));
?>
    </div>
<?php endif; ?>

<?php } ?>

    <form method="<?php echo $formMethod; ?>" action="<?php echo public_path($formAction); ?>" id="frmList_ohrmListComponent">
<?php
    if (count($buttons) > 0) {
        renderActionBar($buttons, $buttonsPosition === ohrmListConfigurationFactory::BEFORE_TABLE);
        echo "<br class=\"clear\" />";
    }

    if (isset($extraButtons)) {
        renderActionBar($extraButtons);
        echo "<br class=\"clear\" />";
    }

    include_component('core', 'ohrmPluginPannel', array('location' => 'list-component-before-table-action-bar'));
?>
        <div id="helpText"class="helpText"></div>
        <?php if ($pager->haveToPaginate()) {
 ?>
            <div class="navigationHearder">
                <div class="pagingbar"><?php include_partial('global/paging_links_js', array('pager' => $pager)); ?></div>
                <br class="clear" />
            </div>
<?php } ?>

        <table style="border-collapse: collapse; width: <?php echo $tableWidth; ?>; text-align: left;" class="data-table">
            <colgroup align="right">
<?php if ($hasSelectableRows) { ?>
                    <col width="50" />
<?php } ?>
                <?php foreach ($columns as $header) {
 ?>
                    <col width="<?php echo $header->getWidth(); ?>" />
                <?php } ?>
            </colgroup>
            <thead>
                <tr>
                    <?php
                    if ($hasSelectableRows) {
                        $selectAllCheckobx = new Checkbox();
                        $selectAllCheckobx->setProperties(array(
                            'id' => 'ohrmList_chkSelectAll',
                            'name' => 'chkSelectAll'
                        ));
                        $selectAllCheckobx->setIdentifier('Select_All');
                        echo content_tag('th', $selectAllCheckobx->__toString());
                    }
                    ?>

                    <?php
                    foreach ($columns as $header) {
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
                    ?>
                        <th style="text-align: <?php echo $header->getTextAlignmentStyleForHeader(); ?>"><?php echo $headerCell->__toString(); ?></th>
                    <?php } ?>
                </tr>
            </thead>

            <tbody>
                <?php
                    if (is_object($data) && $data->count() > 0) {
                        $rowCssClass = 'even';

                        foreach ($data as $object) {
                            $idValue = ($object instanceof sfOutputEscaperArrayDecorator) ? $object[$idValueGetter] : $object->$idValueGetter();
                            $rowCssClass = ($rowCssClass === 'odd') ? 'even' : 'odd';
                ?>
                            <tr class="<?php echo $rowCssClass; ?>">
                    <?php
                            if ($hasSelectableRows) {
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

                                if ($hasSummary && $header->getName() == $summary['summaryField']) {
                                    ohrmListSummaryHelper::collectValue($cell->toValue(), $summary['summaryFunction']);
                                }
                    ?>
                                <td class="<?php echo $header->getTextAlignmentStyle(); ?>"><?php echo $cell->__toString(); ?></td>
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
                            <td colspan="<?php echo $colspan; ?>"><?php echo __('No records to display'); ?></td>
                        </tr>
                <?php
                    }
                ?>
                </tbody>
            <?php if ($hasSummary) {
 ?>
                        <tfoot>
                            <tr>
                    <?php
                        $firstHeader = true;
                        foreach ($columns as $header) {
                            if ($header->getName() == $summary['summaryField']) {
                                $aggregateValue = ohrmListSummaryHelper::getAggregateValue($summary['summaryFunction'], $summary['summaryFieldDecimals']);
                                if ($firstHeader) {
                                    $aggregateValue = $summary['summaryLabel'] . ':' . $aggregateValue;
                                    $firstHeader = false;
                                }
                                //echo tag('td', $aggregateValue);
                                echo "<td class='right'>" . $aggregateValue . '</td>';
                            } else {
                                $tdValue = '&nbsp;';
                                if ($firstHeader) {
                                    $tdValue = $summary['summaryLabel'];
                                    $firstHeader = false;
                                }
                                //echo tag('td', $tdValue);
                                echo "<td>" . $tdValue . '</td>';
                            }
                        }
                    ?>
                    </tr>
                </tfoot>
<?php } ?>
                </table>

<?php renderActionBar($buttons, $buttonsPosition === ohrmListConfigurationFactory::AFTER_TABLE); ?>
                    <br class="clear" />
                </form>

            </div>

<?php echo javascript_include_tag('../orangehrmCorePlugin/js/_ohrmList.js'); ?>

<?php
                    $assestsPath = "../{$pluginName}/";

                    if (isset($assets)) {
                        printAssetPaths($assets, $assestsPath);
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

</script>
