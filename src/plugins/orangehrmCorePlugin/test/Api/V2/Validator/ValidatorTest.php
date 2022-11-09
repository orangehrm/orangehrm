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

namespace OrangeHRM\Tests\Core\Api\V2\Validator;

use Generator;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Validator;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Api
 * @group Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @dataProvider validCasesDataProvider
     */
    public function testValidCases($values, $rules): void
    {
        $result = Validator::validate($values, $rules);

        $this->assertTrue($result);
    }

    /**
     * @return Generator
     */
    public function validCasesDataProvider(): Generator
    {
        yield [
            [
                "userName" => "Admin",
                "status" => "true",
            ],
            new ParamRuleCollection(
                new ParamRule(
                    "userName",
                    new Rule(Rules::STRING_VAL)
                ),
                new ParamRule(
                    "status",
                    new Rule(Rules::BOOL_VAL)
                )
            )
        ];
        $sortOrderRule = new ParamRule(
            CommonParams::PARAMETER_SORT_ORDER,
            new Rule(
                Rules::ONE_OF,
                [
                    new Rule(Rules::IN, [[ListSorter::ASCENDING, ListSorter::DESCENDING]]),
                    new Rule(Rules::NOT_REQUIRED),
                ]
            )
        );
        yield [
            [CommonParams::PARAMETER_SORT_ORDER => null],
            new ParamRuleCollection($sortOrderRule)
        ];
        yield [
            [CommonParams::PARAMETER_SORT_ORDER => "DESC"],
            new ParamRuleCollection($sortOrderRule)
        ];
        $statusRule = new ParamRule(
            "status",
            new Rule(Rules::BOOL_VAL),
            new Rule(Rules::REQUIRED),
        );
        yield [
            ["status" => "true"],
            new ParamRuleCollection($statusRule)
        ];
        yield [
            ["status" => "false"],
            new ParamRuleCollection($statusRule)
        ];
        yield [
            [
                "attachment" => [
                    "name" => "file.txt",
                    "type" => "text/plain",
                    "size" => "6",
                    "base64" => "dGVzdA0K"
                ]
            ],
            new ParamRuleCollection(
                new ParamRule("attachment")
            )
        ];
    }
}
