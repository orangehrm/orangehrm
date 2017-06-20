<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

/**
 * A simple wrapper around the php password_hash and password_verify functions
 */
class PasswordHash {

    const ALGORITHM = PASSWORD_BCRYPT;

    // 2^12 iterations
    const COST = 12;

    /**
     * Create password hash
     *
     * @param string $password Password
     * @return string
     */
    public function hash($password) {
        $options = [
            'cost' => self::COST
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * Verify password
     *
     * @param string $password Password
     * @param string $hash Hash
     * @return boolean
     */
    public function verify($password, $hash) {
        return password_verify($password, $hash);
    }

}