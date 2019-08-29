<?php
use_javascript('jquery.js');
$error_found = false;
$notWritableAddons = $sf_data->getRaw('notWritableAddons');

if (count($notCopiedPlugins) > 0) : ?>
    <?php $error_found = true; ?>
    <h2>Addon Check</h2>
    <p>Following plugins are not copied. Please copy the plugins from previous codebase. You may find plugins under <strong>&lt;codebase_path&gt;/symfony/plugins</strong> directory. This step has to be completed manually.</p>
    <ul>
        <?php foreach ($notCopiedPlugins as $notCopiedPlugin) : ?>
            <li><?php echo $notCopiedPlugin;?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <h2>Addon Check</h2>
    <?php if (count($notSupportedPlugins) > 0) : ?>
        <p>Following addons are not supported in the newer version of OrangeHRM.</p>
        <ul>
            <?php foreach ($notSupportedPlugins as $notSupportedPlugin) : ?>
                <li><?php echo $notSupportedPlugin;?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if (count($updatePendingAddons) > 0) : ?>
        <p>Following addons should be updated to be compatible with the newer version of OrangeHRM.</p>
        <table border="1">
            <thead>
                <tr>
                    <th>Addon</th>
                    <th>Current Version</th>
                    <th>New Version</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($updatePendingAddons as $addonId => $addonDetails) : ?>
                <tr>
                    <td style="padding: 2px 5px"><?php echo $addonDetails['title'];?></td>
                    <td style="padding: 2px 5px"><?php echo $addonDetails['currentVersion'];?></td>
                    <td style="padding: 2px 5px"><?php echo $addonDetails['newVersion'];?></td>
                    <td style="padding: 2px 5px; color: <?php echo isset($notWritableAddons[$addonId]) ? 'red': 'orange';?>" id="addon_status_<?php echo $addonId;?>"><?php
                        echo isset($notWritableAddons[$addonId]) ? ('No write permission for symfony/plugins/' . $notWritableAddons[$addonId] . ' directory') : 'Download Pending';
                    ?></td>
                    <td style="padding: 2px 5px">
                        <button onclick="downloadAddon(event, <?php echo $addonId;?>)" style="padding: 2px 5px; margin: 2px 5px" <?php if (isset($notWritableAddons[$addonId])) {echo 'disabled';} ?>>Download</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if (count($notSupportedPlugins) === 0 && count($updatePendingAddons) === 0) :?>
        <p>All installed addons are up to date</p>
    <?php endif;?>
<?php endif; ?>

<br />
<form action="<?php echo url_for('upgrade/executePluginCheck');?>" id="pluginCheckForm" name="pluginCheckForm" method="post">
    <?php echo $form->renderHiddenFields();?>
    <input class="button" id="recheckButton" type="button" name="Re-check" value="Re-check" tabindex="3">
    <input class="button" id="nextButton" type="submit" value="Proceed" <?php echo  ($error_found) ? 'disabled' : '' ?> tabindex="2">
</form>

<script type="application/javascript">
    var downloadAddonUrl = '<?php echo url_for("upgrade/updateAddon")?>';
    var isDownloading = false;
    var downloadCount = 0;

    $('#pluginCheckForm').submit(function() {
        if (isDownloading) {
            alert('Download in progress. Please wait till download completes.');
            return false;
        }
    });

    $('#recheckButton').click(function() {
        if (isDownloading) {
            alert('Download in progress. Please wait till download completes.');
            return false;
        }
        location.reload();
    });

    function downloadAddon(event, addonId) {
        event.currentTarget.disabled = 1;
        $('#addon_status_' + addonId).text('Downloading...');
        isDownloading = true;
        downloadCount++;
        $.get(downloadAddonUrl + '?addonId=' + addonId, function(res) {
            if (res === '1') {
                $('#addon_status_' + addonId).text('Downloaded').css('color', 'green');
            } else {
                $('#addon_status_' + addonId).text('Download failed').css('color', 'red');
            }
            downloadCount--;
            if (downloadCount === 0) {
                isDownloading = false;
            }
        });
    }
</script>
