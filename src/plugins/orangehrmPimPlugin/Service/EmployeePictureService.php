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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Pim\Dao\EmployeePictureDao;
use Psr\Cache\InvalidArgumentException;

class EmployeePictureService
{
    use UserRoleManagerTrait;
    use CacheTrait;
    use ETagHelperTrait;

    public const PIM_EMP_PICTURE_CACHE_KEY_PREFIX = 'pim.emp_picture';

    public const ETAG_CACHE_KEY_SUFFIX = 'etag';

    /**
     * @var EmployeePictureDao|null
     */
    protected ?EmployeePictureDao $employeePictureDao = null;

    /**
     * @return EmployeePictureDao
     */
    public function getEmployeePictureDao(): EmployeePictureDao
    {
        if (!$this->employeePictureDao instanceof EmployeePictureDao) {
            $this->employeePictureDao = new EmployeePictureDao();
        }
        return $this->employeePictureDao;
    }

    /**
     * @param string $picture
     * @return int[]
     */
    public function pictureSizeAdjust($picture): array
    {
        list($imgWidth, $imgHeight) = getimagesizefromstring($picture);
        $newHeight = 0;
        $newWidth = 0;

        if ($imgHeight > 200 || $imgWidth > 200) {
            $propHeight = floor(($imgHeight / $imgWidth) * 200);
            $propWidth = floor(($imgWidth / $imgHeight) * 200);

            if ($propHeight <= 200) {
                $newHeight = $propHeight;
                $newWidth = 200;
            }

            if ($propWidth <= 200) {
                $newWidth = $propWidth;
                $newHeight = 200;
            }
        } else {
            if ($imgHeight <= 200) {
                $newHeight = $imgHeight;
            }

            if ($imgWidth <= 200) {
                $newWidth = $imgWidth;
            }
        }
        return [$newWidth, $newHeight];
    }

    /**
     * @param EmpPicture $employee
     * @return EmpPicture
     * @throws DaoException
     */
    public function saveEmployeePicture(EmpPicture $employee): EmpPicture
    {
        return $this->getEmployeePictureDao()->saveEmployeePicture($employee);
    }

    /**
     * @param int $empNumber
     * @return EmpPicture|null
     * @throws DaoException
     */
    public function getEmpPictureByEmpNumber(int $empNumber): ?EmpPicture
    {
        return $this->getEmployeePictureDao()->getEmpPictureByEmpNumber($empNumber);
    }

    /**
     * @param int $empNumber
     * @return EmpPicture|null
     * @throws DaoException
     */
    public function getAccessibleEmpPictureByEmpNumber(int $empNumber): ?EmpPicture
    {
        $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
        $self = $this->getUserRoleManagerHelper()->isSelfByEmpNumber($empNumber);
        if (in_array($empNumber, $accessibleEmpNumbers) || $self) {
            return $this->getEmpPictureByEmpNumber($empNumber);
        }
        return null;
    }

    /**
     * @param EmpPicture $empPicture
     * @return string
     */
    public function getETagByEmpPicture(EmpPicture $empPicture): string
    {
        $cacheKey = $this->generateEmpPictureETagCacheKey($empPicture->getEmployee()->getEmpNumber());
        $cacheItem = $this->getCache()->getItem($cacheKey);
        if (!$cacheItem->isHit()) {
            $this->getEmpPictureETagAlongWithCache($empPicture);
            $cacheItem = $this->getCache()->getItem($cacheKey);
        }
        return $cacheItem->get();
    }

    /**
     * @param int $empNumber
     */
    public function deleteETagByEmpNumber(int $empNumber): void
    {
        $this->getCache()->delete($this->generateEmpPictureETagCacheKey($empNumber));
    }

    /**
     * @param EmpPicture $empPicture
     * @return string
     */
    private function getEmpPictureETagAlongWithCache(EmpPicture $empPicture): string
    {
        return $this->getCache()->get(
            $this->generateEmpPictureETagCacheKey($empPicture->getEmployee()->getEmpNumber()),
            function () use ($empPicture) {
                return $this->generateEtag($empPicture->getDecorator()->getPicture());
            }
        );
    }

    /**
     * @param int $empNumber
     * @return string
     */
    private function generateEmpPictureETagCacheKey(int $empNumber): string
    {
        return self::PIM_EMP_PICTURE_CACHE_KEY_PREFIX . '.' . $empNumber . '.' . self::ETAG_CACHE_KEY_SUFFIX;
    }
}
