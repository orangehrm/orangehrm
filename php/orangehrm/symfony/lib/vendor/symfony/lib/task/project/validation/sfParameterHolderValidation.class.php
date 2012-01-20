<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Finds usage of array notation with a parameter holder.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfParameterHolderValidation.class.php 25411 2009-12-15 15:31:29Z fabien $
 */
class sfParameterHolderValidation extends sfValidation
{
  public function getHeader()
  {
    return 'Checking usage of array notation with a parameter holder';
  }

  public function getExplanation()
  {
    return array(
          '',
          '  The files above use the array notation with a parameter holder,',
          '  which is not supported anymore in symfony 1.4.',
          '  For instance, you need to change this construct:',
          '',
          '    $foo = $request->getParameter(\'foo[bar]\')',
          '',
          '  to this one:',
          '',
          '    $params = $request->getParameter(\'foo\')',
          '    $foo = $params[\'bar\'])',
          '',
    );
  }

  public function validate()
  {
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

      if (preg_match('#\b(get|has|remove)(Request)*Parameter\(\s*[\'"][^\),]*?\[[^\),]#', $content))
      {
        $found[$file] = true;
      }
    }

    return $found;
  }
}
