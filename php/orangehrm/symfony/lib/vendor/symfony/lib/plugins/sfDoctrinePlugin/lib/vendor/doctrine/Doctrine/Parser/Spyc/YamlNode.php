<?php
/**
* Spyc -- A Simple PHP YAML Class
* @version 0.2.(5) -- 2006-12-31
* @author Chris Wanstrath <chris@ozmm.org>
* @author Vlad Andersen <vlad@oneiros.ru>
* @link http://spyc.sourceforge.net/
* @copyright Copyright 2005-2006 Chris Wanstrath
* @license http://www.opensource.org/licenses/mit-license.php MIT License
* @package Doctrine
* @subpackage Spyc
*/

/**
* A node, used by Doctrine_Parser_Spyc for parsing YAML.
* @package Doctrine
* @subpackage Spyc
*/
class Doctrine_Parser_Spyc_YamlNode
{
    /**#@+
     * @access public
     * @var string
     */
    var $parent;
    var $id;

    /**#@+*/
    /**
     * @access public
     * @var mixed
     */
    var $data;

    /**
     * @access public
     * @var int
     */
    var $indent;

    /**
     * @access public
     * @var bool
     */
    var $children = false;

    /**
     * The constructor assigns the node a unique ID.
     * @access public
     * @return void
     */
    function __construct($nodeId)
    {
      $this->id = $nodeId;
    }
}