<?php
namespace ImageAnnotate\Service\Site\ResourcePageBlockLayout;

use ImageAnnotate\Site\ResourcePageBlockLayout\ImageAnnotatePrimaryMedia;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ImageAnnotatePrimaryMediaFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ImageAnnotatePrimaryMedia($services->get('Omeka\EntityManager'));
    }
}
