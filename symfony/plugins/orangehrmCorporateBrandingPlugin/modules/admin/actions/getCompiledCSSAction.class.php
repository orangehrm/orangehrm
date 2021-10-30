<?php

class getCompiledCSSAction extends sfAction
{
    public function execute($request)
    {
        $params = $request->getGetParameters();
        $indexIncluded = in_array($params['indexIncluded'], [true, 1, '1', 'true']);
        $imagesPath = sfConfig::get('ohrm_resource_dir') . '/themes/default/images/';
        $variables = array_merge($params, [
            'imagesPath' => '"' . ($indexIncluded ? '../' : '') . '../' . $imagesPath . '"',
            'login-logo-inner-color' => $params['primaryColor'],
            'login-logo-outer-color' => $params['secondaryColor'],
            'login-social-links-display' => 'inline',
        ]);
        $sass = Sass::instance();
        echo $sass->compileSCSS($variables);
        exit;
    }

    public function isSecure()
    {
        return false;
    }
}
