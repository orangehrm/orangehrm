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

class I18NDao extends BaseDao
{
    /**
     * @param string $langCode
     * @param bool $onlyCustomized
     * @return I18NTranslate[]
     * @throws DaoException
     */
    public function getMessages(string $langCode, bool $onlyCustomized = true)
    {
        try {
            $q = Doctrine2::getEntityManager()->getRepository(I18NTranslate::class)->createQueryBuilder('t');
            $q->leftJoin('t.I18NLangString', 'ls')
                ->leftJoin('t.I18NLanguage', 'l')
                ->andWhere('l.code = :langCode')
                ->setParameter('langCode', $langCode);
            if ($onlyCustomized) {
                $q->andWhere('t.customized = ?', true);
            }
            return $q->getQuery()->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $langCode
     * @return I18NLanguage
     * @throws DaoException
     */
    public function getLanguageByCode(string $langCode)
    {
        try {
            return Doctrine2::getEntityManager()->getRepository(I18NLanguage::class)->findOneBy(['code' => $langCode]);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param ParameterObject $searchParams
     * @return I18NLanguage[]
     * @throws DaoException
     */
    public function searchLanguages(ParameterObject $searchParams)
    {
        try {
            $q = Doctrine2::getEntityManager()->getRepository(I18NLanguage::class)->createQueryBuilder('l');

            if (!empty($searchParams->getParameter('langCode'))) {
                $q->andWhere('l.code = :langCode');
                $q->setParameter('langCode', $searchParams->getParameter('langCode'));
            }
            if (is_bool($searchParams->getParameter('enabled'))) {
                $q->andWhere('l.enabled = :enabled');
                $q->setParameter('enabled', $searchParams->getParameter('enabled'));
            }
            if (is_bool($searchParams->getParameter('added'))) {
                $q->andWhere('l.added = :added');
                $q->setParameter('added', $searchParams->getParameter('added'));
            }

            if (!empty($searchParams->getParameter('sortField'))) {
                if (in_array($searchParams->getParameter('sortOrder'), ['ASC', 'DESC'])) {
                    $q->addOrderBy(
                        $searchParams->getParameter('sortField'),
                        $searchParams->getParameter('sortOrder')
                    );
                } else {
                    $q->addOrderBy($searchParams->getParameter('sortField'));
                }
            }
            return $q->getQuery()->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $id
     * @return null|I18NLanguage
     * @throws DaoException
     */
    public function getLanguageById(string $id)
    {
        try {
            return Doctrine2::getEntityManager()->getRepository(I18NLanguage::class)->find($id);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param array $currentSourceIds
     * @param int $sourceId
     * @return int
     * @throws DaoException
     */
    public function deleteLangStrings(array $currentSourceIds, int $sourceId)
    {
        try {
            $q = Doctrine2::getEntityManager()->createQueryBuilder();
            $q->delete(I18NLangString::class, 'ls')
                ->andWhere($q->expr()->notIn('ls.unitId', $currentSourceIds))
                ->andWhere('ls.sourceId = :sourceId')
                ->setParameter('sourceId', $sourceId);
            return $q->getQuery()->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param int $sourceId
     * @return I18NLangString[]
     * @throws DaoException
     */
    public function getLangStringsBySourceId(int $sourceId)
    {
        try {
            $q = Doctrine2::getEntityManager()->getRepository(I18NLangString::class)->createQueryBuilder('ls')
                ->andWhere('ls.sourceId = :sourceId')
                ->setParameter('sourceId', $sourceId);
            return $q->getQuery()->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $source
     * @return I18NSource
     * @throws DaoException
     */
    public function getI18NSource(string $source)
    {
        try {
            return Doctrine2::getEntityManager()->getRepository(I18NSource::class)->findOneBy(['source' => $source]);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param I18NSource $i18NSource
     * @return I18NSource
     * @throws Exception
     */
    public function saveI18NSource(I18NSource $i18NSource)
    {
        Doctrine2::getEntityManager()->persist($i18NSource);
        Doctrine2::getEntityManager()->flush();
        return $i18NSource;
    }

    /**
     * @param I18NSource $i18NSource
     */
    public function deleteI18NSource(I18NSource $i18NSource)
    {
        Doctrine2::getEntityManager()->remove($i18NSource);
        Doctrine2::getEntityManager()->flush();
    }

    /**
     * @return I18NSource[]
     * @throws DaoException
     */
    public function getAllI18NSources()
    {
        try {
            return Doctrine2::getEntityManager()->getRepository(I18NSource::class)->findAll();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param I18NLangString $i18NLangString
     * @return I18NLangString
     * @throws Exception
     */
    public function saveI18NLangString(I18NLangString $i18NLangString)
    {
        Doctrine2::getEntityManager()->persist($i18NLangString);
        Doctrine2::getEntityManager()->flush();
        return $i18NLangString;
    }

    /**
     * @param int $langStringId
     * @param int $langId
     * @return I18NTranslate|null
     * @throws DaoException
     */
    public function getI18NTranslate(int $langStringId, int $langId)
    {
        try {
            $q = Doctrine2::getEntityManager()->getRepository(I18NTranslate::class)->createQueryBuilder('t')
                ->andWhere('t.langStringId = :langStringId')
                ->setParameter('langStringId', $langStringId)
                ->andWhere('t.languageId = :languageId')
                ->setParameter('languageId', $langId);
            return $q->getQuery()->getOneOrNullResult();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $id
     * @return null|I18NTranslate
     * @throws DaoException
     */
    public function getI18NTranslateById(string $id)
    {
        try {
            return Doctrine2::getEntityManager()->getRepository(I18NTranslate::class)->find($id);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param I18NTranslate $i18NTranslate
     * @return I18NTranslate
     * @throws Exception
     */
    public function saveI18NTranslate(I18NTranslate $i18NTranslate)
    {
        Doctrine2::getEntityManager()->persist($i18NTranslate);
        Doctrine2::getEntityManager()->flush();
        return $i18NTranslate;
    }

    /**
     * @param I18NLanguage $i18NLanguage
     * @return I18NLanguage
     * @throws Exception
     */
    public function saveI18NLanguage(I18NLanguage $i18NLanguage)
    {
        Doctrine2::getEntityManager()->persist($i18NLanguage);
        Doctrine2::getEntityManager()->flush();
        return $i18NLanguage;
    }

    /**
     * @param ParameterObject $searchParams
     * @param bool $getCount
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function searchTranslations(ParameterObject $searchParams, bool $getCount = false)
    {
        try {
            $q = Doctrine2::getEntityManager()->getRepository(I18NTranslate::class)->createQueryBuilder('t')
                ->leftJoin('t.I18NLangString', 'ls')
                ->leftJoin('ls.I18NGroup', 'g')
                ->leftJoin('t.I18NLanguage', 'l');

            if (!empty($searchParams->getParameter('langCode'))) {
                $q->andWhere('l.code = :langCode');
                $q->setParameter('langCode', $searchParams->getParameter('langCode'));
            }
            if (is_bool($searchParams->getParameter('translated'))) {
                $q->andWhere('t.translated = :translated');
                $q->setParameter('translated', $searchParams->getParameter('translated'));
            }
            if (!empty($searchParams->getParameter('sourceText'))) {
                $q->andWhere('ls.value LIKE :sourceText');
                $q->setParameter('sourceText', '%' . $searchParams->getParameter('sourceText') . '%');
            }
            if (!empty($searchParams->getParameter('translatedText'))) {
                $q->andWhere('t.value LIKE :translatedText');
                $q->setParameter('translatedText', '%' . $searchParams->getParameter('translatedText') . '%');
            }
            if (!empty($searchParams->getParameter('group'))) {
                $q->andWhere('g.name = :group');
                $q->setParameter('group', $searchParams->getParameter('group'));
            }

            if ($getCount) {
                return $q->select('count(t.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }

            if (!empty($searchParams->getParameter('sortField'))) {
                if (in_array($searchParams->getParameter('sortOrder'), ['ASC', 'DESC'])) {
                    $q->addOrderBy(
                        $searchParams->getParameter('sortField'),
                        $searchParams->getParameter('sortOrder')
                    );
                } else {
                    $q->addOrderBy($searchParams->getParameter('sortField'));
                }
            }
            if (!empty($searchParams->getParameter('offset'))) {
                $q->setFirstResult($searchParams->getParameter('offset'));
            }
            if (!empty($searchParams->getParameter('limit'))) {
                $q->setMaxResults($searchParams->getParameter('limit'));
            }
            return $q->getQuery()->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return Doctrine_Collection|I18NGroup[]
     * @throws DaoException
     */
    public function getI18NGroups()
    {
        try {
            return Doctrine2::getEntityManager()->getRepository(I18NGroup::class)->findAll();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
