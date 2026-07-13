<?php
namespace App\Tests\Entity;
use App\Entity\User;use PHPUnit\Framework\TestCase;
final class UserTest extends TestCase{public function testPapelBasicoSempreExiste():void{$user=(new User())->setEmail('USER@EXAMPLE.COM')->setRoles([User::ROLE_EDITOR]);self::assertSame('user@example.com',$user->getEmail());self::assertContains(User::ROLE_USER,$user->getRoles());self::assertContains(User::ROLE_EDITOR,$user->getRoles());}}
