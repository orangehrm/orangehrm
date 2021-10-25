<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

if (version_compare(PHP_VERSION, '7.1', '<')) {
    eval('namespace Respect\Validation\Rules; class Iterable extends IterableType {}');
}
