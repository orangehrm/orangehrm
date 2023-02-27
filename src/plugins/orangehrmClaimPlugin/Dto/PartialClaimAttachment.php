<?php

namespace OrangeHRM\Claim\Dto;

class PartialClaimAttachment
{
    /**
     * @var int|null
     */
    private ?int $requestId;

    /**
     * @var int|null
     */
    private ?int $eattachId;

    /**
     * @var int|null
     */
    private ?int $eattachSize;

    /**
     * @var string|null
     */
    private ?string $eattachDesc;

    /**
     * @var string|null
     */
    private ?string $eattachFileName;

    /**
     * @var string|null
     */
    private ?string $eattachFileType;

    /**
     * @var string|null
     */
    private ?string $attachedByName;

    /**
     * @param int|null $requestId
     * @param int|null $eattachId
     * @param int|null $eattachSize
     * @param string|null $eattachDesc
     * @param string|null $eattachFileName
     * @param string|null $eattachFileType
     * @param string|null $attachedByName
     */
    public function __construct(
        ?int $requestId,
        ?int $eattachId,
        ?int $eattachSize,
        ?string $eattachDesc,
        ?string $eattachFileName,
        ?string $eattachFileType,
        ?string $attachedByName
    ) {
        $this->requestId = $requestId;
        $this->eattachId = $eattachId;
        $this->eattachSize = $eattachSize;
        $this->eattachDesc = $eattachDesc;
        $this->eattachFileName = $eattachFileName;
        $this->eattachFileType = $eattachFileType;
        $this->attachedByName = $attachedByName;
    }

    /**
     * @return int|null
     */
    public function getRequestId(): ?int
    {
        return $this->requestId;
    }

    /**
     * @param int|null $requestId
     */
    public function setRequestId(?int $requestId): void
    {
        $this->requestId = $requestId;
    }

    /**
     * @return int|null
     */
    public function getEattachId(): ?int
    {
        return $this->eattachId;
    }

    /**
     * @param int|null $eattachId
     */
    public function setEattachId(?int $eattachId): void
    {
        $this->eattachId = $eattachId;
    }

    /**
     * @return int|null
     */
    public function getEattachSize(): ?int
    {
        return $this->eattachSize;
    }

    /**
     * @param int|null $eattachSize
     */
    public function setEattachSize(?int $eattachSize): void
    {
        $this->eattachSize = $eattachSize;
    }

    /**
     * @return string|null
     */
    public function getEattachDesc(): ?string
    {
        return $this->eattachDesc;
    }

    /**
     * @param string|null $eattachDesc
     */
    public function setEattachDesc(?string $eattachDesc): void
    {
        $this->eattachDesc = $eattachDesc;
    }

    /**
     * @return string|null
     */
    public function getEattachFileName(): ?string
    {
        return $this->eattachFileName;
    }

    /**
     * @param string|null $eattachFileName
     */
    public function setEattachFileName(?string $eattachFileName): void
    {
        $this->eattachFileName = $eattachFileName;
    }

    /**
     * @return string|null
     */
    public function getEattachFileType(): ?string
    {
        return $this->eattachFileType;
    }

    /**
     * @param string|null $eattachFileType
     */
    public function setEattachFileType(?string $eattachFileType): void
    {
        $this->eattachFileType = $eattachFileType;
    }

    /**
     * @return int|null
     */
    public function getEattachAttachedBy(): ?int
    {
        return $this->eattachAttachedBy;
    }

    /**
     * @param int|null $eattachAttachedBy
     */
    public function setEattachAttachedBy(?int $eattachAttachedBy): void
    {
        $this->eattachAttachedBy = $eattachAttachedBy;
    }

    /**
     * @param string|null $eattachAttachedByName
     */
    public function setAttachedByName(?string $attachedByName): void
    {
        $this->attachedByName = $attachedByName;
    }

    /**
     * @return string|null
     */
    public function getAttachedByName(): ?string
    {
        return $this->attachedByName;
    }
}