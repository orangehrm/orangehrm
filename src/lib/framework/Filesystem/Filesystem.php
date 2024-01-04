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

namespace OrangeHRM\Framework\Filesystem;

use Symfony\Component\Filesystem\Exception\IOException;

class Filesystem extends \Symfony\Component\Filesystem\Filesystem
{
    /**
     * @inheritDoc
     */
    public function dumpFile(string $filename, $content): void
    {
        $dir = dirname($filename);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        }

        $error = null;
        set_error_handler(static function (int $errno, string $errStr) use (&$error) {
            $error = $errStr;
        });
        try {
            file_put_contents($filename, $content);
        } finally {
            restore_error_handler();
        }
        if ($error !== null) {
            throw new IOException("Failed to write file \"$filename\": " . $error, 0, null, $filename);
        }
    }
}
