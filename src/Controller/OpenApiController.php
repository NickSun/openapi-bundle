<?php

namespace Nicksun\OpenApi\Controller;

use Nicksun\OpenApi\Service\YamlDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class OpenApiController extends AbstractController
{
    private YamlDataProvider $dataProvider;
    private ParameterBagInterface $params;
    private Environment $twig;

    public function __construct(
        Environment $twig,
        ParameterBagInterface $parameterBag,
        YamlDataProvider $dataProvider,
    ) {
        $this->dataProvider = $dataProvider;
        $this->params = $parameterBag;
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        $definitions = $this->dataProvider->getDefinitions();

        return new Response(
            $this->twig->render(
                '@OpenApi/OpenApi/index.html.twig',
                [
                    'swagger_data' => $definitions,
                    'swagger_ui_version' => $this->params->get('open_api.swagger_ui_version'),
                    'title' => $this->params->get('open_api.title'),
                ]
            ),
            Response::HTTP_OK,
            ['Content-Type' => 'text/html']
        );
    }
}
