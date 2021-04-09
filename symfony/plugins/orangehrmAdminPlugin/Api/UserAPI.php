<?php

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Api\Model\UserModel;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\ORM\Doctrine;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Http\Response;

class UserAPI extends EndPoint
{
    public const PARAMETER_ID = 'id';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_USERNAME = 'username';
    public const PARAMETER_PASSWORD = 'password';
    public const PARAMETER_USER_ROLE_ID = 'userRoleId';
    public const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_CHANGE_PASSWORD = 'changePassword';

    public const PARAMETER_SORT_FIELD = 'sortField';
    public const PARAMETER_SORT_ORDER = 'sortOrder';
    public const PARAMETER_OFFSET = 'offset';
    public const PARAMETER_LIMIT = 'limit';

    /**
     * @var null|UserService
     */
    protected ?UserService $systemUserService = null;

    /**
     * @return UserService|null
     */
    public function getSystemUserService(): ?UserService
    {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new UserService();
        }
        return $this->systemUserService;
    }

    /**
     * @param UserService|null $systemUserService
     */
    public function setSystemUserService(?UserService $systemUserService): void
    {
        $this->systemUserService = $systemUserService;
    }

    public function getOne(): Response
    {
        $userId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $systemUser = $this->getSystemUserService()->getSystemUser($userId);
        return new Response((new UserModel($systemUser))->toArray());
    }

    public function getList(): Response
    {
        $searchClues['offset'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET);
        $searchClues['limit'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT);
        $searchClues['sortField'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_FIELD);
        $searchClues['sortOrder'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_SORT_ORDER);

        $users = [];
        $systemUsers = $this->getSystemUserService()->searchSystemUsers($searchClues);
        foreach ($systemUsers as $user) {
            $users[] = (new UserModel($user))->toArray();
        }
        return new Response(
            $users,
            [],
            ['total' => $this->getSystemUserService()->getSearchSystemUsersCount($searchClues)]
        );
    }

    public function create(): Response
    {
        $username = $this->getRequestParams()->getPostParam(self::PARAMETER_USERNAME);
        $password = $this->getRequestParams()->getPostParam(self::PARAMETER_PASSWORD);
        $userRoleId = $this->getRequestParams()->getPostParam(self::PARAMETER_USER_ROLE_ID);
        $empNumber = $this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_NUMBER);
        $status = $this->getRequestParams()->getPostParam(self::PARAMETER_STATUS);

        $employee = Doctrine::getEntityManager()->getReference(Employee::class, $empNumber);

        $userRole = $this->getSystemUserService()->getUserRoleById($userRoleId);
        $systemUser = new User();
        $systemUser->setUserName($username);
        $systemUser->setUserPassword($password);
        $systemUser->setStatus($status);
        $systemUser->setUserRole($userRole);
        $systemUser->setEmployee($employee);
        $systemUser = $this->getSystemUserService()->saveSystemUser($systemUser, true);
        return new Response((new UserModel($systemUser))->toArray());
    }

    public function update(): Response
    {
        $userId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $username = $this->getRequestParams()->getPostParam(self::PARAMETER_USERNAME);
        $userRoleId = $this->getRequestParams()->getPostParam(self::PARAMETER_USER_ROLE_ID);
        $empNumber = $this->getRequestParams()->getPostParam(self::PARAMETER_EMPLOYEE_NUMBER);
        $status = $this->getRequestParams()->getPostParam(self::PARAMETER_STATUS);
        $changePassword = $this->getRequestParams()->getPostParam(self::PARAMETER_CHANGE_PASSWORD);

        $employee = Doctrine::getEntityManager()->getReference(Employee::class, $empNumber);

        $userRole = $this->getSystemUserService()->getUserRoleById($userRoleId);

        $systemUser = $this->getSystemUserService()->getSystemUser($userId);
        $systemUser->setUserName($username);
        $systemUser->setStatus($status);
        $systemUser->setUserRole($userRole);
        $systemUser->setEmployee($employee);

        if ($changePassword) {
            $password = $this->getRequestParams()->getPostParam(self::PARAMETER_PASSWORD);
            $systemUser->setUserPassword($password);
        }
        $systemUser = $this->getSystemUserService()->saveSystemUser($systemUser, $changePassword);
        return new Response((new UserModel($systemUser))->toArray());
    }

    public function delete(): Response
    {
        $ids = $this->getRequestParams()->getPostParam(self::PARAMETER_IDS);
        $this->getSystemUserService()->deleteSystemUsers($ids);
        return new Response($ids);
    }
}
