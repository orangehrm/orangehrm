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

namespace OrangeHRM\ORM\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class TimeDiff extends FunctionNode
{
    /**
     * @var string
     */
    public $dateTime1;

    /**
     * @var string
     */
    public $dateTime2;

    /**
     * @var string
     */
    public $type;

    /**
     * @param Parser $parser
     * @return void
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateTime1 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->dateTime2 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->type = $parser->Literal()->value;
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        switch ($this->type) {
            case "minute":
                $time = 60;
                break;
            case "hour":
                $time = 3600;
                break;
            default:
                $time = 1;
        }
        return 'ROUND(TIME_TO_SEC(TIMEDIFF(' .
            $this->dateTime1->dispatch($sqlWalker) . ', ' .
            $this->dateTime2->dispatch($sqlWalker) .
            '))/' . $time . ', 0)';
    }
}
