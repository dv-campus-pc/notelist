<?php

namespace App\Tests;

use App\Entity\User;
use App\Exception\ValidationException;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private MockObject $validatorMock;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->userService = new UserService(
            $this->validatorMock,
            $this->createMock(UserPasswordHasher::class),
            $this->createMock(EntityManagerInterface::class)
        );
    }

    public function test_create_validationFailed_exceptionThrown(): void
    {
        $this->validatorMock->method('validate')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('test_error', null, [], null, null, null)
        ]));

        $this->expectException(ValidationException::class);

        $this->userService->create('test_password', 'test_username');
    }

    public function test_create_validationSuccess_returnUser(): void
    {
        $this->validatorMock->method('validate')->willReturn(new ConstraintViolationList([]));

        $user = $this->userService->create('test_password', 'test_username');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test_username', $user->getUserIdentifier());
    }
}
