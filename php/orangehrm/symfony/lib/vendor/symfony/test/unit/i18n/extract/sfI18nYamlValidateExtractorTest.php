<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(2, new lime_output_color());

// __construct()
$t->diag('__construct()');
$e = new sfI18nYamlValidateExtractor();
$t->ok($e instanceof sfI18nExtractorInterface, 'sfI18nYamlValidateExtractor implements the sfI18nExtractorInterface interface');

// ->extract();
$t->diag('->extract()');

$content = <<<EOF
fields:
  name:
    required:
      msg: Name is required
    sfStringValidator:
      min_error: The name is too short

validators:
  myStringValidator:
    class: sfStringValidator
    param:
      min_error: The name is really too short
      max_error: The name is really too long
EOF;

$t->is($e->extract($content), array(
  'Name is required',
  'The name is too short',
  'The name is really too short',
  'The name is really too long',
), '->extract() extracts strings from generator.yml files');
