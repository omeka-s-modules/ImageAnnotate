<?php
namespace ImageAnnotate\Site\ResourcePageBlockLayout;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Site\ResourcePageBlockLayout\ResourcePageBlockLayoutInterface;

class ImageAnnotateItem implements ResourcePageBlockLayoutInterface
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getLabel() : string
    {
        return 'Media embeds (with image annotations)'; // @translate
    }

    public function getCompatibleResourceNames() : array
    {
        return ['items'];
    }

    public function render(PhpRenderer $view, AbstractResourceEntityRepresentation $resource) : string
    {
        $output = '';
        foreach ($resource->media() as $resource) {
            // Get annotations, if any.
            $imageAnnotateMedia = $this->entityManager
                ->getRepository('ImageAnnotate\Entity\ImageAnnotateMedia')
                ->findOneBy(['media' => $resource->id()]);
            $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
            if ($annotations) {
                $output .= $view->partial('common/resource-page-block-layout/image-annotate-media', [
                    'resource' => $resource,
                    'annotations' => $annotations,
                ]);
            } else {
                $output .= $resource->render();
            }
        }
        return $output;
    }
}
