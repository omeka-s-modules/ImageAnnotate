<?php
namespace ImageAnnotate\Site\BlockLayout;

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
        $mediaId = null;
        $imageSrc = null;
        if ($attachments && $media = $attachments[0]->media()) {
            $mediaId = $media->id();
            $imageSrc = $media->thumbnailUrl('large');
        }

        return sprintf(
            '%s
            <a href="#" class="expand" aria-label="expand"><h4>%s</h4></a>
            <div class="image-annotate-container-wrapper collapsible" data-media-id-current="%s" data-api-endpoint-url="%s">
                %s
            </div>',
            $view->blockAttachmentsForm($block, false, [], 1),
            $view->translate('Annotate image'),
            $view->escapeHtml($mediaId),
            $view->escapeHtml($view->url('api-local')),
            $view->partial('common/image-annotate', [
                'imageSrc' => $imageSrc,
                'annotations' => $annotations,
                'inputName' => 'o:block[__blockIndex__][o:data][annotations]',
            ])
        );
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block, $templateViewScript = 'common/block-layout/image-annotate')
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
        $imageSrc = null;
        if ($attachments && $media = $attachments[0]->media()) {
            $imageSrc = $media->thumbnailUrl('large');
        }

        return $view->partial('common/image-annotate', [
            'imageSrc' => $imageSrc,
            'annotations' => $annotations,
        ]);
    }
}