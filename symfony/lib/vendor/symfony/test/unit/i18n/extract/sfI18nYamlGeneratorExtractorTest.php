<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(3);

// __construct()
$t->diag('__construct()');
$e = new sfI18nYamlGeneratorExtractor();
$t->ok($e instanceof sfI18nExtractorInterface, 'sfI18nYamlGeneratorExtractor implements the sfI18nExtractorInterface interface');

// ->extract();
$t->diag('->extract()');

$content = <<<EOF
generator:
  param:
    config:
      fields:
        name: { name: "Global Field Name", help: "Global Help for Name" }
      list:
        title: List title
        fields:
          name: { name: "List Field Name", help: "List Help for Name" }
      edit:
        title: Edit title
        display:
          NONE: []
          First category: []
          Last category: []
        fields:
          name: { name: "Edit Field Name", help: "Edit Help for Name" }
EOF;

$t->is($e->extract($content), array(
  'List title',
  'Edit title',
  'Global Field Name',
  'Global Help for Name',
  'List Field Name',
  'List Help for Name',
  'Edit Field Name',
  'Edit Help for Name',
  'First category',
  'Last category',
), '->extract() extracts strings from generator.yml files');

$content = <<<EOF
generator:
  param:
    edit:
      display: [first_name, last_name]
EOF;

$t->is($e->extract($content), array(), '->extract() extracts strings from generator.yml files');
