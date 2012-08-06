<?php use_javascript('jquery.js') ?>
<?php use_javascript('executeCompleteSuccess.js') ?>
<div>
    <h2>Congratulations!</h2>
    <p>You have successfully upgraded to OrangeHRM <?php echo $newVersion; ?>. Make sure you go through following steps before start using the system.</p>
    <ul>
        <li>
            If notes were displayed under <strong>Version Info</strong>, make sure you understand them thoroughly. You can find the notes at <strong>upgrader/log/notes.log</strong> in your OrangeHRM root folder.  
        </li>
        <li>
            After going through the manual steps mentioned in the notes (if there are any), do a full round of data verification to see whether data from previous version has been imported properly.
        </li>        
    </ul>
</div>
<div>
    <form action="<?php echo url_for('');?>" method="post" name="completionForm" id="completionForm">
        <table>
            <tbody>
                <tr>
                    <td>
                        <input type="submit" id="sumbitButton" name="sumbitButton" value="<?php echo __('Proceed')?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<script type="text/javascript">
    var mainAppUrl = '<?php echo $mainAppUrl;?>';
</script>
