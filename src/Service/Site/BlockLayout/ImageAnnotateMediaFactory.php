<?php
namespace ImageAnnotate\Service\Site\BlockLayout;

use ImageAnnotate\Site\BlockLayout\ImageAnnotateMedia;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ImageAnnotateMediaFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ImageAnnotateMedia($services->get('Omeka\EntityManager'));
    }
}
