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

namespace OrangeHRM\ToDo\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\ToDo;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ToDo\Dto\ToDoSearchFilterParams;

class ToDoDao extends BaseDao
{
    /**
     * @param ToDo $toDo
     * @return ToDo
     * @throws DaoException
     */
    public function saveTodo(ToDo $toDo): ToDo
    {
        try {
            $this->persist($toDo);
            return $toDo;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $id
     * @return ToDo|null
     * @throws DaoException
     */
    public function getTodo(int $id): ?ToDo
    {
        try {
            return $this->getRepository(ToDo::class)->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ToDoSearchFilterParams $toDoSearchFilterParams
     * @return ToDo[]
     * @throws DaoException
     */
    public function getTodos(ToDoSearchFilterParams $toDoSearchFilterParams): array
    {
        try {
            return $this->getTodosPaginator($toDoSearchFilterParams)->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ToDoSearchFilterParams $toDoSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getTodosCount(ToDoSearchFilterParams $toDoSearchFilterParams): int
    {
        try {
            return $this->getTodosPaginator($toDoSearchFilterParams)->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ToDoSearchFilterParams $toDoSearchFilterParams
     * @return Paginator
     */
    private function getTodosPaginator(ToDoSearchFilterParams $toDoSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(ToDo::class, 't');
        $this->setSortingAndPaginationParams($q, $toDoSearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param int[] $ids
     * @return int
     * @throws DaoException
     */
    public function deleteTodos(array $ids): int
    {
        try {
            $q = $this->createQueryBuilder(ToDo::class, 't');
            $q->delete();
            $q->where($q->expr()->in('t.id', ':ids'))
                ->setParameter('ids', $ids);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
