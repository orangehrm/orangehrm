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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmpPictureDecorator;

/**
 * @method EmpPictureDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_picture")
 * @ORM\Entity
 */
class EmpPicture
{
    use DecoratorTrait;

    public const ALLOWED_IMAGE_TYPES = [
        'image/gif',
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/png',
        'image/x-png'
    ];
    public const ALLOWED_IMAGE_EXTENSIONS = [
        'gif',
        'jpeg',
        'jpg',
        'jfif',
        'png',
    ];

    /**
     * @var Employee
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="empPicture", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var string|resource
     *
     * @ORM\Column(name="epic_picture", type="blob", nullable=true)
     */
    private $picture = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="epic_filename", type="string", length=100, nullable=true)
     */
    private ?string $filename = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="epic_type", type="string", length=50, nullable=true)
     */
    private ?string $fileType = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="epic_file_size", type="string", length=20, nullable=true)
     */
    private ?string $size = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="epic_file_width", type="string", length=20, nullable=true)
     */
    private ?string $width = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="epic_file_height", type="string", length=20, nullable=true)
     */
    private ?string $height = null;

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return string|resource
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string|null $fileType
     */
    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param string|null $size
     */
    public function setSize(?string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string|null
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * @param string|null $width
     */
    public function setWidth(?string $width): void
    {
        $this->width = $width;
    }

    /**
     * @return string|null
     */
    public function getHeight(): ?string
    {
        return $this->height;
    }

    /**
     * @param string|null $height
     */
    public function setHeight(?string $height): void
    {
        $this->height = $height;
    }
}
