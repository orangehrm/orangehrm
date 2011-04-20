
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo $title; ?></h2></div>

    <table style="border-collapse: collapse; width: 100%;  text-align: left;" class="data-table">
        <thead>
            <tr>
                <?php
                if ($hasSelectableRows) {
                    $selectAllCheckobxTag = tag('input', array(
                                'type' => 'checkbox',
                                'id' => 'ohrmList_chkSelectAll',
                                'name' => 'chkSelectAll'
                            ));
                    echo content_tag('th', $selectAllCheckobxTag);
                }
                ?>

                <?php
                foreach ($columns as $header) {
                    if ($header->isSortable()) {
                        $nextSortOrder = ($header->getSortOrder() === 'ASC') ? 'DESC' : 'ASC';
                        $sortOrderStyle = ($header->getSortOrder() == '') ? 'null' : $header->getSortOrder();

                        $actionName = sfContext::getInstance()->getActionName();

                        $sortUrl = 'index.php/' .
                                sfContext::getInstance()->getModuleName() . '/' .
                                $actionName . '/' .
                                'sortField/' . 'field' . '/' .
                                'sortOrder/' . $nextSortOrder;

                        $request = sfContext::getInstance()->getRequest();
                        if ($request->isMethod('post') && $request->getParameter('cmbSearchBy', null) !== null) {
                            $searchBy = $request->getParameter('cmbSearchBy');
                            $searchFor = $request->getParameter('txtSearchFor');
                            $sortUrl .= '/isSearch/yes' .
                                    '/searchBy/' . $request->getParameter('cmbSearchBy') .
                                    '/searchFor/' . $request->getParameter('txtSearchFor');
                        }

                        $headerHtml = content_tag('a', __($header->getName()), array(
                                    'href' => public_path($sortUrl),
                                    'class' => $sortOrderStyle,
                                ));
                    }
                ?>
                    <th><?php echo $headerHtml; ?></th>
                <?php } ?>
            </tr>
        </thead>

        <tbody>
            <?php
                if ($data->count() > 0) {
                    $rowCssClass = 'even';

                    foreach ($data as $object) {
                        $idValue = 0;
                        $rowCssClass = ($rowCssClass === 'odd') ? 'even' : 'odd';
            ?>
                        <tr class="<?php echo $rowCssClass; ?>">
                <?php
                        if ($hasSelectableRows) {
                            $selectCheckobxTag = (/*in_array($idValue, $unselectableRowIds)*/ false) ? '&nbsp;' : tag('input', array(
                                        'type' => 'checkbox',
                                        'id' => "ohrmList_chkSelectRecord_{$idValue}",
                                        'value' => $idValue,
                                        'name' => 'chkSelectRow[]'
                                    ));
                            echo content_tag('td', $selectCheckobxTag);
                        }

                        foreach ($columns as $header) {
                            $cellHtml = '';
                            $getter = $header->getElementProperty();
                            $cellHtml = $object->$getter();

                            if ($header->getElementType() === 'link') {
                                $cellHtml = content_tag('a', $cellHtml, array(
                                    'href' => '#',
                                ));
                            }
                ?>
                            <td><?php echo $cellHtml; ?></td>
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
</div>
