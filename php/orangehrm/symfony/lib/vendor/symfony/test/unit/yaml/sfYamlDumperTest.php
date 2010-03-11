<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/yaml/sfYamlParser.class.php');
require_once(dirname(__FILE__).'/../../../lib/yaml/sfYamlDumper.class.php');

$t = new lime_test(141, new lime_output_color());

$parser = new sfYamlParser();
$dumper = new sfYamlDumper();

$path = dirname(__FILE__).'/fixtures';
$files = $parser->parse(file_get_contents($path.'/index.yml'));
foreach ($files as $file)
{
  $t->diag($file);

  $yamls = file_get_contents($path.'/'.$file.'.yml');

  // split YAMLs documents
  foreach (preg_split('/^---( %YAML\:1\.0)?/m', $yamls) as $yaml)
  {
    if (!$yaml)
    {
      continue;
    }

    $test = $parser->parse($yaml);
    if (isset($test['dump_skip']) && $test['dump_skip'])
    {
      continue;
    }
    else if (isset($test['todo']) && $test['todo'])
    {
      $t->todo($test['test']);
    }
    else
    {
      $expected = eval('return '.trim($test['php']).';');

      $t->is_deeply($parser->parse($dumper->dump($expected, 10)), $expected, $test['test']);
    }
  }
}

// inline level
$array = array(
  '' => 'bar',
  'foo\'bar' => array(),
  'bar' => array(1, 'foo'),
  'foobar' => array(
    'foo' => 'bar',
    'bar' => array(1, 'foo'),
    'foobar' => array(
      'foo' => 'bar',
      'bar' => array(1, 'foo'),
    ),
  ),
);

$expected = <<<EOF
{ '': bar, 'foo''bar': {  }, bar: [1, foo], foobar: { foo: bar, bar: [1, foo], foobar: { foo: bar, bar: [1, foo] } } }
EOF;
$t->is($dumper->dump($array, -10), $expected, '->dump() takes an inline level argument');
$t->is($dumper->dump($array, 0), $expected, '->dump() takes an inline level argument');

$expected = <<<EOF
'': bar
'foo''bar': {  }
bar: [1, foo]
foobar: { foo: bar, bar: [1, foo], foobar: { foo: bar, bar: [1, foo] } }

EOF;
$t->is($dumper->dump($array, 1), $expected, '->dump() takes an inline level argument');

$expected = <<<EOF
'': bar
'foo''bar': {  }
bar:
  - 1
  - foo
foobar:
  foo: bar
  bar: [1, foo]
  foobar: { foo: bar, bar: [1, foo] }

EOF;
$t->is($dumper->dump($array, 2), $expected, '->dump() takes an inline level argument');

$expected = <<<EOF
'': bar
'foo''bar': {  }
bar:
  - 1
  - foo
foobar:
  foo: bar
  bar:
    - 1
    - foo
  foobar:
    foo: bar
    bar: [1, foo]

EOF;
$t->is($dumper->dump($array, 3), $expected, '->dump() takes an inline level argument');

$expected = <<<EOF
'': bar
'foo''bar': {  }
bar:
  - 1
  - foo
foobar:
  foo: bar
  bar:
    - 1
    - foo
  foobar:
    foo: bar
    bar:
      - 1
      - foo

EOF;
$t->is($dumper->dump($array, 4), $expected, '->dump() takes an inline level argument');
$t->is($dumper->dump($array, 10), $expected, '->dump() takes an inline level argument');

// objects
$t->diag('Objects support');
class A
{
  public $a = 'foo';
}
$a = array('foo' => new A(), 'bar' => 1);
$t->is($dumper->dump($a), '{ foo: !!php/object:O:1:"A":1:{s:1:"a";s:3:"foo";}, bar: 1 }', '->dump() is able to dump objects');
