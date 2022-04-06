<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAuthenticationTest extends WebTestCase
{
    const HOST = 'http://localhost';

    public function test_registration_emptyUsername_flashMessageWithError(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('Register', [
            'username' => '',
            'password' => 'test_password'
        ]);

        $this->assertResponseRedirects(self::HOST . '/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'Username should not be blank');
    }

    public function test_registration_emptyPassword_flashMessageWithError(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('Register', [
            'username' => 'test_username',
            'password' => ''
        ]);

        $this->assertResponseRedirects(self::HOST . '/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'Password should not be blank');
    }

    public function test_registration_validUsernameAndPassword_successRegistration(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('Register', [
            'username' => 'test_username',
            'password' => 'test_password'
        ]);

        $this->assertResponseRedirects('/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'You have been registered!');
    }

    public function test_registration_userAlreadyRegistered_flashMessageWithError(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('Register', [
            'username' => 'test_username',
            'password' => 'test_password'
        ]);

        $this->assertResponseRedirects('/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'You have been registered!');

        $client->submitForm('Register', [
            'username' => 'test_username',
            'password' => 'test_password'
        ]);

        $this->assertResponseRedirects(self::HOST . '/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'User with such name already exists');
    }

    public function test_login_invalidUsernameAndInvalidPassword_flashMessageWithError(): void {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('login-form', [
            '_username' => '',
            '_password' => ''
        ]);

        $this->assertResponseRedirects(self::HOST . '/user/login', 302);
        $client->followRedirect();
        $this->assertResponseRedirects('/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'Bad credentials.');
    }

    public function test_login_validUsernameAndInvalidPassword_flashMessageWithError(): void {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('Register', [
            'username' => 'test_username',
            'password' => 'test_password'
        ]);

        $this->assertResponseRedirects('/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'You have been registered!');

        $client->submitForm('login-form', [
            '_username' => 'test_username',
            '_password' => 'invalid_password'
        ]);

        $this->assertResponseRedirects(self::HOST . '/user/login', 302);
        $client->followRedirect();
        $this->assertResponseRedirects('/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', 'The presented password is invalid.');
    }

    public function test_login_validUsernameAndValidPassword_successLogin(): void {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->submitForm('Register', [
            'username' => 'test_username',
            'password' => 'test_password'
        ]);

        $this->assertResponseRedirects('/', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'You have been registered!');

        $client->submitForm('login-form', [
            '_username' => 'test_username',
            '_password' => 'test_password'
        ]);

        $this->assertResponseRedirects(self::HOST . '/', 302);
        $client->followRedirect();
        $this->assertResponseRedirects('/notelist', 302);
        $client->followRedirect();
        $this->assertSelectorTextContains('a[href="/user/logout"]', 'Logout');
    }
}
