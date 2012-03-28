<div>
    <form action="<?php echo url_for('upgrade/displayVersionInfo');?>" name="versionInfoForm" method="post">
        <table>
            <tbody>
                <tr>
                    <td>
                        You are going to upgrade to OrangeHRM <?php echo $newVersion; ?>
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        
                    </td>
                    <td>
                        <input type="submit" value="Proceed"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
