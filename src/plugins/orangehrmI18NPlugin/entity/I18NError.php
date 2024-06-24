<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_i18n_error")
 * @ORM\Entity
 */
class I18NError
{
    public const PLACEHOLDER_MISMATCH = 'placeholder_mismatch';
    public const SELECT_MISMATCH = 'select_placeholder_mismatch';
    public const PLURAL_MISMATCH = 'plural_placeholder_mismatch';
    public const UNNECESSARY_PLACEHOLDER = 'unnecessary_placeholder';
    public const INVALID_SYNTAX = 'invalid_syntax';
    public const ERROR_MAP = [
        self::PLACEHOLDER_MISMATCH,
        self::SELECT_MISMATCH,
        self::PLURAL_MISMATCH,
        self::UNNECESSARY_PLACEHOLDER,
        self::INVALID_SYNTAX,
    ];

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @ORM\Id
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private string $message;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
