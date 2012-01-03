<?php
/*
 *  $Id$
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
 * <http://www.doctrine-project.org>.
 */

/**
 * Extended version of Doctrine_Hydrator_ScalarDriver, passes its _gatherRowData function a value of false for $aliasPrefix in order to cause it to generate the sorts of array keys one would see in a HYDRATE_ARRAY type return.
 * Note: This hydrator will have issues with fields in the return that have the same name (such as 2 fields each called id) -- the second field value will overwrite the first field.
 * @package     Doctrine
 * @subpackage  Hydrate
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.2.3
 * @version     $Revision$
 * @author      Will Ferrer
 */
class Doctrine_Hydrator_ArrayShallowDriver extends Doctrine_Hydrator_ScalarDriver
{
    public function hydrateResultSet($stmt)
    {
        $cache = array();
        $result = array();
        while ($data = $stmt->fetch(Doctrine_Core::FETCH_ASSOC)) {
            $result[] = $this->_gatherRowData($data, $cache, false);
        }
        return $result;
    }
}