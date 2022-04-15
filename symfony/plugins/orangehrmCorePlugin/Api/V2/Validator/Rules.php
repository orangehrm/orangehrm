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

namespace OrangeHRM\Core\Api\V2\Validator;

use OrangeHRM\Core\Api\V2\Validator\Rules as OHRMRules;
use Respect\Validation\Rules as RespectRules;

final class Rules
{
    public const ALL_OF = OHRMRules\Composite\AllOf::class;
    public const ANY_OF = OHRMRules\Composite\AnyOf::class;
    public const NONE_OF = OHRMRules\Composite\NoneOf::class;
    public const ONE_OF = OHRMRules\Composite\OneOf::class;

    public const REQUIRED = OHRMRules\Required::class;
    public const NOT_REQUIRED = OHRMRules\NotRequired::class;
    public const ZERO_OR_POSITIVE = OHRMRules\ZeroOrPositive::class;
    public const IN_ACCESSIBLE_EMP_NUMBERS = OHRMRules\InAccessibleEmpNumbers::class;
    public const BASE_64_ATTACHMENT = OHRMRules\Base64Attachment::class;
    public const COUNTRY_CODE = OHRMRules\CountryCode::class;
    public const PROVINCE_CODE = OHRMRules\ProvinceCode::class;
    public const CURRENCY = OHRMRules\Currency::class;
    public const API_DATE = OHRMRules\ApiDate::class;
    public const EACH = OHRMRules\Each::class;
    public const NOT_IN = OHRMRules\NotIn::class;
    public const LESS_THAN_OR_EQUAL = OHRMRules\LessThanOrEqual::class;
    public const ENTITY_ID_EXISTS = OHRMRules\EntityIdExists::class;
    public const ENTITY_UNIQUE_PROPERTY = OHRMRules\EntityUniqueProperty::class;
    public const IN_ACCESSIBLE_ENTITY_ID = OHRMRules\InAccessibleEntityId::class;
    public const TIMEZONE_OFFSET = OHRMRules\TimezoneOffset::class;
    public const TIMEZONE_NAME = OHRMRules\TimezoneName::class;
    public const EMAIL = OHRMRules\Email::class;
    public const PHONE = OHRMRules\Phone::class;
    public const PASSWORD = OHRMRules\Password::class;

    public const ALNUM = RespectRules\Alnum::class;
    public const ALPHA = RespectRules\Alpha::class;
    public const ALWAYS_INVALID = RespectRules\AlwaysInvalid::class;
    public const ALWAYS_VALID = RespectRules\AlwaysValid::class;
    public const ARRAY_TYPE = RespectRules\ArrayType::class;
    public const ARRAY_VAL = RespectRules\ArrayVal::class;
    public const ATTRIBUTE = RespectRules\Attribute::class;
    public const BASE = RespectRules\Base::class;
    public const BASE_64 = RespectRules\Base64::class;
    public const BETWEEN = RespectRules\Between::class;
    public const BOOL_TYPE = RespectRules\BoolType::class;
    public const BOOL_VAL = RespectRules\BoolVal::class;
    public const BSN = RespectRules\Bsn::class;
    public const CALL = RespectRules\Call::class;
    public const CALLABLE_TYPE = RespectRules\CallableType::class;
    public const CALLBACK = RespectRules\Callback::class;
    public const CHARSET = RespectRules\Charset::class;
    public const CNH = RespectRules\Cnh::class;
    public const CNPJ = RespectRules\Cnpj::class;
    public const CONSONANT = RespectRules\Consonant::class;
    public const CONTAINS = RespectRules\Contains::class;
    public const CONTAINS_ANY = RespectRules\ContainsAny::class;
    public const CONTROL = RespectRules\Control::class;
    public const COUNTABLE = RespectRules\Countable::class;
    public const CPF = RespectRules\Cpf::class;
    public const CREDIT_CARD = RespectRules\CreditCard::class;
    public const DATE = RespectRules\Date::class;
    public const DATE_TIME = RespectRules\DateTime::class;
    public const DECIMAL = RespectRules\Decimal::class;
    public const DIGIT = RespectRules\Digit::class;
    public const DIRECTORY = RespectRules\Directory::class;
    public const DOMAIN = RespectRules\Domain::class;
    public const ENDS_WITH = RespectRules\EndsWith::class;
    public const EQUALS = RespectRules\Equals::class;
    public const EQUIVALENT = RespectRules\Equivalent::class;
    public const EVEN = RespectRules\Even::class;
    public const EXECUTABLE = RespectRules\Executable::class;
    public const EXISTS = RespectRules\Exists::class;
    public const EXTENSION = RespectRules\Extension::class;
    public const FACTOR = RespectRules\Factor::class;
    public const FALSE_VAL = RespectRules\FalseVal::class;
    public const FIBONACCI = RespectRules\Fibonacci::class;
    public const FILE = RespectRules\File::class;
    public const FILTER_VAR = RespectRules\FilterVar::class;
    public const FINITE = RespectRules\Finite::class;
    public const FLOAT_TYPE = RespectRules\FloatType::class;
    public const FLOAT_VAL = RespectRules\FloatVal::class;
    public const GRAPH = RespectRules\Graph::class;
    public const GREATER_THAN = RespectRules\GreaterThan::class;
    public const HEX_RGB_COLOR = RespectRules\HexRgbColor::class;
    public const IBAN = RespectRules\Iban::class;
    public const IDENTICAL = RespectRules\Identical::class;
    public const IMAGE = RespectRules\Image::class;
    public const IMEI = RespectRules\Imei::class;
    public const IN = RespectRules\In::class;
    public const INFINITE = RespectRules\Infinite::class;
    public const INSTANCE = RespectRules\Instance::class;
    public const INT_TYPE = RespectRules\IntType::class;
    public const INT_VAL = RespectRules\IntVal::class;
    public const IP = RespectRules\Ip::class;
    public const ISBN = RespectRules\Isbn::class;
    public const ITERABLE_TYPE = RespectRules\IterableType::class;
    public const JSON = RespectRules\Json::class;
    public const KEY = RespectRules\Key::class;
    public const KEY_NESTED = RespectRules\KeyNested::class;
    public const KEY_SET = RespectRules\KeySet::class;
    public const KEY_VALUE = RespectRules\KeyValue::class;
    public const LANGUAGE_CODE = RespectRules\LanguageCode::class;
    public const LEAP_DATE = RespectRules\LeapDate::class;
    public const LEAP_YEAR = RespectRules\LeapYear::class;
    public const LENGTH = RespectRules\Length::class;
    public const LESS_THAN = RespectRules\LessThan::class;
    public const LOWERCASE = RespectRules\Lowercase::class;
    public const LUHN = RespectRules\Luhn::class;
    public const MAC_ADDRESS = RespectRules\MacAddress::class;
    public const MAX = RespectRules\Max::class;
    public const MAX_AGE = RespectRules\MaxAge::class;
    public const MIMETYPE = RespectRules\Mimetype::class;
    public const MIN = RespectRules\Min::class;
    public const MIN_AGE = RespectRules\MinAge::class;
    public const MULTIPLE = RespectRules\Multiple::class;
    public const NEGATIVE = RespectRules\Negative::class;
    public const NFE_ACCESS_KEY = RespectRules\NfeAccessKey::class;
    public const NIF = RespectRules\Nif::class;
    public const NIP = RespectRules\Nip::class;
    public const NO = RespectRules\No::class;
    public const NOT = RespectRules\Not::class;
    public const NOT_BLANK = RespectRules\NotBlank::class;
    public const NOT_EMOJI = RespectRules\NotEmoji::class;
    public const NOT_EMPTY = RespectRules\NotEmpty::class;
    public const NOT_OPTIONAL = RespectRules\NotOptional::class;
    public const NO_WHITESPACE = RespectRules\NoWhitespace::class;
    public const NULLABLE = RespectRules\Nullable::class;
    public const NULL_TYPE = RespectRules\NullType::class;
    public const NUMBER = RespectRules\Number::class;
    public const NUMERIC_VAL = RespectRules\NumericVal::class;
    public const OBJECT_TYPE = RespectRules\ObjectType::class;
    public const ODD = RespectRules\Odd::class;
    public const OPTIONAL = RespectRules\Optional::class;
    public const PERFECT_SQUARE = RespectRules\PerfectSquare::class;
    public const PESEL = RespectRules\Pesel::class;
    public const PHP_LABEL = RespectRules\PhpLabel::class;
    public const PIS = RespectRules\Pis::class;
    public const POLISH_ID_CARD = RespectRules\PolishIdCard::class;
    public const POSITIVE = RespectRules\Positive::class;
    public const POSTAL_CODE = RespectRules\PostalCode::class;
    public const PRIME_NUMBER = RespectRules\PrimeNumber::class;
    public const PRINTABLE = RespectRules\Printable::class;
    public const PUNCT = RespectRules\Punct::class;
    public const READABLE = RespectRules\Readable::class;
    public const REGEX = RespectRules\Regex::class;
    public const RESOURCE_TYPE = RespectRules\ResourceType::class;
    public const ROMAN = RespectRules\Roman::class;
    public const SCALAR_VAL = RespectRules\ScalarVal::class;
    public const SF = RespectRules\Sf::class;
    public const SIZE = RespectRules\Size::class;
    public const SLUG = RespectRules\Slug::class;
    public const SORTED = RespectRules\Sorted::class;
    public const SPACE = RespectRules\Space::class;
    public const STARTS_WITH = RespectRules\StartsWith::class;
    public const STRING_TYPE = RespectRules\StringType::class;
    public const STRING_VAL = RespectRules\StringVal::class;
    public const SUBDIVISION_CODE = RespectRules\SubdivisionCode::class;
    public const SUBSET = RespectRules\Subset::class;
    public const SYMBOLIC_LINK = RespectRules\SymbolicLink::class;
    public const TIME = RespectRules\Time::class;
    public const TLD = RespectRules\Tld::class;
    public const TRUE_VAL = RespectRules\TrueVal::class;
    public const TYPE = RespectRules\Type::class;
    public const UNIQUE = RespectRules\Unique::class;
    public const UPLOADED = RespectRules\Uploaded::class;
    public const UPPERCASE = RespectRules\Uppercase::class;
    public const URL = RespectRules\Url::class;
    public const UUID = RespectRules\Uuid::class;
    public const VERSION = RespectRules\Version::class;
    public const VIDEO_URL = RespectRules\VideoUrl::class;
    public const VOWEL = RespectRules\Vowel::class;
    public const WHEN = RespectRules\When::class;
    public const WRITABLE = RespectRules\Writable::class;
    public const XDIGIT = RespectRules\Xdigit::class;
    public const YES = RespectRules\Yes::class;
    public const ZEND = RespectRules\Zend::class;

    /**
     * Unused rules
     * @RespectRules\CountryCode::class
     * @RespectRules\CurrencyCode::class
     * @RespectRules\Each::class
     * @RespectRules\Email::class
     * @RespectRules\Phone::class
     */

    private function __construct()
    {
    }
}
