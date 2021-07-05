<?php


namespace OrangeHRM\Pim\Dto;


use OrangeHRM\Core\Dto\FilterParams;

class EmployeeSupervisorSearchFilterParams extends FilterParams
{

    public const ALLOWED_SORT_FIELDS = ['rt.reportingMethod'];


    /**
     * @var string|null
     */
    protected ?string $empNumber = null;

    /**
     * EmployeeSupervisorSearchFilterParams constructor.
     */
    public function __construct()
    {
        $this->setSortField('rt.reportingMethod');
    }

    /**
     * @return string|null
     */
    public function getEmpNumber(): ?string
    {
        return $this->empNumber;
    }

    /**
     * @param string|null $empNumber
     */
    public function setEmpNumber(?string $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

}