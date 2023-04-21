<?php

namespace OrangeHRM\Installer\Controller\Installer\Api;

use InvalidArgumentException;
use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;

class ValidateFieldsAPI extends AbstractInstallerRestController
{
    public const TYPE_USERNAME = 'username';

    private UserDao $userDao;

    /**
     * @return UserDao
     */
    public function getUserDao(): UserDao
    {
        return $this->userDao ??= new UserDao();
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        try {
            $type = $this->checkAndGetField($request, 'type');
            $value = $this->checkAndGetField($request, 'value');
        } catch (InvalidArgumentException $e) {
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
            return [
                'error' => [
                    'status' => $this->getResponse()->getStatusCode(),
                    'message' => $e->getMessage(),
                ]
            ];
        }
        $status = match ($type) {
            self::TYPE_USERNAME => $this->getUserDao()->isExistingSystemUserByUsername($value) === null,
            default => throw new InvalidArgumentException('invalid type provided'),
        };

        return [
            'status' => $status
        ];
    }

    /**
     * @param Request $request
     * @param string $name
     * @return string|null
     */
    private function checkAndGetField(Request $request, string $name): ?string
    {
        if ($request->request->has($name)) {
            if (!empty($request->request->get($name))) {
                return $request->request->get($name);
            }
        }
        throw new InvalidArgumentException("`$name` is required");
    }
}