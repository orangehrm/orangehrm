<?php

class updateAddonAction extends sfAction
{
    function execute($request)
    {
        $accessToken = $this->getUser()->getAttribute('marketplace.accessToken');
        $baseUrl = $this->getUser()->getAttribute('marketplace.baseUrl');
        $updatePendingAddons = json_decode($this->getUser()->getAttribute('marketplace.addons'), true);
        $addonId = $request->getParameter('addonId');
        $url = $baseUrl . $updatePendingAddons[$addonId]['url'];

        $fpHeader = fopen('php://temp', 'w+');

        $tempName = tempnam(ROOT_PATH . '/symfony/cache', 'mp_addon_');
        $fp = fopen ($tempName, 'w+');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken"
        ]);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_WRITEHEADER, $fpHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        rewind($fpHeader);
        $headers = stream_get_contents($fpHeader);

        preg_match('/Content-Disposition: attachment; filename=(.*)/', $headers, $matches);
        $fName = $matches[1];
        $addonFilePath = ROOT_PATH . "/symfony/cache/$fName";
        copy($tempName, $addonFilePath);

        preg_match('/ETag: "(.*)"/', $headers, $matches);
        $eTag = $matches[1];
        $checksum = bin2hex(base64_decode($eTag));
        if (strcasecmp(hash_file('sha256', $addonFilePath), $checksum) === 0) {
            $zip = new ZipArchive();
            if ($zip->open($addonFilePath) === true) {
                $pluginName = $zip->getNameIndex(0);
                $pluginsDir = ROOT_PATH . "/symfony/plugins";
                if (is_writable($pluginsDir)) {
                    $zip->extractTo($pluginsDir);
                    $zip->close();
                    echo '1';
                    exit;
                }
            }
        }

        return sfView::NONE;
    }
}