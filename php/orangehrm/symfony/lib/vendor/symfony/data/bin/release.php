<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Release script.
 *
 * Usage: php data/bin/release.php 1.3.0 stable
 *
 * @package    symfony
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: release.php 24079 2009-11-17 07:59:41Z Kris.Wallsmith $
 */
require_once(dirname(__FILE__).'/../../lib/exception/sfException.class.php');
require_once(dirname(__FILE__).'/../../lib/task/sfFilesystem.class.php');
require_once(dirname(__FILE__).'/../../lib/util/sfFinder.class.php');
require_once(dirname(__FILE__).'/../../lib/vendor/lime/lime.php');

if (!isset($argv[1]))
{
  throw new Exception('You must provide version prefix.');
}

if (!isset($argv[2]))
{
  throw new Exception('You must provide stability status (alpha/beta/stable).');
}

$stability = $argv[2];

$filesystem = new sfFilesystem();

if (($stability == 'beta' || $stability == 'alpha') && count(explode('.', $argv[1])) < 2)
{
  $version_prefix = $argv[1];

  list($result) = $filesystem->execute('svn status -u '.getcwd());
  if (preg_match('/Status against revision\:\s+(\d+)\s*$/im', $result, $match))
  {
    $version = $match[1];
  }

  if (!isset($version))
  {
    throw new Exception('Unable to find last SVN revision.');
  }

  // make a PEAR compatible version
  $version = $version_prefix.'.'.$version;
}
else
{
  $version = $argv[1];
}

print sprintf("Releasing symfony version \"%s\".\n", $version);

// tests
list($result) = $filesystem->execute('php data/bin/symfony symfony:test');

if (0 != $result)
{
  throw new Exception('Some tests failed. Release process aborted!');
}

if (is_file('package.xml'))
{
  $filesystem->remove(getcwd().DIRECTORY_SEPARATOR.'package.xml');
}

$filesystem->copy(getcwd().'/package.xml.tmpl', getcwd().'/package.xml');

// add class files
$finder = sfFinder::type('file')->relative();
$xml_classes = '';
$dirs = array('lib' => 'php', 'data' => 'data');
foreach ($dirs as $dir => $role)
{
  $class_files = $finder->in($dir);
  foreach ($class_files as $file)
  {
    $xml_classes .= '<file role="'.$role.'" baseinstalldir="symfony" install-as="'.$file.'" name="'.$dir.'/'.$file.'" />'."\n";
  }
}

// replace tokens
$filesystem->replaceTokens(getcwd().DIRECTORY_SEPARATOR.'package.xml', '##', '##', array(
  'SYMFONY_VERSION' => $version,
  'CURRENT_DATE'    => date('Y-m-d'),
  'CLASS_FILES'     => $xml_classes,
  'STABILITY'       => $stability,
));

list($results) = $filesystem->execute('pear package');
echo $results;

$filesystem->remove(getcwd().DIRECTORY_SEPARATOR.'package.xml');

exit(0);
