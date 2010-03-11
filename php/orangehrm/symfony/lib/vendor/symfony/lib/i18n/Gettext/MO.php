<?php

// +----------------------------------------------------------------------+
// | PEAR :: File :: Gettext :: MO                                        |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id: MO.php 9856 2008-06-25 11:33:49Z fabien $

/**
 * File::Gettext::MO
 * 
 * @author      Michael Wallner <mike@php.net>
 * @license     PHP License
 */

require_once dirname(__FILE__).'/TGettext.class.php';

/** 
 * File_Gettext_MO
 * 
 * GNU MO file reader and writer.
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision: 9856 $
 * @access      public
 * @package System.I18N.core 
 */
class TGettext_MO extends TGettext
{
    /**
     * file handle
     * 
     * @access  private
     * @var     resource
     */
    protected $_handle = null;
    
    /**
     * big endianess
     * 
     * Whether to write with big endian byte order.
     * 
     * @access  public
     * @var     bool
     */
    protected $writeBigEndian = false;
    
    /**
     * Constructor
     *
     * @access  public
     * @return  object      File_Gettext_MO
     * @param   string      $file   path to GNU MO file
     */
    function TGettext_MO($file = '')
    {
        $this->file = $file;
    }

    /**
     * _read
     *
     * @access  private
     * @return  mixed
     * @param   int     $bytes
     */
    function _read($bytes = 1)
    {
        if (0 < $bytes = abs($bytes)) {
            return fread($this->_handle, $bytes);
        }
        return null;
    }
    
    /**
     * _readInt
     *
     * @access  private
     * @return  int
     * @param   bool    $bigendian
     */
    function _readInt($bigendian = false)
    {
		//unpack returns a reference????
		$unpacked = unpack($bigendian ? 'N' : 'V', $this->_read(4));
        return array_shift($unpacked);
    }
    
    /**
     * _writeInt
     *
     * @access  private
     * @return  int
     * @param   int     $int
     */
    function _writeInt($int)
    {
        return $this->_write(pack($this->writeBigEndian ? 'N' : 'V', (int) $int));
    }
    
    /**
     * _write
     *
     * @access  private
     * @return  int
     * @param   string  $data
     */
    function _write($data)
    {
        return fwrite($this->_handle, $data);
    }
    
    /**
     * _writeStr
     *
     * @access  private
     * @return  int
     * @param   string  $string
     */
    function _writeStr($string)
    {
        return $this->_write($string . "\0");
    }
    
    /**
     * _readStr
     *
     * @access  private
     * @return  string
     * @param   array   $params     associative array with offset and length 
     *                              of the string
     */
    function _readStr($params)
    {
        fseek($this->_handle, $params['offset']);
        return $this->_read($params['length']);
    }
    
    /**
     * Load MO file
     *
     * @access   public
     * @return   mixed   Returns true on success or PEAR_Error on failure.
     * @param    string  $file
     */
    function load($file = null)
    {
        if (!isset($file)) {
            $file = $this->file;
        }
        
        // open MO file
        if (!is_resource($this->_handle = @fopen($file, 'rb'))) {
            return false;
        }
        // lock MO file shared
        if (!@flock($this->_handle, LOCK_SH)) {
            @fclose($this->_handle);
            return false;
        }
        
        // read (part of) magic number from MO file header and define endianess

		//unpack returns a reference????
		$unpacked = unpack('c', $this->_read(4));
        switch ($magic = array_shift($unpacked))
        {
            case -34:
                $be = false;
            break;
            
            case -107:
                $be = true;
            break;
            
            default:
                return false;
        }

        // check file format revision - we currently only support 0
        if (0 !== ($_rev = $this->_readInt($be))) {
            return false;
        }
       
        // count of strings in this file
        $count = $this->_readInt($be);
        
        // offset of hashing table of the msgids
        $offset_original = $this->_readInt($be);
        // offset of hashing table of the msgstrs
        $offset_translat = $this->_readInt($be);
        
        // move to msgid hash table
        fseek($this->_handle, $offset_original);
        // read lengths and offsets of msgids
        $original = array();
        for ($i = 0; $i < $count; $i++) {
            $original[$i] = array(
                'length' => $this->_readInt($be),
                'offset' => $this->_readInt($be)
            );
        }
        
        // move to msgstr hash table
        fseek($this->_handle, $offset_translat);
        // read lengths and offsets of msgstrs
        $translat = array();
        for ($i = 0; $i < $count; $i++) {
            $translat[$i] = array(
                'length' => $this->_readInt($be),
                'offset' => $this->_readInt($be)
            );
        }
        
        // read all
        for ($i = 0; $i < $count; $i++) {
            $this->strings[$this->_readStr($original[$i])] = 
                $this->_readStr($translat[$i]);
        }
        
        // done
        @flock($this->_handle, LOCK_UN);
        @fclose($this->_handle);
        $this->_handle = null;
        
        // check for meta info
        if (isset($this->strings[''])) {
            $this->meta = parent::meta2array($this->strings['']);
            unset($this->strings['']);
        }
        
        return true;
    }
    
    /**
     * Save MO file
     *
     * @access  public
     * @return  mixed   Returns true on success or PEAR_Error on failure.
     * @param   string  $file
     */
    function save($file = null)
    {
        if (!isset($file)) {
            $file = $this->file;
        }
        
        // open MO file
        if (!is_resource($this->_handle = @fopen($file, 'wb'))) {
            return false;
        }
        // lock MO file exclusively
        if (!@flock($this->_handle, LOCK_EX)) {
            @fclose($this->_handle);
            return false;
        }
        
        // write magic number
        if ($this->writeBigEndian) {
            $this->_write(pack('c*', 0x95, 0x04, 0x12, 0xde));
        } else {
            $this->_write(pack('c*', 0xde, 0x12, 0x04, 0x95));
        }
        
        // write file format revision
        $this->_writeInt(0);
        
        $count = count($this->strings) + ($meta = (count($this->meta) ? 1 : 0));
        // write count of strings
        $this->_writeInt($count);
        
        $offset = 28;
        // write offset of orig. strings hash table
        $this->_writeInt($offset);
        
        $offset += ($count * 8);
        // write offset transl. strings hash table
        $this->_writeInt($offset);
        
        // write size of hash table (we currently ommit the hash table)
        $this->_writeInt(0);
        
        $offset += ($count * 8);
        // write offset of hash table
        $this->_writeInt($offset);
        
        // unshift meta info
        if ($meta) {
            $meta = '';
            foreach ($this->meta as $key => $val) {
                $meta .= $key . ': ' . $val . "\n";
            }
            $strings = array('' => $meta) + $this->strings;
        } else {
            $strings = $this->strings;
        }
        
        // write offsets for original strings
        foreach (array_keys($strings) as $o) {
            $len = strlen($o);
            $this->_writeInt($len);
            $this->_writeInt($offset);
            $offset += $len + 1;
        }
        
        // write offsets for translated strings
        foreach ($strings as $t) {
            $len = strlen($t);
            $this->_writeInt($len);
            $this->_writeInt($offset);
            $offset += $len + 1;
        }

        // write original strings
        foreach (array_keys($strings) as $o) {
            $this->_writeStr($o);
        }

        // write translated strings
        foreach ($strings as $t) {
            $this->_writeStr($t);
        }
        
        // done
        @flock($this->_handle, LOCK_UN);
        @fclose($this->_handle);
        return true;
    }
}