<?php

/**
 * BaseI18NLangString
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int                                  $id                                        Type: integer, primary key
 * @property int                                  $unitId                                    Type: integer
 * @property int                                  $sourceId                                  Type: integer
 * @property int                                  $groupId                                   Type: integer
 * @property string                               $value                                     Type: string, unique
 * @property string                               $note                                      Type: string
 * @property I18NGroup                            $I18NGroup                                 
 * @property I18NSource                           $I18NSource                                
 * @property Doctrine_Collection|I18NTranslate[]  $I18NTranslate                             
 *  
 * @method int                                    getId()                                    Type: integer, primary key
 * @method int                                    getUnitId()                                Type: integer
 * @method int                                    getSourceId()                              Type: integer
 * @method int                                    getGroupId()                               Type: integer
 * @method string                                 getValue()                                 Type: string, unique
 * @method string                                 getNote()                                  Type: string
 * @method I18NGroup                              getI18NGroup()                             
 * @method I18NSource                             getI18NSource()                            
 * @method Doctrine_Collection|I18NTranslate[]    getI18NTranslate()                         
 *  
 * @method I18NLangString                         setId(int $val)                            Type: integer, primary key
 * @method I18NLangString                         setUnitId(int $val)                        Type: integer
 * @method I18NLangString                         setSourceId(int $val)                      Type: integer
 * @method I18NLangString                         setGroupId(int $val)                       Type: integer
 * @method I18NLangString                         setValue(string $val)                      Type: string, unique
 * @method I18NLangString                         setNote(string $val)                       Type: string
 * @method I18NLangString                         setI18NGroup(I18NGroup $val)               
 * @method I18NLangString                         setI18NSource(I18NSource $val)             
 * @method I18NLangString                         setI18NTranslate(Doctrine_Collection $val) 
 *  
 * @package    orangehrm
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseI18NLangString extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_i18n_lang_string');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('unit_id as unitId', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('source_id as sourceId', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('group_id as groupId', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('value', 'string', null, array(
             'type' => 'string',
             'unique' => true,
             ));
        $this->hasColumn('note', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('I18NGroup', array(
             'local' => 'groupId',
             'foreign' => 'id'));

        $this->hasOne('I18NSource', array(
             'local' => 'sourceId',
             'foreign' => 'id'));

        $this->hasMany('I18NTranslate', array(
             'local' => 'id',
             'foreign' => 'langStringId'));
    }
}
