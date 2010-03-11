<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrade configuration.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfConfigUpgrade.class.php 9191 2008-05-22 17:47:29Z fabien $
 */
class sfConfigUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    if (file_exists(sfConfig::get('sf_lib_dir').'/ProjectConfiguration.class.php'))
    {
      throw new sfCommandException('Unable to upgrade your project automatically. Read the "NOTE to early adopters" at the end in the symfony UPGRADE file to upgrade your project manually.');
    }

    $this->checkConfigFiles();
    $this->upgradeFrontControllers();
    $this->upgradeConfigurationClasses();
  }

  protected function checkConfigFiles()
  {
    $finder = $this->getFinder('file')->name('config.php');
    foreach ($finder->in($this->getProjectConfigDirectories()) as $file)
    {
      $this->logSection('config', sprintf('The following file is not used anymore. Please remove it.', $file));
      $this->log('   '.$file);
      $this->logSection('config', '  If you made some customization in this file,');
      $this->logSection('config', '  please migrate the content to the configuration classes.');
    }
  }

  protected function upgradeFrontControllers()
  {
    // update front web controllers only if no changes have been made
    $finder = $this->getFinder('file')->name('*.php');
    foreach ($finder->in(sfConfig::get('sf_web_dir')) as $file)
    {
      $content = file_get_contents($file);

      // front controller?
      if (false === strpos($content, 'define(\'SF_ROOT_DIR\''))
      {
        continue;
      }

      // already upgraded?
      if (false !== strpos($content, 'sfContext::createInstance'))
      {
        continue;
      }

      if (!preg_match("/define\('SF_APP',\s*('|\")(.+?)\\1\)/", $content, $matches))
      {
        continue;
      }
      $app = $matches[2];

      if (!preg_match("/define\('SF_ENVIRONMENT',\s*('|\")(.+?)\\1\)/", $content, $matches))
      {
        continue;
      }
      $environment = $matches[2];

      if (!preg_match("/define\('SF_DEBUG',\s*(.+?)\)/", $content, $matches))
      {
        continue;
      }
      $debug = $matches[1];

      // for 0.6.3 compatibility (or manual "fixing" the missing) remove closing at tag end of file
      $content = preg_replace('/\?>\s*$/', '', $content);

      $originalContent = <<<EOF
<?php

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         '$app');
define('SF_ENVIRONMENT', '$environment');
define('SF_DEBUG',       $debug);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

sfContext::getInstance()->getController()->dispatch();

EOF;

      $newContent = file_get_contents(dirname(__FILE__).'/../../generator/skeleton/app/web/index.php');
      $newContent = str_replace(array('##APP_NAME##', '##ENVIRONMENT##', '##IS_DEBUG##'), array($app, $environment, $debug), $newContent);

      if ($originalContent == $content)
      {
        $content = $newContent;
        $this->logSection('config', sprintf('Migrated "%s"', $file));
      }
      else
      {
        $this->logSection('config', '  You made some customization in the following file:');
        $this->log('   '.$file);
        $this->logSection('config', '  Please, upgrade manually (new code appended as a comment)');

        $content .= sprintf("\n\n/*\n%s\n*/\n", $newContent);
      }

      file_put_contents($file, $content);
    }
  }

  protected function upgradeConfigurationClasses()
  {
    foreach ($this->getApplications() as $application)
    {
      $configPath = sfConfig::get('sf_apps_dir').'/'.$application.'/config/'.$application.'Configuration.class.php';

      if (file_exists($configPath))
      {
        continue;
      }

      $this->getFilesystem()->copy(dirname(__FILE__).'/../../generator/skeleton/app/app/config/ApplicationConfiguration.class.php', $configPath);

      $this->getFilesystem()->replaceTokens($configPath, '##', '##', array('APP_NAME' => $application));
    }
  }
}
