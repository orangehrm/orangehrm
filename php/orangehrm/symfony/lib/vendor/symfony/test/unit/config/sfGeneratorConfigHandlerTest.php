<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

sfConfig::set('sf_symfony_lib_dir', realpath(dirname(__FILE__).'/../../../lib'));

$t = new lime_test(5, new lime_output_color());

$handler = new sfGeneratorConfigHandler();
$handler->initialize();

$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'sfGeneratorConfigHandler'.DIRECTORY_SEPARATOR;

$t->diag('parse errors');
$files = array(
  $dir.'empty.yml',
  $dir.'no_generator_class.yml',
);

try
{
  $data = $handler->execute($files);
  $t->fail('generator.yml must have a "class" section');
}
catch (sfParseException $e)
{
  $t->like($e->getMessage(), '/must specify a generator class section under the generator section/', 'generator.yml must have a "class" section');
}

$files = array(
  $dir.'empty.yml',
  $dir.'no_generator_section.yml',
);

try
{
  $data = $handler->execute($files);
  $t->fail('generator.yml must have a "generator" section');
}
catch (sfParseException $e)
{
  $t->like($e->getMessage(), '/must specify a generator section/', 'generator.yml must have a "generator" section');
}

$files = array(
  $dir.'empty.yml',
  $dir.'root_fields_section.yml',
);

try
{
  $data = $handler->execute($files);
  $t->fail('generator.yml can have a "fields" section but only under "param"');
}
catch (sfParseException $e)
{
  $t->like($e->getMessage(), '/can specify a "fields" section but only under the param section/', 'generator.yml can have a "fields" section but only under "param"');
}

$files = array(
  $dir.'empty.yml',
  $dir.'root_list_section.yml',
);

try
{
  $data = $handler->execute($files);
  $t->fail('generator.yml can have a "list" section but only under "param"');
}
catch (sfParseException $e)
{
  $t->like($e->getMessage(), '/can specify a "list" section but only under the param section/', 'generator.yml can have a "list" section but only under "param"');
}

$files = array(
  $dir.'empty.yml',
  $dir.'root_edit_section.yml',
);

try
{
  $data = $handler->execute($files);
  $t->fail('generator.yml can have a "edit" section but only under "param"');
}
catch (sfParseException $e)
{
  $t->like($e->getMessage(), '/can specify a "edit" section but only under the param section/', 'generator.yml can have a "edit" section but only under "param"');
}
