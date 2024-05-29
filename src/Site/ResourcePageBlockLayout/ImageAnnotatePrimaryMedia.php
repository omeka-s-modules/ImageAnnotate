<?php
namespace ImageAnnotate\Site\ResourcePageBlockLayout;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Site\ResourcePageBlockLayout\ResourcePageBlockLayoutInterface;

class ImageAnnotatePrimaryMedia implements ResourcePageBlockLayoutInterface
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getLabel() : string
    {
        return 'Image annotations (primary media)'; // @translate
    }

    public function getCompatibleResourceNames() : array
    {
        return ['items'];
    }

    public function render(PhpRenderer $view, AbstractResourceEntityRepresentation $resource) : string
    {
        $output = '';
        $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
        $view->headLink()->appendStylesheet($view->assetUrl('css/style.css', 'ImageAnnotate'));
        $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate.js', 'ImageAnnotate'));
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/show-annotations.js', 'ImageAnnotate'));
        // Get annotations, if any.
        $media = $resource->primaryMedia();
        if (!$media) {
            return '';
        }
        $imageAnnotateMedia = $this->entityManager
            ->getRepository('ImageAnnotate\Entity\ImageAnnotateMedia')
            ->findOneBy(['media' => $media->id()]);
        $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
        if (!$annotations) {
            return '';
        }
        $output .= $view->partial('common/image-annotate', [
            'imageSrc' => $media->thumbnailDisplayUrl('large'),
            'annotations' => $annotations,
        ]);
        return $output;
    }
}
