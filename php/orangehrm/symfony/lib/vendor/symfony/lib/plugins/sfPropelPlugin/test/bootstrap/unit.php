<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../../../test/bootstrap/unit.php');

require_once(dirname(__FILE__).'/../../../../autoload/sfSimpleAutoload.class.php');
$autoload = sfSimpleAutoload::getInstance(sys_get_temp_dir().DIRECTORY_SEPARATOR.sprintf('sf_autoload_unit_propel_%s.data', md5(__FILE__)));
$autoload->addDirectory(realpath(dirname(__FILE__).'/../../lib'));
$autoload->register();

$_test_dir = realpath(dirname(__FILE__).'/..');

sfToolkit::addIncludePath(realpath(dirname(__FILE__).'/../../lib/vendor'));