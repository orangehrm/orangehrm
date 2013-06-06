<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Finds deprecated plugins usage.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDeprecatedPluginsValidation.class.php 25410 2009-12-15 15:19:07Z fabien $
 */
class sfDeprecatedPluginsValidation extends sfValidation
{
  public function getHeader()
  {
    return 'Checking usage of deprecated plugins';
  }

  public function getExplanation()
  {
    return array(
          '',
          '  The files above use deprecated plugins',
          '  that have been removed in symfony 1.4.',
          '',
          'You can probably remove those references safely.',
          '',
    );
  }

  public function validate()
  {
    $found = array();
    $files = sfFinder::type('file')->name('*Configuration.class.php')->in($this->getProjectConfigDirectories());
    foreach ($files as $file)
    {
      $content = sfToolkit::stripComments(file_get_contents($file));

      $matches = array();
      if (false !== strpos($content, 'sfCompat10Plugin'))
      {
        $matches[] = 'sfCompat10Plugin';
      }
      if (false !== strpos($content, 'sfProtoculousPlugin'))
      {
        $matches[] = 'sfProtoculousPlugin';
      }

      if ($matches)
      {
        $found[$file] = implode(', ', $matches);
      }
    }

    return $found;
  }
}
