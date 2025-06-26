<?php

namespace App\Controller\Api;

use App\Exception\InvalidUrlException;
use App\Exception\ShortcodeNotFoundException;
use App\Service\UrlShortenerService;
use App\Repository\ShortUrlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/urls', name: 'api_short_urls_')]
class UrlShortenerController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function index(UrlShortenerService $urlShortenerService): JsonResponse
    {
        return $this->json($urlShortenerService->list());
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, UrlShortenerService $urlShortenerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $originalUrl = $data['url'] ?? null;

        if (!$originalUrl || !filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidUrlException();
        }

        $shortUrl = $urlShortenerService->getOrCreateShortUrl($originalUrl);

        return $this->json([
            'shortUrl' => $request->getSchemeAndHttpHost() . '/' . $shortUrl->getShortCode()
        ]);
    }
    
    #[Route('/{shortCode}', name: 'show', methods: ['GET'])]
    public function show(string $shortCode, UrlShortenerService $urlShortenerService): JsonResponse
    {
        $shortUrl = $urlShortenerService->findByCode($shortCode);

        if (!$shortUrl) {
            throw new ShortcodeNotFoundException();
        }

        return $this->json($shortUrl->toArray());
    }
}
