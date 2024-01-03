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

namespace OrangeHRM\CorporateBranding\Dto;

class ThemeImage
{
    /**
     * @var resource|null
     */
    private $content;

    private ?string $filename;

    private ?string $fileType;

    private ?int $fileSize;

    private ?string $contentString = null;

    /**
     * @param resource|string|null $content
     * @param string|null $filename
     * @param string|null $fileType
     * @param int|null $fileSize
     */
    public function __construct($content, ?string $filename, ?string $fileType, ?int $fileSize)
    {
        $this->content = $content;
        $this->filename = $filename;
        $this->fileType = $fileType;
        $this->fileSize = $fileSize;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        if (is_string($this->content)) {
            return $this->content;
        }
        if (is_null($this->contentString) && is_resource($this->content)) {
            $this->contentString = stream_get_contents($this->content);
        }
        return $this->contentString;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return is_null($this->content) && is_null($this->filename)
            && is_null($this->fileType) && is_null($this->fileSize);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'content' => $this->getContent(),
            'filename' => $this->getFilename(),
            'fileType' => $this->getFileType(),
            'fileSize' => $this->getFileSize(),
        ];
    }

    /**
     * @param array|null $array
     * @return self|null
     */
    public static function createFromArray(?array $array): ?self
    {
        if (is_null($array)) {
            return null;
        }
        return new self($array['content'], $array['filename'], $array['fileType'], $array['fileSize']);
    }
}
