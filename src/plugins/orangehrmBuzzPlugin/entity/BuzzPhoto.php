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
use OrangeHRM\Entity\Decorator\BuzzPhotoDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method BuzzPhotoDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_buzz_photo")
 * @ORM\Entity
 */
class BuzzPhoto
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
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var BuzzPost
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\BuzzPost", inversedBy="photos")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private BuzzPost $post;

    /**
     * @var string|resource
     *
     * @ORM\Column(name="photo", type="blob", nullable=true)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=100, nullable=true)
     */
    private string $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=50, nullable=true)
     */
    private string $fileType;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=20, nullable=true)
     */
    private string $size;

    /**
     * @var string
     *
     * @ORM\Column(name="width", type="string", length=20, nullable=true)
     */
    private string $width;

    /**
     * @var string
     *
     * @ORM\Column(name="height", type="string", length=20, nullable=true)
     */
    private string $height;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return BuzzPost
     */
    public function getPost(): BuzzPost
    {
        return $this->post;
    }

    /**
     * @param BuzzPost $post
     */
    public function setPost(BuzzPost $post): void
    {
        $this->post = $post;
    }

    /**
     * @return resource|string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param resource|string $photo
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType(string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth(string $width): void
    {
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function getHeight(): string
    {
        return $this->height;
    }

    /**
     * @param string $height
     */
    public function setHeight(string $height): void
    {
        $this->height = $height;
    }
}
