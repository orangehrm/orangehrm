
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo $title; ?></h2></div>

    <form method="<?php echo $formMethod; ?>" action="<?php echo $formAction; ?>">
        <div class="actionbar">
            <div class="actionbuttons">
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

        <?php if ($pager->haveToPaginate()) { ?>
        <div class="navigationHearder">
            <div class="pagingbar"><?php include_partial('global/paging_links_js', array('pager' => $pager));?></div>
            <br class="clear" />
        </div>
        <?php } ?>

        <table style="border-collapse: collapse; width: 100%; text-align: left;" class="data-table">
            <colgroup>
                <?php if ($hasSelectableRows) { ?>
                <col width="50" />
                <?php } ?>
                <?php foreach ($columns as $header) { ?>
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

                            $sortUrl = public_path("index.php/{$currentModule}/{$currentAction}/sortField/{$header->getSortField()}/sortOrder/{$nextSortOrder}", true);

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
                        <th><?php echo $headerCell->__toString(); ?></th>
                    <?php } ?>
                </tr>
            </thead>

            <tbody>
                <?php
                    if ($data->count() > 0) {
                        $rowCssClass = 'even';

                        foreach ($data as $object) {
                            $idValue = $object->$idValueGetter();
                            $rowCssClass = ($rowCssClass === 'odd') ? 'even' : 'odd';
                ?>
                            <tr class="<?php echo $rowCssClass; ?>">
                    <?php
                            if ($hasSelectableRows) {
                                if (false) { /* in_array($idValue, $unselectableRowIds) */
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
                    ?>
                                <td><?php echo $cell->__toString(); ?></td>
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
        </table>
    </form>
</div>

<?php echo javascript_include_tag('../orangehrmCorePlugin/js/_ohrmList.js'); ?>

<?php
    $asseestPath = '../orangehrmCorePlugin/';
    foreach ($assets as $asset) {
        $assetType = substr($asset, strrpos($asset, '.') + 1);

        if ($assetType == 'js') {
            echo javascript_include_tag($asseestPath . 'js/' . $asset);
        } elseif ($assetType == 'css') {
            echo stylesheet_tag($asseestPath . 'css/' . $asset);
        } else {
            echo $assetType;
        }
    }
?>

<script type="text/javascript">

    $(document).ready(function() {
        ohrmList_init();

<?php
    foreach ($buttons as $key => $buttonProperties) {
        $button = new Button();
        $button->setProperties($buttonProperties);
        $button->setIdentifier($key);
        if (!empty($buttonProperties['function'])) {
            echo "\t\$('#{$button->getId()}').click({$buttonProperties['function']});", "\n";
        }
    }
?>
    });

</script>
