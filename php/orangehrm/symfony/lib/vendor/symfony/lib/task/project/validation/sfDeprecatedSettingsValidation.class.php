<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Finds deprecated settings usage.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDeprecatedSettingsValidation.class.php 25410 2009-12-15 15:19:07Z fabien $
 */
class sfDeprecatedSettingsValidation extends sfValidation
{
  public function getHeader()
  {
    return 'Checking usage of deprecated settings';
  }

  public function getExplanation()
  {
    return array(
          '',
          '  The files above use deprecated settings',
          '  that have been removed in symfony 1.4.',
          '',
          '  You can find a list of all deprecated settings under the',
          '  "Settings" section of the DEPRECATED tutorial:',
          '',
          '  http://www.symfony-project.org/tutorial/1_4/en/deprecated',
          '',
    );
  }

  public function validate()
  {
    $settings = array(
      'sf_check_symfony_version', 'sf_max_forwards', 'sf_lazy_cache_key', 'sf_strip_comments',
      'sf_lazy_routes_deserialize', 'sf_calendar_web_dir', 'sf_rich_text_js_dir', 'sf_validation_error_prefix',
      'sf_validation_error_suffix', 'sf_validation_error_class', 'sf_validation_error_id_prefix',
      '_is_internal', 'sf_doc_dir',
    );

    $found = array();
    $files = sfFinder::type('file')->name('*.php')->prune('vendor')->in(array(
      sfConfig::get('sf_apps_dir'),
      sfConfig::get('sf_lib_dir'),
      sfConfig::get('sf_test_dir'),
      sfConfig::get('sf_plugins_dir'),
    ));
    foreach ($files as $file)
    {
      $content = sfToolkit::stripComments(file_get_contents($file));

      $matches = array();
      foreach ($settings as $setting)
      {
        if (false !== stripos($content, $setting))
        {
          $matches[] = $setting;
        }
      }

      if ($matches)
      {
        $found[$file] = implode(', ', $matches);
      }
    }

    return $found;
  }
}
