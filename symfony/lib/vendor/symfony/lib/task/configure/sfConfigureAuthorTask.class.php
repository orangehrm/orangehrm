<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Configures the main author of the project.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfConfigureAuthorTask.class.php 6978 2008-01-06 18:53:47Z fabien $
 */
class sfConfigureAuthorTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('author', sfCommandArgument::REQUIRED, 'The project author'),
    ));

    $this->namespace = 'configure';
    $this->name = 'author';

    $this->briefDescription = 'Configure project author';

    $this->detailedDescription = <<<EOF
The [configure:author|INFO] task configures the author for a project:

  [./symfony configure:author "Fabien Potencier <fabien.potencier@symfony-project.com>"|INFO]

The author is used by the generates to pre-configure the PHPDoc header for each generated file.

The value is stored in [config/properties.ini].
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $file = sfConfig::get('sf_config_dir').'/properties.ini';
    $content = parse_ini_file($file, true);

    if (!isset($content['symfony']))
    {
      $content['symfony'] = array();
    }

    $content['symfony']['author'] = $arguments['author'];

    $ini = '';
    foreach ($content as $section => $values)
    {
      $ini .= sprintf("[%s]\n", $section);
      foreach ($values as $key => $value)
      {
        $ini .= sprintf("  %s=%s\n", $key, $value);
      }
    }

    file_put_contents($file, $ini);
  }
}
