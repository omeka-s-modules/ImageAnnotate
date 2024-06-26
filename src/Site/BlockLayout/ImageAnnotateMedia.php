<?php
namespace ImageAnnotate\Site\BlockLayout;

use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Site\BlockLayout\TemplateableBlockLayoutInterface;

class ImageAnnotateMedia extends AbstractBlockLayout implements TemplateableBlockLayoutInterface
{
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
        $data = $block ? $block->data() : [];
        $attachments = $block ? $block->attachments() : [];

        $annotations = [];
        if (isset($data['annotations']) && is_string($data['annotations'])) {
            $annotations = json_decode($data['annotations'], true);
        }

        $itemId = null;
        $mediaId = null;
        $imageSrc = null;
        if ($attachments && $media = $attachments[0]->media()) {
            $itemId = $attachments[0]->item()->id();
            $mediaId = $media->id();
            $imageSrc = $media->thumbnailDisplayUrl('large');
        }

        $displayTitleSelect = (new Element\Select('o:block[__blockIndex__][o:data][display_title]'))
            ->setLabel('Display title') // @translate
            ->setEmptyOption('No title') // @translate
            ->setValueOptions([
                'item' => 'Item title', // @translate
                'media' => 'Media title', // @translate
            ])
            ->setValue($data['display_title'] ?? '');

        return sprintf(
            '%s
            <a href="#" class="expand" aria-label="expand">
                <h4>%s</h4>
            </a>
            <div class="collapsible">
                %s
            </div>
            <a href="#" class="expand" aria-label="expand">
                <h4>%s</h4>
            </a>
            <div class="image-annotate-container-wrapper collapsible"
                data-item-id-original="%s"
                data-media-id-original="%s"
                data-media-id-current="%s"
                data-api-endpoint-url="%s">
                %s
            </div>',
            $view->blockAttachmentsForm($block, false, [], 1),
            $view->translate('Options'),
            $view->formRow($displayTitleSelect),
            $view->translate('Annotate image'),
            $view->escapeHtml($itemId),
            $view->escapeHtml($mediaId),
            $view->escapeHtml($mediaId),
            $view->escapeHtml($view->url('api-local')),
            $view->partial('common/image-annotate', [
                'imageSrc' => $imageSrc,
                'annotations' => $annotations,
                'inputName' => 'o:block[__blockIndex__][o:data][annotations]',
            ])
        );
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block, $templateViewScript = 'common/block-layout/image-annotate-media')
    {
        $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
        $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate.js', 'ImageAnnotate'));
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/show-annotations.js', 'ImageAnnotate'));

        $data = $block ? $block->data() : [];
        $attachments = $block ? $block->attachments() : [];

        $annotations = [];
        if (isset($data['annotations']) && is_string($data['annotations'])) {
            $annotations = json_decode($data['annotations'], true);
        }

        $displayTitle = null;
        if (isset($data['display_title']) && in_array($data['display_title'], ['item', 'media'])) {
            $displayTitle = $data['display_title'];
        }

        $item = null;
        $media = null;
        $imageSrc = null;
        $caption = null;
        if ($attachments && $media = $attachments[0]->media()) {
            $item = $media->item();
            $imageSrc = $media->thumbnailDisplayUrl('large');
            $caption = $attachments[0]->caption();
        }

        return $view->partial($templateViewScript, [
            'imageSrc' => $imageSrc,
            'annotations' => $annotations,
            'item' => $item,
            'media' => $media,
            'displayTitle' => $displayTitle,
            'caption' => $caption,
        ]);
    }
}
