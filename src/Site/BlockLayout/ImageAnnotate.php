<?php
namespace ImageAnnotate\Site\BlockLayout;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Site\BlockLayout\TemplateableBlockLayoutInterface;

class ImageAnnotate extends AbstractBlockLayout implements TemplateableBlockLayoutInterface
{
    public function getLabel()
    {
        return 'Image annotate'; // @translate
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

        // Get the annotations.
        $annotations = [];
        if (isset($data['annotations']) && is_string($data['annotations'])) {
            $annotations = json_decode($data['annotations'], true);
        }

        // Get the image source.
        $attachments = $block ? $block->attachments() : [];
        $mediaId = '';
        $imageSrc = '';
        if ($attachments && $attachments[0]->media()) {
            $mediaId = $attachments[0]->media()->id();
            $imageSrc = $attachments[0]->media()->thumbnailUrl('large');
        }

        return sprintf(
            '%s
            <a href="#" class="expand" aria-label="expand"><h4>%s</h4></a>
            <div class="image-annotate-container-wrapper collapsible"
                data-media-id-current="%s"
                data-api-endpoint-url="%s">%s</div>',
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
    }
}
