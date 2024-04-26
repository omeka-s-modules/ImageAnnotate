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
        return 'Image annotations'; // @translate
    }

    public function getCompatibleResourceNames() : array
    {
        return ['items'];
    }

    public function render(PhpRenderer $view, AbstractResourceEntityRepresentation $resource) : string
    {
        $output = '';
        $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
        $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/show-annotations.js', 'ImageAnnotate'));
        foreach ($resource->media() as $media) {
            // Get annotations, if any.
            $imageAnnotateMedia = $this->entityManager
                ->getRepository('ImageAnnotate\Entity\ImageAnnotateMedia')
                ->findOneBy(['media' => $media->id()]);
            $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
            if (!$annotations) {
                continue;
            }
            $output .= $view->partial('common/image-annotate', [
                'imageSrc' => $media->thumbnailUrl('large'),
                'annotations' => $annotations,
            ]);
        }
        return $output;
    }
}
