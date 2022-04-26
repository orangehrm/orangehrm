<?php

namespace OrangeHRM\Installer\Util\V1\Dto;

class CustomField
{
    protected string $fieldNum;
    protected string $extraData;

    /**
     * @param string $fieldNum
     * @param string $extraData
     */
    public function __construct(string $fieldNum, string $extraData)
    {
        $this->fieldNum = $fieldNum;
        $this->extraData = $extraData;
    }

    /**
     * @return string
     */
    public function getFieldNum(): string
    {
        return $this->fieldNum;
    }

    /**
     * @param string $fieldNum
     */
    public function setFieldNum(string $fieldNum): void
    {
        $this->fieldNum = $fieldNum;
    }

    /**
     * @return string
     */
    public function getExtraData(): string
    {
        return $this->extraData;
    }

    /**
     * @param string $extraData
     */
    public function setExtraData(string $extraData): void
    {
        $this->extraData = $extraData;
    }

    /**
     * @param array $customString
     * @return static
     */
    public static function createFromArray(array $customString): self
    {
        return new self(
            $customString['field_num'],
            $customString['extra_data'],
        );
    }
}
