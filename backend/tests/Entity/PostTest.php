<?php
namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testAvisoNasceComoRascunhoInterno(): void { $post = new Post(); self::assertSame(Post::STATUS_RASCUNHO, $post->getStatus()); self::assertFalse($post->isPublico()); self::assertFalse($post->isFixado()); }
    public function testAvisoRegistraAutorEPublicacao(): void { $user = (new User())->setNome('Editora')->setEmail('editora@example.com'); $post = (new Post())->setTitulo('Manutenção')->setResumo('Resumo')->setConteudo('Conteúdo completo')->setAutor($user)->setStatus(Post::STATUS_PUBLICADO)->setPublicadoEm(new \DateTimeImmutable('2026-07-11')); self::assertSame('Editora', $post->getAutor()?->getNome()); self::assertSame('2026-07-11', $post->getPublicadoEm()?->format('Y-m-d')); }
}
