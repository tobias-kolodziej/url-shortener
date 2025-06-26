<?php

namespace App\Service;

use App\Entity\ShortUrl;
use App\Exception\ShortcodeCollisionException;
use App\Repository\ShortUrlRepository;
use Doctrine\ORM\EntityManagerInterface;

class UrlShortenerService
{
    public function __construct(
        private ShortUrlRepository $shortUrlRepository,
        private EntityManagerInterface $entityManagerInterface,
        private ShortCodeGenerator $shortCodeGenerator,
    ) {}

    public function getOrCreateShortUrl(string $originalUrl): ShortUrl
    {
        $existing = $this->shortUrlRepository->findOneBy(['originalUrl' => $originalUrl]);
        if ($existing) {
            return $existing;
        }

        foreach ($this->shortCodeGenerator->generate($originalUrl) as $shortCode) {
            $existingCode = $this->shortUrlRepository->findOneBy(['shortCode' => $shortCode]);

            if (!$existingCode) {
                $shortUrl = new ShortUrl();
                $shortUrl->setOriginalUrl($originalUrl);
                $shortUrl->setShortCode($shortCode);
                $shortUrl->setClickCount(0);
                $shortUrl->setCreatedAt(new \DateTimeImmutable());

                $this->entityManagerInterface->persist($shortUrl);
                $this->entityManagerInterface->flush();

                return $shortUrl;
            }

            if ($existingCode->getOriginalUrl() === $originalUrl) {
                return $existingCode;
            }
        }

        throw new ShortcodeCollisionException();
    }

    public function resolve(string $shortCode): ?string
    {
        $shortUrl = $this->shortUrlRepository->findOneBy(['shortCode' => $shortCode]);

        if (!$shortUrl) {
            return null;
        }

        $shortUrl->setClickCount($shortUrl->getClickCount() + 1);
        $this->entityManagerInterface->flush();

        return $shortUrl->getOriginalUrl();
    }

    public function findByCode(string $shortCode): ?ShortUrl
    {
        return $this->shortUrlRepository->findOneBy(['shortCode' => $shortCode]);
    }

    public function list(): array
    {
        $shortUrls = $this->shortUrlRepository->findAll();

        return array_map(fn($shortUrl) => $shortUrl->toArray(), $shortUrls);
    }
}
