<?php

class SessionAttributeSetterListener extends Doctrine_EventListener
{
    /**
     * @param Doctrine_Connection $connection
     * @throws Exception
     */
    public function onOpen(Doctrine_Connection $connection)
    {
        try {
            $connection->exec('SET SESSION sql_mode = ""; ');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}