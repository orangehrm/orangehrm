<?php

class executePluginCheckAction extends sfAction
{
    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'pluginCheck');
    }

    function execute($request)
    {
        $this->form = new SystemCheck();

        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('systemCheck'));
            if ($this->form->isValid()) {
                $this->getRequest()->setParameter('submitBy', 'pluginCheck');
                $this->forward('upgrade','index');
            }
        }

        $dbInfo = $this->getUser()->getAttribute('dbInfo');
        $upgradeUtility = new UpgradeUtility();
        $upgradeUtility->getDbConnection(
            $dbInfo['host'],
            $dbInfo['username'],
            $dbInfo['password'],
            $dbInfo['database'],
            $dbInfo['port']
        );
        $installedAddons = $upgradeUtility->getInstalledAddons();
        $addonByName = array_column($installedAddons, null, 'title');
        $this->getUser()->setAttribute('hasPlugins', count($installedAddons) > 0);
        $this->getUser()->setAttribute('includesThemePlugin', in_array('Corporate Branding', array_keys($addonByName)));
        if (count($installedAddons) === 0 || $this->getUser()->getAttribute('plugins.ready')) {
            $this->redirect('upgrade/selectVersion');
        }
        $pluginsDir = ROOT_PATH . DIRECTORY_SEPARATOR . 'symfony' . DIRECTORY_SEPARATOR . 'plugins';
        $notCopiedPlugins = [];
        $versions = [];
        foreach ($installedAddons as $installedAddon) {
            if (!is_dir($pluginsDir . DIRECTORY_SEPARATOR . $installedAddon['plugin_name'])) {
                $notCopiedPlugins[] = $installedAddon['plugin_name'];
            } else {
                $filePath = $pluginsDir . DIRECTORY_SEPARATOR . $installedAddon['plugin_name'] . DIRECTORY_SEPARATOR .
                    'config' . DIRECTORY_SEPARATOR . 'app.yml';
                $content = sfYaml::load($filePath);
                $versions[$installedAddon['title']] = $content['all'][$installedAddon['plugin_name']]['version'];
            }
        }

        $this->notCopiedPlugins = $notCopiedPlugins;

        if (count($notCopiedPlugins) === 0) {
            // check addons are up to date
            $targetVersion = $this->getOhrmVersion();
            $params = [
                'product' => 'opensource',
                'version' => $targetVersion,
                'addons' => array_keys($versions)
            ];
            $marketplaceUrl = $upgradeUtility->getMarketplaceBaseUrl();
            $addonsUrl = "$marketplaceUrl/api/v1/addon/latest?" . http_build_query($params);
            $ch = curl_init($addonsUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $doc = json_decode($response, true);

            $updatePendingAddons = [];
            $notSupportedPlugins = [];
            foreach ($doc as $addon => $latestVersion) {
                if ($latestVersion == -1) {
                    $notSupportedPlugins[] = $addon;
                } else if (version_compare($latestVersion['version'], $versions[$addon]) > 0) {
                    $updatePendingAddons[$addonByName[$addon]['addon_id']] = [
                        'title' => $addon,
                        'currentVersion' => $versions[$addon],
                        'newVersion' => $latestVersion['version'],
                        'url' => $latestVersion['url']
                    ];
                }
            }
            $this->notSupportedPlugins = $notSupportedPlugins;
            $this->updatePendingAddons = $updatePendingAddons;
            $this->notWritableAddons = [];

            foreach (array_column($updatePendingAddons, 'title') as $addonName) {
                $addon = $addonByName[$addonName];
                $pluginDir = $pluginsDir . DIRECTORY_SEPARATOR . $addon['plugin_name'];
                if (!is_writable($pluginDir)) {
                    $this->notWritableAddons[$addon['addon_id']] = $addon['plugin_name'];
                }
            }

            if (count($updatePendingAddons) > 0) {
                $this->getUser()->setAttribute('marketplace.baseUrl', $marketplaceUrl);
                $this->getUser()->setAttribute('marketplace.addons', json_encode($updatePendingAddons));
                $this->getUser()->setAttribute('marketplace.accessToken', $upgradeUtility->getMarketplaceAccessToken());
            }
        } else {
            $this->getUser()->setAttribute('missingPlugins', $notCopiedPlugins);
        }
    }

    /**
     * get ohrmVersion
     * @return string ohrmVersion
     */
    protected function getOhrmVersion()
    {
        if (!class_exists('sysConf')) {
            require_once ROOT_PATH . '/lib/confs/sysConf.php';
        }
        $sysConf = new sysConf();
        return $sysConf->getReleaseVersion();
    }
}