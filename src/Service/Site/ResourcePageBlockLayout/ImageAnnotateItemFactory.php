<?php
namespace ImageAnnotate\Service\Site\ResourcePageBlockLayout;

use ImageAnnotate\Site\ResourcePageBlockLayout\ImageAnnotateItem;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ImageAnnotateItemFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ImageAnnotateItem($services->get('Omeka\EntityManager'));
    }
}
