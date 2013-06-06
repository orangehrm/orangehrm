<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Description of EmailDao
 *
 */
class EmailDao {

    public function getEmailByName($name) {

        try {

            $query = Doctrine_Query::create()
                    ->select('e.*, t.*, p.*')
                    ->from("Email e")
                    ->leftJoin('e.EmailTemplate t')
                    ->leftJoin('e.EmailProcessor p')
                    ->where("e.name = ?", $name);

            return $query->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     * Get all matching email templates for the given email
     * 
     * fetches templates for given role and records for which role is null.
     * 
     * @param string $name Email Name
     * @param string $locale locale
     * @param string $recipientRole recipient role
     * @param string $performerRole performer role
     */
    public function getEmailTemplateMatches($name, $locale, $recipientRole, $performerRole) {
        try {

            $query = Doctrine_Query::create()
                    ->from("EmailTemplate t")
                    ->leftJoin('t.Email e')
                    ->where("e.name = ?", $name)
                    ->andWhere('t.locale = ?', $locale);
            
            if (empty($recipientRole)) {
                $query->andWhere('t.recipient_role IS NULL');
            } else {
                $query->andWhere('(t.recipient_role IS NULL OR t.recipient_role = ?)', $recipientRole);
            }
            
            if (empty($performerRole)) {
                $query->andWhere('t.performer_role IS NULL');
            } else {
                $query->andWhere('(t.performer_role IS NULL OR t.performer_role = ?)', $performerRole);
            }            
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }        
    }

}

