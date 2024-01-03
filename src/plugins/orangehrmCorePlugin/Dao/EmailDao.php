<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Core\Dao;

use OrangeHRM\Entity\Email;
use OrangeHRM\Entity\EmailTemplate;

class EmailDao extends BaseDao
{
    /**
     * @param string $name
     * @return Email|null
     */
    public function getEmailByName(string $name): ?Email
    {
        return $this->getRepository(Email::class)->findOneBy(['name' => $name]);
    }

    /**
     * Get all matching email templates for the given email
     *
     * fetches templates for given role and records for which role is null.
     *
     * @param string $name Email Name
     * @param string $locale locale
     * @param string|null $recipientRole recipient role
     * @param string|null $performerRole performer role
     * @return EmailTemplate[]
     */
    public function getEmailTemplateMatches(
        string $name,
        string $locale,
        ?string $recipientRole,
        ?string $performerRole
    ): array {
        $q = $this->createQueryBuilder(EmailTemplate::class, 't')
            ->leftJoin('t.email', 'e')
            ->andWhere('e.name = :name')
            ->setParameter('name', $name)
            ->andWhere('t.locale = :locale')
            ->setParameter('locale', $locale);
        if (empty($recipientRole)) {
            $q->andWhere($q->expr()->isNull('t.recipientRole'));
        } else {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->isNull('t.recipientRole'),
                    $q->expr()->eq('t.recipientRole', ':recipientRole'),
                )
            );
            $q->setParameter('recipientRole', $recipientRole);
        }

        if (empty($performerRole)) {
            $q->andWhere($q->expr()->isNull('t.performerRole'));
        } else {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->isNull('t.performerRole'),
                    $q->expr()->eq('t.performerRole', ':performerRole'),
                )
            );
            $q->setParameter('performerRole', $performerRole);
        }
        return $q->getQuery()->execute();
    }
}
