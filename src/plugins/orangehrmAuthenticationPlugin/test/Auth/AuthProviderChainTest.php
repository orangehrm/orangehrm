<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Authentication\Auth;

use InvalidArgumentException;
use OrangeHRM\Authentication\Auth\AbstractAuthProvider;
use OrangeHRM\Authentication\Auth\AuthProviderChain;
use OrangeHRM\Authentication\Dto\AuthParams;
use OrangeHRM\Authentication\Dto\AuthParamsInterface;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Authentication
 * @group Auth
 */
class AuthProviderChainTest extends TestCase
{
    public function testAuthenticateWithOneProvider(): void
    {
        $chain = new AuthProviderChain();
        $debugAuthProvider = $this->getMockBuilder(DebugAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $debugAuthProvider->expects($this->once())
            ->method('authenticate')
            ->willReturn(false);
        $chain->addProvider($debugAuthProvider);
        $this->assertFalse($chain->authenticate(new AuthParams(new UserCredential())));
    }

    public function testAuthenticateWithMultipleProvidersResolvingFirstProvider(): void
    {
        $chain = new AuthProviderChain();
        $debugAuthProvider = $this->getMockBuilder(DebugAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $debugAuthProvider->expects($this->once())
            ->method('authenticate')
            ->willReturn(true);

        $localAuthProvider = $this->getMockBuilder(LocalAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $localAuthProvider->expects($this->never())
            ->method('authenticate')
            ->willReturn(true);

        $ldapAuthProvider = $this->getMockBuilder(LDAPAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $ldapAuthProvider->expects($this->never())
            ->method('authenticate')
            ->willReturn(true);
        $chain->addProvider($localAuthProvider->setPriority(10));
        $chain->addProvider($debugAuthProvider->setPriority(10000));
        $chain->addProvider($ldapAuthProvider->setPriority(100));
        $this->assertTrue($chain->authenticate(new AuthParams(new UserCredential())));
    }

    public function testAuthenticateWithMultipleProvidersResolvingSecondProvider(): void
    {
        $chain = new AuthProviderChain();
        $debugAuthProvider = $this->getMockBuilder(DebugAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $debugAuthProvider->expects($this->once())
            ->method('authenticate')
            ->willReturn(false);

        $localAuthProvider = $this->getMockBuilder(LocalAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $localAuthProvider->expects($this->never())
            ->method('authenticate')
            ->willReturn(true);

        $ldapAuthProvider = $this->getMockBuilder(LDAPAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $ldapAuthProvider->expects($this->once())
            ->method('authenticate')
            ->willReturn(true);
        $chain->addProvider($localAuthProvider->setPriority(10));
        $chain->addProvider($debugAuthProvider->setPriority(10000));
        $chain->addProvider($ldapAuthProvider->setPriority(100));
        $this->assertTrue($chain->authenticate(new AuthParams(new UserCredential())));
    }

    public function testAuthenticateWithMultipleProvidersResolvingLastProvider(): void
    {
        $chain = new AuthProviderChain();
        $debugAuthProvider = $this->getMockBuilder(DebugAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $debugAuthProvider->expects($this->once())
            ->method('authenticate')
            ->willReturn(false);

        $localAuthProvider = $this->getMockBuilder(LocalAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $localAuthProvider->expects($this->once())
            ->method('authenticate')
            ->willReturn(true);

        $ldapAuthProvider = $this->getMockBuilder(LDAPAuthProvider::class)
            ->onlyMethods(['authenticate'])
            ->getMock();
        $ldapAuthProvider->expects($this->once())
            ->method('authenticate')
            ->willReturn(false);
        $chain->addProvider($localAuthProvider->setPriority(10));
        $chain->addProvider($debugAuthProvider->setPriority(10000));
        $chain->addProvider($ldapAuthProvider->setPriority(100));
        $this->assertTrue($chain->authenticate(new AuthParams(new UserCredential())));
    }

    public function testAddProviderWithMultipleProvidersWithSamePriority(): void
    {
        $chain = new AuthProviderChain();
        $chain->addProvider((new DebugAuthProvider())->setPriority(10));
        $chain->addProvider((new LocalAuthProvider())->setPriority(100));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Conflicting priority value of `' . LDAPAuthProvider::class . '` with another auth provider'
        );
        $chain->addProvider((new LDAPAuthProvider())->setPriority(100));
    }

    public function testAddProvider(): void
    {
        $chain = new AuthProviderChain();
        $chain->addProvider(new DebugAuthProvider());
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Instance of `' . DebugAuthProvider::class . '` already register as auth provider'
        );
        $chain->addProvider(new DebugAuthProvider());
    }
}

class DebugAuthProvider extends AbstractAuthProvider
{
    private int $priority = 10;

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(AuthParamsInterface $authParams): bool
    {
        return true;
    }
}

class LocalAuthProvider extends AbstractAuthProvider
{
    private int $priority = 10000;

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(AuthParamsInterface $authParams): bool
    {
        return $authParams->getCredential()->getUsername() == $authParams->getCredential()->getPassword()
            && $authParams->getCredential()->getUsername() != null;
    }
}

class LDAPAuthProvider extends AbstractAuthProvider
{
    private int $priority = 100;

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(AuthParamsInterface $authParams): bool
    {
        return $authParams->getCredential()->getUsername() == $authParams->getCredential()->getPassword()
            && $authParams->getCredential()->getUsername() != null;
    }
}
