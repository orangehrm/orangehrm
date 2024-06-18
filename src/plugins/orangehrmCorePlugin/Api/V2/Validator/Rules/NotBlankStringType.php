<?php

namespace OrangeHRM\Core\Api\V2\Validator\Rules;

/**
 * Validates whether the input is a string and not only whitespace
 */
class NotBlankStringType extends AbstractRule
{
    public const SPACE_REGEX = '/^\s+$/'; // matches if the string consists of only whitespace

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        return is_string($input) && preg_match(self::SPACE_REGEX, $input) === 0;
    }
}
