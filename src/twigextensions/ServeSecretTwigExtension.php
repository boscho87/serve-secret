<?php

namespace itscoding\servesecret\twigextensions;

use craft\elements\Asset;
use itscoding\servesecret\ServeSecret;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ServeSecretTwigExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'ServeSecret';
    }
    public function getFilters(): array
    {
        return [
            new TwigFilter('secretFile', [$this, 'secretFile']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('secretFile', [$this, 'secretFile']),
        ];
    }

    public function secretFile(Asset $file, bool $inline = true): string
    {
        return ServeSecret::$plugin->security->getActionLink($file, $inline);
    }
}
