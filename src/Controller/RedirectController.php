<?php

namespace App\Controller;

use App\Routing\ReservedRoutes;
use App\Service\UrlShortenerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    #[Route(
        '/{shortCode}',
        name: 'resolve_short_url',
        requirements: [
            'shortCode' => '^(?!urls$|stats\/[a-zA-Z0-9]{4,}|not-found)[a-zA-Z0-9]{4,}$'
        ],
        methods: ['GET']
    )]
    public function __invoke(string $shortCode, UrlShortenerService $urlShortenerService): RedirectResponse|Response
    {
        $originalUrl = $urlShortenerService->resolve($shortCode);

        if (!$originalUrl) {
            return new Response(
                file_get_contents($this->getParameter('kernel.project_dir') . '/public/spa/index.html'),
                200,
                ['Content-Type' => 'text/html']
            );
        }

        return $this->redirect($originalUrl, 301);
    }

    #[Route(
        '/{vueRoute}',
        name: 'vue_fallback',
        requirements: ['vueRoute' => '.+'],
        methods: ['GET']
    )]
    public function vueFallback(): Response
    {
        return new Response(
            file_get_contents($this->getParameter('kernel.project_dir') . '/public/spa/index.html'),
            200,
            ['Content-Type' => 'text/html']
        );
    }
}
