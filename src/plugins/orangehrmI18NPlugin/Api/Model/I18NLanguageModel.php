<?php

namespace OrangeHRM\I18N\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\I18NLanguage;

class I18NLanguageModel implements Normalizable
{
    use ModelTrait;

    public function __construct(I18NLanguage $i18NLanguage)
    {
        $this->setEntity($i18NLanguage);
        $this->setFilters([
            'id',
            'name',
            'code',
        ]);

        $this->setAttributeNames([
            'id',
            'name',
            'code',
        ]);
    }
}
