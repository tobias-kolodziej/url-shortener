<?php

namespace App\Tests\Service;

use App\Entity\ShortUrl;
use App\Exception\ShortcodeCollisionException;
use App\Repository\ShortUrlRepository;
use App\Service\ShortCodeGenerator;
use App\Service\UrlShortenerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UrlShortenerServiceTest extends TestCase
{

    public function testGetOrCreateReturnsExisting(): void
    {
        $originalUrl = 'https://unit-test.com';
        $existingShortUrl = (new ShortUrl())->setOriginalUrl($originalUrl)->setShortCode('abc12345');

        $shortUrlRepository = $this->createMock(ShortUrlRepository::class);
        $shortUrlRepository->method('findOneBy')->willReturn($existingShortUrl);

        $entityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $shortCodeGenerator = $this->createMock(ShortCodeGenerator::class);

        $entityManagerInterface->expects($this->never())->method('persist');
        $entityManagerInterface->expects($this->never())->method('flush');
        $shortCodeGenerator->expects($this->never())->method('generate');

        $urlShortenerService = new UrlShortenerService($shortUrlRepository, $entityManagerInterface, $shortCodeGenerator);
        $result = $urlShortenerService->getOrCreateShortUrl($originalUrl);

        $this->assertSame($existingShortUrl, $result);
    }

    public function testGetOrCreateCreatesNew(): void
    {
        $originalUrl = 'https://unit-test.com';

        $shortUrlRepository = $this->createMock(ShortUrlRepository::class);
        $shortUrlRepository->method('findOneBy')->willReturn(null);

        $entityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $entityManagerInterface->expects($this->once())->method('persist');
        $entityManagerInterface->expects($this->once())->method('flush');

        $shortCodeGenerator = $this->createMock(ShortCodeGenerator::class);
        $shortCodeGenerator->method('generate')->willReturn(['code1', 'code2']);

        $urlShortenerService = new UrlShortenerService($shortUrlRepository, $entityManagerInterface, $shortCodeGenerator);
        $result = $urlShortenerService->getOrCreateShortUrl($originalUrl);

        $this->assertInstanceOf(ShortUrl::class, $result);
        $this->assertSame($originalUrl, $result->getOriginalUrl());
    }

    public function testGetOrCreateFailsWithCollision(): void
    {
        $originalUrl = 'https://unit-test.com';

        $conflictingShortUrl = new ShortUrl();
        $conflictingShortUrl->setOriginalUrl('https://some-other-url.com');

        $shortUrlRepository = $this->createMock(ShortUrlRepository::class);
        $shortUrlRepository->method('findOneBy')->willReturnCallback(function (array $criteria) use ($originalUrl, $conflictingShortUrl) {
            if (isset($criteria['originalUrl']) && $criteria['originalUrl'] === $originalUrl) {
                return null;
            }

            if (isset($criteria['shortCode'])) {
                return $conflictingShortUrl;
            }

            return null;
        });

        $entityManagerInterface = $this->createMock(EntityManagerInterface::class);

        $shortCodeGenerator = $this->createMock(ShortCodeGenerator::class);
        $shortCodeGenerator->method('generate')->willReturn([
            '2K9yfUCA',
            '2w5EhfnV',
            '2AW6wHvr',
            '2LxFfCoe',
            '246ZfnBZ'
        ]);

        $urlShortenerService = new UrlShortenerService($shortUrlRepository, $entityManagerInterface, $shortCodeGenerator);

        $this->expectException(ShortcodeCollisionException::class);
        $urlShortenerService->getOrCreateShortUrl($originalUrl);
    }

    public function testResolveReturnsUrlAndIncrementsClickCount(): void
    {
        $shortCode = 'abc12345';
        $shortUrl = (new ShortUrl())->setOriginalUrl('https://unit-test.com')->setShortCode($shortCode)->setClickCount(0);

        $shortUrlRepository = $this->createMock(ShortUrlRepository::class);
        $shortUrlRepository->method('findOneBy')->willReturn($shortUrl);

        $entityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $entityManagerInterface->expects($this->once())->method('flush');

        $shortCodeGenerator = $this->createMock(ShortCodeGenerator::class);

        $urlShortenerService = new UrlShortenerService($shortUrlRepository, $entityManagerInterface, $shortCodeGenerator);
        $result = $urlShortenerService->resolve($shortCode);

        $this->assertSame('https://unit-test.com', $result);
        $this->assertSame(1, $shortUrl->getClickCount());
    }

    public function testResolveReturnsNullIfNotFound(): void
    {
        $shortUrlRepository = $this->createMock(ShortUrlRepository::class);
        $shortUrlRepository->method('findOneBy')->willReturn(null);

        $entityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $shortCodeGenerator = $this->createMock(ShortCodeGenerator::class);

        $urlShortenerService = new UrlShortenerService($shortUrlRepository, $entityManagerInterface, $shortCodeGenerator);
        $result = $urlShortenerService->resolve('nonexistent');

        $this->assertNull($result);
    }
}
