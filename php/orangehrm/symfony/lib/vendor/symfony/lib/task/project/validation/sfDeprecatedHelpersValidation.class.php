<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Finds deprecated helpers usage.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDeprecatedHelpersValidation.class.php 25411 2009-12-15 15:31:29Z fabien $
 */
class sfDeprecatedHelpersValidation extends sfValidation
{
  public function getHeader()
  {
    return 'Checking usage of deprecated helpers';
  }

  public function getExplanation()
  {
    return array(
          '',
          '  The files above use deprecated helpers',
          '  that have been removed in symfony 1.4.',
          '',
          '  You can find a list of all deprecated helpers under the',
          '  "Helpers" section of the DEPRECATED tutorial:',
          '',
          '  http://www.symfony-project.org/tutorial/1_4/en/deprecated',
          '',
    );
  }

  public function validate()
  {
    $helpers = array(
      'select_day_tag', 'select_month_tag', 'select_year_tag', 'select_date_tag', 'select_second_tag',
      'select_minute_tag', 'select_hour_tag', 'select_ampm_tag', 'select_time_tag', 'select_datetime_tag',
      'select_number_tag', 'select_timezone_tag', 'options_for_select', 'select_tag', 'select_country_tag',
      'select_language_tag', 'select_currency_tag', 'input_tag', 'input_hidden_tag', 'input_file_tag',
      'input_password_tag', 'textarea_tag', 'checkbox_tag', 'radiobutton_tag', 'input_date_range_tag',
      'input_date_tag', 'submit_tag', 'reset_tag', 'submit_image_tag', 'label_for',
      'object_admin_input_file_tag', 'object_admin_double_list', 'object_admin_select_list',
      'object_admin_check_list', 'object_input_date_tag', 'object_textarea_tag', 'objects_for_select',
      'object_select_tag', 'object_select_country_tag', 'object_select_language_tag', 'object_input_hidden_tag',
      'object_input_tag', 'object_checkbox_tag', 'form_has_error', 'form_error', 'get_callbacks',
      'get_ajax_options', 'button_to_remote', 'link_to_remote', 'periodically_call_remote', 'form_remote_tag',
      'submit_to_remote', 'submit_image_to_remote', 'update_element_function', 'evaluate_remote_response',
      'remote_function', 'observe_field', 'observe_form', 'visual_effect', 'sortable_element',
      'draggable_element', 'drop_receiving_element', 'input_auto_complete_tag', 'input_in_place_editor_tag',
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
      foreach ($helpers as $helper)
      {
        if (preg_match('#\b'.preg_quote($helper, '#').'\b#', $content))
        {
          $matches[] = $helper;
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
