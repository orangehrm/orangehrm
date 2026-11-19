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

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use InvalidArgumentException;

/**
 * The application's base URL: scheme, host, optional port and the path before the application routes, i.e. the
 * address shown before `/auth/login` (with or without `index.php`, depending on the deployment),
 * e.g. `https://hr.example.com/orangehrm/index.php`. Stored in `hs_hr_config`
 * as `instance.base_url` and used where an absolute link leaves the request/response cycle (e.g. emails).
 */
class InstanceBaseUrl
{
    public const EXAMPLE = 'https://hr.example.com/orangehrm/index.php';
    public const DESCRIPTION = 'the OrangeHRM address as shown in the browser before /auth/login, e.g. ' . self::EXAMPLE . ' (internationalised domain names in punycode form, xn--...)';

    /**
     * Validates the value and returns it in canonical form, or null if it cannot be used as a base URL.
     *
     * Validation: an absolute http(s) URL (PHP's FILTER_VALIDATE_URL) without credentials, port 0, query or fragment,
     * so a mistyped value counts as "not configured" (no reset email) rather than producing broken links.
     *
     * Canonical form (RFC 3986 section 6, via Guzzle's UriNormalizer): lower-case scheme and host, default port
     * removed, dot segments removed, percent-encoding normalized, and no trailing slash. Symfony's
     * `RequestContext::fromUri()` reads the port before lower-casing the scheme (`HTTP://host:8080` would lose
     * the port), and the generated route path is appended directly to the base URL.
     *
     * @param string|null $url
     * @return string|null
     */
    public static function normalize(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }
        $url = trim($url);
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false
            || preg_match('#^https?://([^/?\#]*)#i', $url, $matches) !== 1
            || strpos($matches[1], '@') !== false
        ) {
            return null;
        }
        // Lower-case scheme and authority first: Guzzle only recognises lower-case IPv6 hex digits
        $url = strtolower(substr($url, 0, strlen($matches[0]))) . substr($url, strlen($matches[0]));
        try {
            $uri = new Uri($url);
        } catch (InvalidArgumentException $e) {
            return null;
        }
        if ($uri->getHost() === ''
            // FILTER_VALIDATE_URL lets the last label of a host name end with a hyphen (PHP < 8.x even without a
            // trailing dot); host names do not, and the installer screens reject it
            || preg_match('/-\.?$/', $uri->getHost()) === 1
            || $uri->getPort() === 0
            || $uri->getQuery() !== ''
            || $uri->getFragment() !== ''
            || strpos($url, '?') !== false
            || strpos($url, '#') !== false
        ) {
            return null;
        }
        $uri = UriNormalizer::normalize(
            $uri,
            UriNormalizer::PRESERVING_NORMALIZATIONS | UriNormalizer::REMOVE_DOT_SEGMENTS
        );
        $normalized = rtrim((string)$uri, '/');

        // The result must itself be a valid URL (normalize() is idempotent)
        return filter_var($normalized, FILTER_VALIDATE_URL) === false ? null : $normalized;
    }
}
