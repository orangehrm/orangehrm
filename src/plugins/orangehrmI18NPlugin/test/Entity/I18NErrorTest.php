<?php

namespace OrangeHRM\Tests\I18N\Entity;

use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\EntityMissingAssignedId;
use OrangeHRM\Entity\I18NError;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group I18N
 * @group Entity
 */
class I18NErrorTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([I18NError::class]);
    }

    public function testI18NErrorEntity(): void
    {
        $errorEntity = new I18NError();
        $errorEntity->setName('placeholder_mismatch');
        $errorEntity->setMessage('Mismatch found between placeholders');
        $this->persist($errorEntity);

        $errorEntity = $this->getRepository(I18NError::class)->findOneBy(['name' => 'placeholder_mismatch']);
        $this->assertEquals('placeholder_mismatch', $errorEntity->getName());
        $this->assertEquals('Mismatch found between placeholders', $errorEntity->getMessage());
    }

    public function testI18NErrorEntityWithDuplicateName(): void
    {
        $errorEntity = new I18NError();
        $errorEntity->setName('placeholder_mismatch');
        $errorEntity->setMessage('Mismatch found between placeholders');
        $this->persist($errorEntity);

        $errorEntity = new I18NError();
        $errorEntity->setName('placeholder_mismatch');
        $errorEntity->setMessage('Mismatch found between placeholders');

        $this->expectException(UniqueConstraintViolationException::class);
        $this->persist($errorEntity);
    }

    public function testI18NErrorEntityWithNullName(): void
    {
        $errorEntity = new I18NError();
        $errorEntity->setMessage('Mismatch found between placeholders');

        $this->expectException(EntityMissingAssignedId::class);
        $this->persist($errorEntity);
    }

    public function testI18NErrorEntityWithNullDescription(): void
    {
        $errorEntity = new I18NError();
        $errorEntity->setName('placeholder_mismatch');

        $this->expectException(NotNullConstraintViolationException::class);
        $this->persist($errorEntity);
    }
}
