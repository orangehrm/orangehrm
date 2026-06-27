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

namespace OrangeHRM\Tests\I18N\Migration;

use OrangeHRM\Tests\Util\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Guards against the lang-string collision that hid the "Employees on Leave Today"
 * dashboard widget title (see OHRM5X-2654).
 *
 * Root cause: {@see \OrangeHRM\Installer\Util\V1\LangStringHelper::insertOrUpdateLangStrings()}
 * resolves an existing row by its VALUE alone (group-agnostic). When a later migration
 * seeds a different unitId/group with a value that already exists, the helper updates the
 * existing row's unit_id + group_id instead of inserting a new one — silently re-homing the
 * original string into another group and leaving the original key unresolved (so the frontend
 * renders the raw `$t()` key).
 *
 * Until that helper is made group-aware, the same English value must not be seeded under more
 * than one group. This test scans every migration lang-string YAML and fails if it is.
 *
 * @group I18N
 * @group Migration
 */
class LangStringGroupUniquenessTest extends TestCase
{
    /**
     * Pre-existing cross-group duplicates that predate this guard. These are tolerated only
     * because every consumer happens to reference the surviving row; do NOT add new entries —
     * use a distinct value instead. Listed here so genuinely new collisions still fail the test.
     */
    private const KNOWN_CROSS_GROUP_DUPLICATES = [
        // unitId 'amount' in general/claim/pim — all collapse to a single "Amount" row.
        'Amount',
    ];

    /**
     * @return string Absolute path to installer/Migration.
     */
    private function getMigrationRoot(): string
    {
        return realpath(__DIR__ . '/../../../../../installer/Migration');
    }

    public function testNoLangStringValueIsSharedAcrossGroups(): void
    {
        $migrationRoot = $this->getMigrationRoot();
        $this->assertNotFalse($migrationRoot, 'Could not locate installer/Migration directory');

        $files = glob($migrationRoot . '/*/lang-string/*.yaml');
        $this->assertNotEmpty($files, 'No lang-string YAML files found to scan');

        // value => [ group => list of "group.unitId (file)" occurrences ]
        $valueToGroups = [];
        foreach ($files as $file) {
            $group = basename($file, '.yaml');
            $relativeFile = ltrim(str_replace($migrationRoot, '', $file), '/');
            $parsed = Yaml::parseFile($file);
            foreach ($parsed['langStrings'] ?? [] as $langString) {
                if (!isset($langString['value'], $langString['unitId'])) {
                    continue;
                }
                $value = $langString['value'];
                $valueToGroups[$value][$group][] = "$group.{$langString['unitId']} ($relativeFile)";
            }
        }

        $violations = [];
        foreach ($valueToGroups as $value => $groups) {
            if (count($groups) <= 1) {
                continue;
            }
            if (in_array($value, self::KNOWN_CROSS_GROUP_DUPLICATES, true)) {
                continue;
            }
            $occurrences = array_merge(...array_values($groups));
            $violations[] = sprintf("  '%s' is seeded in %d groups:\n    - %s", $value, count($groups), implode("\n    - ", $occurrences));
        }

        $this->assertSame(
            [],
            $violations,
            "The same lang-string value is seeded under more than one group. Because LangStringHelper "
            . "matches existing strings by value alone, the later seed silently re-homes the earlier one into "
            . "another group, leaving its key unresolved. Give each occurrence a distinct value:\n"
            . implode("\n", $violations)
        );
    }
}
