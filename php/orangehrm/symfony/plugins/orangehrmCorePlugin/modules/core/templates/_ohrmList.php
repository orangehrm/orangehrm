
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

                <?php foreach ($columns as $columnLabel => $getterMethod): ?>
                    <th><?php echo __($columnLabel); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <tr class="odd">
                <td><input type="checkbox" id="ohrmList_chkSelectRecord_NAT001" value="NAT001" name="chkSelectRow[]" /></td>		<td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT001">NAT001</a></td>
                <td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT001">American</a></td>
                <td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT001">999</a></td>
            </tr>
            <tr class="even">
                <td><input type="checkbox" id="ohrmList_chkSelectRecord_NAT002" value="NAT002" name="chkSelectRow[]" /></td>		<td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT002">NAT002</a></td>
                <td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT002">British</a></td>
                <td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT002">999</a></td>
            </tr>
            <tr class="odd">
                <td><input type="checkbox" id="ohrmList_chkSelectRecord_NAT003" value="NAT003" name="chkSelectRow[]" /></td>		<td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT003">NAT003</a></td>
                <td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT003">Chinese</a></td>
                <td><a href="/people_manager/web/index.php/nus_admin/viewNationality/id/NAT003">999</a></td>
            </tr>
        </tbody>
    </table>
</div>
