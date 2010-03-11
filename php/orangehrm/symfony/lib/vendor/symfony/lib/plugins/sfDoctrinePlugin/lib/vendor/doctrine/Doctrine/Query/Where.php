<?php
/*
 *  $Id: Where.php 5801 2009-06-02 17:30:27Z piccoloprincipe $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Query_Where
 *
 * @package     Doctrine
 * @subpackage  Query
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 5801 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Query_Where extends Doctrine_Query_Condition
{
    public function load($where) 
    {
        $where = $this->_tokenizer->bracketTrim(trim($where));
        $conn  = $this->query->getConnection();
        $terms = $this->_tokenizer->sqlExplode($where);  

        if (count($terms) > 1) {
            if (substr($where, 0, 6) == 'EXISTS') {
                return $this->parseExists($where, true);
            } elseif (substr($where, 0, 10) == 'NOT EXISTS') {
                return $this->parseExists($where, false);
            }
        }

        if (count($terms) < 3) {
            $terms = $this->_tokenizer->sqlExplode($where, array('=', '<', '<>', '>', '!='));
        }

        if (count($terms) > 1) {
            $first = array_shift($terms);
            $value = array_pop($terms);
            $operator = trim(substr($where, strlen($first), -strlen($value)));
            $table = null;
            $field = null;

            if (strpos($first, "'") === false && strpos($first, '(') === false) {
                // normal field reference found
                $a = explode('.', $first);
        
                $field = array_pop($a);
                $reference = implode('.', $a);
                
                if (empty($reference)) {
                    $map = $this->query->getRootDeclaration();  
                    
                    $alias = $this->query->getTableAlias($this->query->getRootAlias());
                    $table = $map['table'];
                } else {
                    $map = $this->query->load($reference, false);
    
                    $alias = $this->query->getTableAlias($reference);
                    $table = $map['table'];
                }
            }
            $first = $this->query->parseClause($first);
            
            $sql = $first . ' ' . $operator . ' ' . $this->parseValue($value, $table, $field);
        
            return $sql;  
        } else {
            return $where;
        }
    }

    public function parseValue($value, Doctrine_Table $table = null, $field = null)
    {
        $conn = $this->query->getConnection();

        // If value is contained in paranthesis
        if (substr($value, 0, 1) == '(') {
            // trim brackets
            $trimmed = $this->_tokenizer->bracketTrim($value);

            // If subquery found which begins with FROM and SELECT
            // FROM User u WHERE u.id IN(SELECT u.id FROM User u WHERE u.id = 1)
            if (substr($trimmed, 0, 4) == 'FROM' ||
                substr($trimmed, 0, 6) == 'SELECT') {

                // subquery found
                $q     = $this->query->createSubquery()->parseQuery($trimmed, false);
                $sql   = $q->getSql();
                $value = '(' . $sql . ')';
                $q->free();
            // If custom sql for custom subquery
            // You can specify SQL: followed by any valid sql expression
            // FROM User u WHERE u.id = SQL:(select id from user where id = 1)
            } elseif (substr($trimmed, 0, 4) == 'SQL:') {
                $value = '(' . substr($trimmed, 4) . ')';
            // simple in expression found
            } else {
                $e = $this->_tokenizer->sqlExplode($trimmed, ',');

                $value = array();

                $index = false;

                foreach ($e as $part) {
                    $value[] = $this->parseLiteralValue($part);
                }

                $value = '(' . implode(', ', $value) . ')';
            }
        } else {
            $value = $this->parseLiteralValue($value);
        }
        return $value;
    }

    /**
     * parses an EXISTS expression
     *
     * @param string $where         query where part to be parsed
     * @param boolean $negation     whether or not to use the NOT keyword
     * @return string
     */
    public function parseExists($where, $negation)
    {
        $operator = ($negation) ? 'EXISTS' : 'NOT EXISTS';

        $pos = strpos($where, '(');

        if ($pos == false) {
            throw new Doctrine_Query_Exception('Unknown expression, expected a subquery with () -marks');
        }

        $sub = $this->_tokenizer->bracketTrim(substr($where, $pos));

        $q = $this->query->createSubquery()->parseQuery($sub, false);
        $sql = $q->getSql();
        $q->free();
        return $operator . ' (' . $sql . ')';
    }
}
