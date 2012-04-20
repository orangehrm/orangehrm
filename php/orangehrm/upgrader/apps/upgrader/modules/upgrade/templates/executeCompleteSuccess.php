<?php use_javascript('jquery.js') ?>
<?php use_javascript('executeCompleteSuccess.js') ?>
<div>
    <h2>Successfully upgraded</h2>
    <p>Have to chage this.</p>
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
