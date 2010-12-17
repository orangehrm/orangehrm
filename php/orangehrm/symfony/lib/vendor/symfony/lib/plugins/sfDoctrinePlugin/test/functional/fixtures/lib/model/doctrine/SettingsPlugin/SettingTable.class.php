<?php


class SettingTable extends PluginSettingTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Setting');
    }
}