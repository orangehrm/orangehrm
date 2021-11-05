<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

class addThemeAction extends sfAction
{
    const MAX_MAIN_LOGO_WIDTH = 300;
    const MAX_MAIN_LOGO_HEIGHT = 60;
    const MAX_LOGIN_BANNER_WIDTH = 1024;
    const MAX_LOGIN_BANNER_HEIGHT = 180;
    const UPDATED_WEBRES_DIR = "admin.corporate_branding.webres_dir";

    protected $configService;

    /**
     * @return ConfigService
     */
    public function getConfigService()
    {
        if (!$this->configService instanceof ConfigService) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     * @param mixed $configService
     */
    public function setConfigService($configService)
    {
        $this->configService = $configService;
    }

    /**
     * @param sfRequest $request
     */
    public function execute($request)
    {
        $this->form = new ThemeForm();
        if ($request->isMethod("POST")) {
            $postParams = $request->getPostParameters();
            $this->form->bind($postParams);
            if (!$this->form->isValid()) {
                $this->getUser()->setFlash('warning', __("Invalid color values"));
                $this->redirect('admin/addTheme');
            }
            if ($postParams['resetTheme'] == '1') {
                $this->getThemeService()->getThemeDao()->deleteThemeByThemeName('custom');
                OrangeConfig::getInstance()->setAppConfValue(ConfigService::KEY_THEME_NAME, "default");
                $this->getUser()->setAttribute('meta.themeName', 'default');
                $this->getUser()->setFlash('success', __("Successfully reset to default theme."));
                $this->redirect('admin/addTheme');
            }
            $files = $request->getFiles();
            $mainLogoWidth = getimagesize($files['file']['tmp_name'])[0];
            $mainLogoHeight = getimagesize($files['file']['tmp_name'])[1];
            $loginBannerWidth = getimagesize($files['loginBanner']['tmp_name'])[0];
            $loginBannerHeight = getimagesize($files['loginBanner']['tmp_name'])[1];

            //check the width and height compatible for main logo
            if( ($mainLogoWidth > self::MAX_MAIN_LOGO_WIDTH) || ($mainLogoHeight > self::MAX_MAIN_LOGO_HEIGHT) ) {
                $this->getUser()->setFlash('warning.nofade', __("Please upload an image of dimensions below ") . self::MAX_MAIN_LOGO_WIDTH . "*" . self::MAX_MAIN_LOGO_HEIGHT);
                $this->redirect('admin/addTheme');
            }
            //check the width and height compatible for login banner
            if( ($loginBannerWidth > self::MAX_LOGIN_BANNER_WIDTH) || ($loginBannerHeight > self::MAX_LOGIN_BANNER_HEIGHT) ) {
                $this->getUser()->setFlash('warning.nofade', __("Please upload an image of dimensions below ") . self::MAX_LOGIN_BANNER_WIDTH . "*" . self::MAX_LOGIN_BANNER_HEIGHT);
                $this->redirect('admin/addTheme');
            }

            //in case if main logo size exceeds 1MB
            if($files['file']['size'] > 1000000) {
                $this->getUser()->setFlash('warning.nofade', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
                $this->redirect('admin/addTheme');
            }
            //in case if login banner size exceeds 1MB
            if($files['loginBanner']['size'] > 1000000) {
                $this->getUser()->setFlash('warning.nofade', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
                $this->redirect('admin/addTheme');
            }
            //verify the main logo and login banner is of image type
            $mainLogoFileType = $files['file']['type'];
            $loginBannerFileType = $files['loginBanner']['type'];
            $allowedImageTypes = ['image/jpeg','image/png','image/jpg','image/gif','image/pjpeg','image/x-png'];
            if(!empty($mainLogoFileType) && !in_array($mainLogoFileType, $allowedImageTypes)) {
                $this->getUser()->setFlash('warning.nofade', __(TopLevelMessages::FILE_TYPE_SAVE_FAILURE));
                $this->redirect('admin/addTheme');
            }
            if(!empty($loginBannerFileType) && !in_array($loginBannerFileType, $allowedImageTypes)) {
                $this->getUser()->setFlash('warning.nofade', __(TopLevelMessages::FILE_TYPE_SAVE_FAILURE));
                $this->redirect('admin/addTheme');
            }

            $themeName = "custom";//$postParams['themeName'];
            $primaryColor = $postParams['primaryColor'];
            $mainLogo = file_get_contents($files['file']['tmp_name']);
            $loginBanner = file_get_contents($files['loginBanner']['tmp_name']);
            $socialMediaIcons = $postParams['socialMediaIcons'];
            $secondaryColor = $postParams['secondaryColor'];
            $buttonSuccessColor = $postParams['buttonSuccessColor'];
            $buttonCancelColor = $postParams['buttonCancelColor'];
            $variables = [];
            $variables['primaryColor'] = $primaryColor;
            $variables['secondaryColor'] = $secondaryColor;
            $variables['buttonSuccessColor'] = $buttonSuccessColor;
            $variables['buttonCancelColor'] = $buttonCancelColor;

            $theme = $this->getThemeService()->getThemeByThemeName($themeName);

            $theme->setVariables(json_encode($variables));
            $theme->setSocialMediaIcons($socialMediaIcons);
            if($mainLogo) {
                $theme->setMainLogo($mainLogo);
            }
            if($loginBanner) {
                $theme->setLoginBanner($loginBanner);
            }
            $result = $this->getThemeService()->addTheme($theme);

            if($result) {
                try {
                    $isPublished = $this->getThemeService()->publishTheme($theme);
                } catch (Exception $e) {
                    Logger::getLogger("orangehrm")->error($e->getCode() . ' : ' . $e->getMessage());
                    Logger::getLogger("orangehrm")->error($e->getTraceAsString());
                    $this->getUser()->setFlash('error', __($e->getMessage()));
                    $this->redirect('admin/addTheme');
                }
                if($isPublished) {
                    $this->getUser()->setAttribute('meta.themeName', $themeName);
                    $this->getUser()->setFlash('success', __("Successfully Published"));
                }
            }

            // OHRM-590: [Random]CSS is breaking when publishing a custom theme
            $this->getUser()->setAttribute(self::UPDATED_WEBRES_DIR, sfConfig::get('ohrm_resource_dir'));

            $this->redirect('admin/addTheme');
        } else {

            // OHRM-590: [Random]CSS is breaking when publishing a custom theme
            $resourceDir = $this->getUser()->getAttribute(self::UPDATED_WEBRES_DIR);
            if ($this->isWebResExists($resourceDir)) {
                $propertiesToSet = array(
                    'sf_web_css_dir_name' => $resourceDir . '/css',
                    'sf_web_js_dir_name' => $resourceDir . '/js',
                    'sf_web_images_dir_name' => $resourceDir . '/images',
                    'ohrm_resource_dir' => $resourceDir,
                );

                foreach ($propertiesToSet as $key => $value) {
                    sfConfig::set($key, $value);
                }
            }

            $themeName = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_THEME_NAME);
            $theme = $this->getThemeService()->getThemeByThemeName($themeName);
            $variables = json_decode($theme->getVariables(),true);
            $this->showChecked =  ($theme->getSocialMediaIcons() == 'none') ? false : true ;
            $this->form->setDefaults($variables);

            $html = $this->getController()->getPresentationFor('admin', 'viewEmployeeListPartial');
            $doc = new DOMDocument();
            $doc->loadHTML($html);
            $this->searchForm = $doc->saveHTML($doc->getElementById('employee-information'));
            $this->searchResults = $doc->saveHTML($doc->getElementById('search-results'));
        }
    }

    /**
     * Check whether `ohrm_resource_dir` exists
     * @param string $resourceDir
     * @return bool
     */
    protected function isWebResExists($resourceDir)
    {
        if (is_null($resourceDir)) {
            return false;
        }
        if (is_dir(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $resourceDir)) {
            return true;
        }
        return false;
    }

    /**
     * @return ThemeService|null
     */
    protected function getThemeService()
    {
        if (!isset($this->themeService)) {
            $this->themeService = new ThemeService();
        }
        return $this->themeService;
    }
    /**
     * @throws CoreServiceException
     * @throws sfStopException
     */
    public function preExecute()
    {
        $licenseEnforced = $this->isLicenseEnforced();
        if ($licenseEnforced) {
            $properties = array_map(function($property) {
                return $property['value'];
            }, ioncube_license_properties());
            $addonName = $properties['addonName'];
            $instanceId = $properties['instanceIdentifier'];
            if ($addonName !== 'Corporate-Branding-opensource' || $instanceId !== $this->getConfigService()->getInstanceIdentifier()) {
                $this->getUser()->setFlash(displayMessageAction::MESSAGE_HEADING, __('Incorrect license for Corporate Branding addon'), false);
                $this->getUser()->setFlash('error.nofade', __('Please update the correct license for Corporate Branding addon.'), false);
                $this->forward('core', 'displayMessage');
            }

            if ($this->hasLicenseExpired()) {
                // block access
                $this->getUser()->setFlash(displayMessageAction::MESSAGE_HEADING, __('License Expired.'), false);
                $this->getUser()->setFlash('error.nofade', __('This addon license is expired, Please request through the marketplace to renew.'), false);
                $this->forward('core', 'displayMessage');
            }
        }
    }

    /**
     * @return bool
     */
    protected function hasLicenseExpired()
    {
        if (!ioncube_license_has_expired()) {
            return false;
        }
        $fileInfo = ioncube_file_info();
        $expiry = $fileInfo['FILE_EXPIRY'];
        $graceDays = $this->getLicenseExpiryGracePeriod();
        $endOfGracePeriod = strtotime("+{$graceDays} day", $expiry);
        return time() > $endOfGracePeriod;
    }

    /**
     * @return bool
     */
    public function isLicenseEnforced()
    {
        return function_exists('ioncube_file_is_encoded') && ioncube_file_is_encoded();
    }

    /**
     * @return int
     */
    protected function getLicenseExpiryGracePeriod()
    {
        $gracePeriod = 0;
        $properties = array_map(function($property) {
            return $property['value'];
        }, ioncube_license_properties());
        if (isset($properties['gracePeriod'])) {
            $gracePeriod = $properties['gracePeriod'];
        }
        return $gracePeriod;
    }

}
