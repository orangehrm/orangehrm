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

namespace OrangeHRM\Core\Utility;

use phpseclib3\Crypt\AES;
use RuntimeException;

class Cryptographer
{
    private const KEY_LENGTH = 128;
    private const LEGACY_MODE = 'ecb';

    /**
     * Prefix written for new AES-256-GCM ciphertext (base64 payload follows).
     */
    private const GCM_STORAGE_PREFIX = 'GCMAES256.';
    private const GCM_NONCE_LENGTH = 12;
    private const GCM_TAG_LENGTH = 16;

    private string $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Encrypt using AES-256-GCM (random nonce per value, authentication tag).
     * Legacy AES-128-ECB ciphertext is still accepted by decrypt().
     *
     * @param string|null $value
     * @return string|null
     */
    public function encrypt(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        $binaryKey = $this->deriveGcmKey();
        $iv = random_bytes(self::GCM_NONCE_LENGTH);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $value,
            'aes-256-gcm',
            $binaryKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::GCM_TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new RuntimeException('AES-256-GCM encryption failed');
        }

        $payload = $iv . $ciphertext . $tag;

        return self::GCM_STORAGE_PREFIX . base64_encode($payload);
    }

    /**
     * Decrypt AES-256-GCM ciphertext or legacy AES-128-ECB hex.
     *
     * @param string|null $encryptedValue
     * @return string|null
     */
    public function decrypt(?string $encryptedValue): ?string
    {
        if (empty($encryptedValue)) {
            return $encryptedValue;
        }

        if (strpos($encryptedValue, self::GCM_STORAGE_PREFIX) === 0) {
            return $this->decryptGcm(substr($encryptedValue, strlen(self::GCM_STORAGE_PREFIX)));
        }

        $encryptedValue = pack('H*', $encryptedValue);

        return $this->decryptLegacyEcb($encryptedValue);
    }

    private function decryptGcm(string $base64Payload): string
    {
        $binary = base64_decode($base64Payload, true);
        if ($binary === false) {
            throw new RuntimeException('Invalid GCM ciphertext encoding');
        }

        $minLen = self::GCM_NONCE_LENGTH + self::GCM_TAG_LENGTH + 1;
        if (strlen($binary) < $minLen) {
            throw new RuntimeException('Invalid GCM ciphertext length');
        }

        $iv = substr($binary, 0, self::GCM_NONCE_LENGTH);
        $tag = substr($binary, -self::GCM_TAG_LENGTH);
        $ciphertext = substr($binary, self::GCM_NONCE_LENGTH, -self::GCM_TAG_LENGTH);

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->deriveGcmKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            ''
        );

        if ($plaintext === false) {
            throw new RuntimeException('AES-256-GCM decryption failed');
        }

        return $plaintext;
    }

    /** Legacy AES-128-ECB decryption for existing database values only. */
    private function decryptLegacyEcb(string $encryptedValue): string
    {
        $aes = new AES(self::LEGACY_MODE);

        $aes->setKeyLength(self::KEY_LENGTH);
        $aes->setKey($this->generateKey());

        return $aes->decrypt($encryptedValue);
    }

    /**
     * 32-byte key for AES-256-GCM, derived from the same material as the legacy 128-bit key.
     *
     * @return string
     */
    private function deriveGcmKey(): string
    {
        return hash('sha256', $this->generateKey() . "\x00OrangeHRM-GCM-v2", true);
    }

    /**
     * @return string
     */
    private function generateKey(): string
    {
        $generatedKey = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

        for ($a = 0; $a < strlen($this->key); $a++) {
            $generatedKey[$a % 16] = chr(ord($generatedKey[$a % 16]) ^ ord($this->key[$a]));
        }

        return $generatedKey;
    }
}
