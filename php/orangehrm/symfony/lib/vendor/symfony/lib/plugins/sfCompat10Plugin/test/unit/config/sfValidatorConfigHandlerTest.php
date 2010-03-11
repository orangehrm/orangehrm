<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(1, new lime_output_color());

$handler = new sfValidatorConfigHandler();
$handler->initialize();

$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'sfValidatorConfigHandler'.DIRECTORY_SEPARATOR;

// standard format
$files = array($dir.'standard_format.yml');
$standard_data = $handler->execute($files);
$standard_data = preg_replace('#date\: \d+/\d+/\d+ \d+\:\d+\:\d+#', '', $standard_data);

// alternate format
$files = array($dir.'alternate_format.yml');
$alternate_data = $handler->execute($files);
$alternate_data = preg_replace('#date\: \d+/\d+/\d+ \d+\:\d+\:\d+#', '', $alternate_data);

$t->is($standard_data, $alternate_data, 'standard and alternate format of validate.yml files produce the same output');
