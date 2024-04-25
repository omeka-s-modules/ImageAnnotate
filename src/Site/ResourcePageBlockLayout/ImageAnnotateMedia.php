<?php
namespace ImageAnnotate\Site\ResourcePageBlockLayout;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Site\ResourcePageBlockLayout\ResourcePageBlockLayoutInterface;

class ImageAnnotateMedia implements ResourcePageBlockLayoutInterface
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getLabel() : string
    {
        return 'Image annotations'; // @translate
    }

    public function getCompatibleResourceNames() : array
    {
        return ['media'];
    }

    public function render(PhpRenderer $view, AbstractResourceEntityRepresentation $resource) : string
    {
        // Get annotations, if any.
        $imageAnnotateMedia = $this->entityManager
            ->getRepository('ImageAnnotate\Entity\ImageAnnotateMedia')
            ->findOneBy(['media' => $resource->id()]);
        $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
        if (!$annotations) {
            return '';
        }

        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/media-show.js', 'ImageAnnotate'));
        return $view->partial('common/image-annotate', [
            'imageSrc' => $resource->thumbnailUrl('large'),
            'imageAnnotations' => $annotations,
        ]);
    }
}