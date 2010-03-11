<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(98, new lime_output_color());

$tests = array(
  'Integer',
  'Integer()',
  'Integer({min: 18})',
  'Integer(  {  min:  18  }  )',
  'Integer({min: 18}, {required: "This is required"})',
  '(Integer)',
  '
   (Integer)
  ',
  '(
    Integer
   )',

  'Integer and String',
  'Integer or String',
  'Integer and (String or Email)',

  'age:Integer',
  'age:Integer()',
  'age:Integer({min: 18})',
  'age:Integer({min: 18}, {required: "This is required"})',
  '(age:Integer)',
  '
    (age:Integer)
  ',
  '(
    age:Integer
   )',

  'age == password',
  'age ==() password',
  'age ==({}, {invalid: "Not equal."}) password',
  'age ==(  {  },  {  invalid:  "Not equal."  }  ) password',
  'age ==({required: true}, {invalid: "Not equal."}) password',
  'age ==({}) password',
  "age
   ==
   password",
  '(age == password)',
  '
    (age == password)
  ',
  '(
    age == password
   )',

  'age != password',
  'age > password',
  'age >= password',
  'age <= password',
  'age > password',

  'age:Integer and password:String',
  'age:Integer and() password:String()',
  'age:Integer and({}, {invalid: "This is invalid."}) password:String({required: true}, {min_length: Min length error message.})',
  'age:Integer and({required: true}, {invalid: "This is invalid."}) password:String',
  'age:Integer and({}) password:String({}, {})',
  "age:Integer
   and
   password:String",
   '(age:Integer and password:String)',
   'age:Integer or password:String',
   '
    (age:Integer or password:String)
   ',
   '(age:Integer or password:String)',
   '(
     age:Integer or password:String
    )',
  '
   (
    age:Integer
     or
    password:String
   )
  ',

  "
   (first_name:String or age:Integer)
    and
   age:Integer({min: 18}, {required: \"This is required.\"})
    or
   (
     age:Integer({max: 18})
      and
     is_young:Boolean({required: true})
   )
  ",

  'email:Email and (age:Integer({min: 18}) or (age:Integer({max: 18}) and is_young:Boolean({required: true})))',
  '(password == password_bis) and begin_date <= end_date and password:String({min_length: 4, max_length: 18})',
  'countries:Choice({choices: [France, USA, Italy, Spain]}) and password ==({}, {invalid: "Passwords must be the same (%left_field% != %right_field%)"}) password_bis and begin_date <= end_date and password:String({min_length: 4, max_length: 18})',
);

foreach ($tests as $test)
{
  $v = new sfValidatorFromDescription($test);
  $embedValidator = $v->getValidator();

  eval('$evaledValidator = '.$v->asPhp().';');

  $t->is($evaledValidator->asString(), $v->asString(), sprintf('sfValidatorFromDescription is able to parse "%s"', str_replace("\n", '\n', $test)));

  $v1 = new sfValidatorFromDescription($embedValidator->asString());
  $embedValidator1 = $v1->getValidator();

  $v2 = new sfValidatorFromDescription($embedValidator1->asString());

  $t->is($v1->asString(), $v2->asString(), sprintf('sfValidatorFromDescription is able to parse "%s"', str_replace("\n", '\n', $test)));
}
