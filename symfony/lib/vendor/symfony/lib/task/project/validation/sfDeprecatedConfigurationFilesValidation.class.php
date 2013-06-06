<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Finds deprecated configuration files usage.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDeprecatedConfigurationFilesValidation.class.php 24610 2009-11-30 22:07:34Z FabianLange $
 */
class sfDeprecatedConfigurationFilesValidation extends sfValidation
{
  public function getHeader()
  {
    return 'Checking usage of deprecated configuration files';
  }

  public function getExplanation()
  {
    return array(
          '',
          '  The project uses deprecated configuration files',
          '  that have been removed in symfony 1.4 (mailer.yml, validate/*.yml)',
          '  or for which the format changed (generator.yml)',
          '',
    );
  }

  public function validate()
  {
    // mailer.yml
    $files = sfFinder::type('file')->name('mailer.yml')->in($this->getProjectConfigDirectories());
    $found = array();
    foreach ($files as $file)
    {
      $found[$file] = true;
    }

    // modules/*/validate/*.yml
    $files = sfFinder::type('file')->name('*.yml')->in(array_merge(
      glob(sfConfig::get('sf_apps_dir').'/*/modules/*/validate'),
      glob(sfConfig::get('sf_plugins_dir').'/*/modules/*/validate')
    ));
    foreach ($files as $file)
    {
      $found[$file] = true;
    }

    // old generator.yml
    $files = sfFinder::type('file')->name('generator.yml')->in(array(
      sfConfig::get('sf_apps_dir'),
      sfConfig::get('sf_plugins_dir'),
    ));
    foreach ($files as $file)
    {
      $content = file_get_contents($file);

      if (false !== strpos($content, 'sfPropelAdminGenerator'))
      {
        $found[$file] = true;
      }
    }

    return $found;
  }
}
