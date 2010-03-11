<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Francois Zaninotto <francois.zaninotto@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../bootstrap/unit.php');

class my_lime_test extends lime_test
{
  public function is_array_explicit($test, $target, $prefix = '')
  {
    foreach ($test as $key => $value)
    {
      if (is_array($value))
      {
        $this->is_array_explicit($value, $target[$key], $prefix.' '.$key);
      }
      else
      {
        $this->is($value, $target[$key], sprintf('%s %s is %s', $prefix, $key, $value));
      }
    }
  }

  public function is_line_by_line($exp1, $exp2)
  {
    $array_exp1 = explode("\n", $exp1);
    $array_exp2 = explode("\n", $exp2);
    $nb_lines = count($array_exp1);
    for ($i=0; $i < $nb_lines; $i++)
    {
      if(!$array_exp1[$i]) continue; // Skip blank lines to avoid testing nothing
      $this->is(trim($array_exp1[$i]), trim($array_exp2[$i]), sprintf('Line %d matches %s', $i, $array_exp1[$i]));
    }
  }
}

require_once(dirname(__FILE__).'/../../../../../test/bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../lib/addon/sfPropelDatabaseSchema.class.php');
require_once(dirname(__FILE__).'/../../../../util/sfInflector.class.php');
require_once(dirname(__FILE__).'/../../../../util/sfToolkit.class.php');
require_once(dirname(__FILE__).'/../../../../yaml/sfYaml.class.php');

$t = new my_lime_test(377, new lime_output_color());

$t->diag('Classical YAML to XML conversion');
$p = new sfPropelDatabaseSchema();
$p->loadYAML(dirname(__FILE__).'/fixtures/schema.yml');
$target = file_get_contents(dirname(__FILE__).'/fixtures/schema.xml');
$t->is_line_by_line($p->asXML(), $target);

$t->diag('New YAML to XML conversion');
$p = new sfPropelDatabaseSchema();
$p->loadYAML(dirname(__FILE__).'/fixtures/new_schema.yml');
$target = file_get_contents(dirname(__FILE__).'/fixtures/schema.xml');
$t->is_line_by_line($p->asXML(), $target);

$t->diag('New YAML to Old YAML conversion');
$old_yml_target = sfYaml::load(dirname(__FILE__).'/fixtures/schema.yml');
$p = new sfPropelDatabaseSchema();
$new_yml_transformed = $p->convertNewToOldYaml(sfYaml::load(dirname(__FILE__).'/fixtures/new_schema.yml'));
$t->is_array_explicit($new_yml_transformed, $old_yml_target);

$t->diag('Old YAML to New YAML conversion');
$new_yml_target = sfYaml::load(dirname(__FILE__).'/fixtures/new_schema.yml');
$p = new sfPropelDatabaseSchema();
$old_yml_transformed = $p->convertOldToNewYaml(sfYaml::load(dirname(__FILE__).'/fixtures/schema.yml'));
$t->is_array_explicit($old_yml_transformed, $new_yml_target);


$t->todo('XML and classical YAML internal representation');
$p1 = new sfPropelDatabaseSchema();
$p1->loadXML(dirname(__FILE__).'/fixtures/schema.xml');
$p2 = new sfPropelDatabaseSchema();
$p2->loadYAML(dirname(__FILE__).'/fixtures/schema.yml');
//$t->is_array_explicit($p1->asArray(), $p2->asArray());

$t->todo('XML and classical YAML compared as XML');
//$t->is_line_by_line($p1->asXML(), $p2->asXML());

$t->todo('XML and classical YAML compared as YAML');
//$t->is_line_by_line($p1->asYAML(), $p2->asYAML());
