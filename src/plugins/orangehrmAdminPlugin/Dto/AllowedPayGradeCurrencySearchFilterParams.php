<?php

namespace OrangeHRM\Admin\Dto;

class AllowedPayGradeCurrencySearchFilterParams extends PayGradeCurrencySearchFilterParams
{
    public const ALLOWED_SORT_FIELDS = ['ct.id', ...parent::ALLOWED_SORT_FIELDS];

    public function __construct()
    {
        parent::__construct();
        $this->setSortField('ct.id');
    }

}
