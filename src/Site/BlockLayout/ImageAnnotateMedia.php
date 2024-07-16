<?php
namespace ImageAnnotate\Site\BlockLayout;

use Doctrine\ORM\EntityManager;
use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Site\BlockLayout\TemplateableBlockLayoutInterface;

class ImageAnnotateMedia extends AbstractBlockLayout implements TemplateableBlockLayoutInterface
{
    protected $defaultData = [
        'annotations' => '[]',
        'media_annotations' => null,
        'title' => null,
    ];

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getLabel()
    {
        return 'Image annotate media'; // @translate
    }

    public function prepareForm(PhpRenderer $view)
    {
        $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
        $view->headLink()->appendStylesheet($view->assetUrl('css/style.css', 'ImageAnnotate'));
        $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate.js', 'ImageAnnotate'));
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/edit-annotations.js', 'ImageAnnotate'));
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/block-layout.js', 'ImageAnnotate'));
    }

    public function form(PhpRenderer $view, SiteRepresentation $site, SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null)
    {
        $data = $this->getBlockData($block);
        $attachments = $block ? $block->attachments() : [];

        // Get resource data.
        $itemId = null;
        $mediaId = null;
        $imageSrc = null;
        if ($attachments && $media = $attachments[0]->media()) {
            $itemId = $attachments[0]->item()->id();
            $mediaId = $media->id();
            $imageSrc = $media->thumbnailDisplayUrl('large');
        }

        // Get the annotations.
        $annotations = json_decode($data['annotations'], true);

        // Build the form.
        $form = new Form('image_annotate_media_form');

        $element = new Element\Checkbox('o:block[__blockIndex__][o:data][media_annotations]');
        $element->setLabel('Include media annotations?');
        $element->setValue($data['media_annotations']);
        $form->add($element);

        $element = new Element\Select('o:block[__blockIndex__][o:data][title]');
        $element->setLabel('Display title'); // @translate
        $element->setEmptyOption('No title'); // @translate
        $element->setValueOptions([
            'item' => 'Item title', // @translate
            'media' => 'Media title', // @translate
        ]);
        $element->setValue($data['title']);
        $form->add($element);

        return $view->partial('common/block-layout/image-annotate-media-form', [
            'itemId' => $itemId,
            'mediaId' => $mediaId,
            'imageSrc' => $imageSrc,
            'block' => $block,
            'form' => $form,
            'annotations' => $annotations,
        ]);
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block, $templateViewScript = 'common/block-layout/image-annotate-media')
    {
        $data = $this->getBlockData($block);
        $attachments = $block ? $block->attachments() : [];

        // Get resource data.
        $item = null;
        $media = null;
        $imageSrc = null;
        $caption = null;
        if ($attachments && $media = $attachments[0]->media()) {
            $item = $media->item();
            $imageSrc = $media->thumbnailDisplayUrl('large');
            $caption = $attachments[0]->caption();
        }

        // Get the annotations.
        $annotations = json_decode($data['annotations'], true);
        if ($media && $data['media_annotations']) {
            // Append media-context annotations to page-context annotations.
            $mediaAnnotationsEntity = $this->entityManager
                ->getRepository('ImageAnnotate\Entity\ImageAnnotateMedia')
                ->findOneBy(['media' => $media->id()]);
            if ($mediaAnnotationsEntity) {
                $annotations = array_merge($annotations, $mediaAnnotationsEntity->getAnnotations());
            }
        }

        return $view->partial($templateViewScript, [
            'item' => $item,
            'media' => $media,
            'imageSrc' => $imageSrc,
            'caption' => $caption,
            'data' => $data,
            'annotations' => $annotations,
        ]);
    }

    public function getBlockData(?SitePageBlockRepresentation $block)
    {
        $data = $block ? $block->data() : [];
        return array_merge($this->defaultData, $data);
    }
}
