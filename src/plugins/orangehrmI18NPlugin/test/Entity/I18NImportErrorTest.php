<?php

namespace OrangeHRM\Tests\I18N\Entity;

use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use OrangeHRM\Entity\Decorator\I18NImportErrorDecorator;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\I18NError;
use OrangeHRM\Entity\I18NGroup;
use OrangeHRM\Entity\I18NImportError;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group I18N
 * @group Entity
 */
class I18NImportErrorTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            Employee::class,
            I18NLanguage::class,
            I18NGroup::class,
            I18NLangString::class,
            I18NError::class,
            I18NImportError::class
        ]);

        $employee = new Employee();
        $employee->setFirstName('Test');
        $employee->setLastName('Test');
        $this->persist($employee);

        $language = new I18NLanguage();
        $language->setName('Test Language');
        $language->setCode('TEST');
        $this->persist($language);

        $group = new I18NGroup();
        $group->setName('test');
        $group->setTitle('Test Group');
        $this->persist($group);

        $langString = new I18NLangString();
        $langString->setUnitId('test_unit_id');
        $langString->setGroup($this->getRepository(I18NGroup::class)->find(1));
        $langString->setValue('Test Value');
        $langString->setNote('Test Note');
        $langString->setVersion('5.TEST');
        $this->persist($langString);

        $error = new I18NError();
        $error->setName('test');
        $error->setMessage('Test Message');
        $this->persist($error);
    }

    public function testI18NImportError(): void
    {
        $langString = $this->getRepository(I18NLangString::class)->find(1);
        $language = $this->getRepository(I18NLanguage::class)->find(1);
        $error = $this->getRepository(I18NError::class)->findOneBy(['name' => 'test']);
        $employee = $this->getRepository(Employee::class)->findOneBy(['empNumber' => 1]);

        $importError = new I18NImportError();
        $importError->setLangString($langString);
        $importError->setLanguage($language);
        $importError->setError($error);
        $importError->setImportedBy($employee);
        $this->persist($importError);

        $importError = $this->getRepository(I18NImportError::class)->find(1);
        $this->assertEquals($language, $importError->getLanguage());
        $this->assertEquals($langString, $importError->getLangString());
        $this->assertEquals($error, $importError->getError());
        $this->assertEquals($employee, $importError->getImportedBy());
    }

    public function testI18NImportErrorWithNullLangString(): void
    {
        $language = $this->getRepository(I18NLanguage::class)->find(1);
        $error = $this->getRepository(I18NError::class)->findOneBy(['name' => 'test']);
        $employee = $this->getRepository(Employee::class)->findOneBy(['empNumber' => 1]);

        $importError = new I18NImportError();
        $importError->setLanguage($language);
        $importError->setError($error);
        $importError->setImportedBy($employee);

        $this->expectException(NotNullConstraintViolationException::class);
        $this->persist($importError);
    }

    public function testI18NImportErrorWithNullLanguage(): void
    {
        $langString = $this->getRepository(I18NLangString::class)->find(1);
        $error = $this->getRepository(I18NError::class)->findOneBy(['name' => 'test']);
        $employee = $this->getRepository(Employee::class)->findOneBy(['empNumber' => 1]);

        $importError = new I18NImportError();
        $importError->setLangString($langString);
        $importError->setError($error);
        $importError->setImportedBy($employee);

        $this->expectException(NotNullConstraintViolationException::class);
        $this->persist($importError);
    }

    public function testI18NImportErrorWithNullError(): void
    {
        $langString = $this->getRepository(I18NLangString::class)->find(1);
        $language = $this->getRepository(I18NLanguage::class)->find(1);
        $employee = $this->getRepository(Employee::class)->findOneBy(['empNumber' => 1]);

        $importError = new I18NImportError();
        $importError->setLangString($langString);
        $importError->setLanguage($language);
        $importError->setImportedBy($employee);

        $this->expectException(NotNullConstraintViolationException::class);
        $this->persist($importError);
    }

    public function testI18NImportErrorWithNullImportedBy(): void
    {
        $langString = $this->getRepository(I18NLangString::class)->find(1);
        $language = $this->getRepository(I18NLanguage::class)->find(1);
        $error = $this->getRepository(I18NError::class)->findOneBy(['name' => 'test']);

        $importError = new I18NImportError();
        $importError->setLangString($langString);
        $importError->setLanguage($language);
        $importError->setError($error);

        $this->expectException(NotNullConstraintViolationException::class);
        $this->persist($importError);
    }

    public function testGetDecorator(): void
    {
        $importError = new I18NImportError();
        $this->assertInstanceOf(I18NImportErrorDecorator::class, $importError->getDecorator());
    }

    public function testI18NImportErrorWithDecorator(): void
    {
        $importError = new I18NImportError();
        $importError->getDecorator()->setLangStringById(1);
        $importError->getDecorator()->setLanguageById(1);
        $importError->getDecorator()->setErrorByName('test');
        $importError->getDecorator()->setImportedEmployeeByEmpNumber(1);
        $this->persist($importError);

        $importError = $this->getRepository(I18NImportError::class)->find(1);
        $this->assertEquals($this->getRepository(I18NLanguage::class)->find(1), $importError->getLanguage());
        $this->assertEquals($this->getRepository(I18NLangString::class)->find(1), $importError->getLangString());
        $this->assertEquals($this->getRepository(I18NError::class)->findOneBy(['name' => 'test']), $importError->getError());
        $this->assertEquals($this->getRepository(Employee::class)->findOneBy(['empNumber' => 1]), $importError->getImportedBy());
    }
}
