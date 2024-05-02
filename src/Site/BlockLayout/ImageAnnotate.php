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
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/edit-annotations.js', 'ImageAnnotate'));
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/block-layout.js', 'ImageAnnotate'));
    }

    public function form(PhpRenderer $view, SiteRepresentation $site, SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null)
    {
        $data = $block ? $block->data() : [];

        // Get the annotations.
        $annotations = [];
        if (isset($data['annotations'])) {
            $annotations = $data['annotations'];
        }
        $annotations = json_decode($data['annotations'], true);
        if (!is_array($annotations)) {
            $annotations = [];
        }

        // Get the image source.
        $attachments = $block ? $block->attachments() : [];
        $imageSrc = '';
        if ($attachments) {
            if ($attachments[0]->media()) {
                $imageSrc = $attachments[0]->media()->thumbnailUrl('large');
            }
        }

        return sprintf(
            '%s<a href="#" class="expand" aria-label="expand"><h4>%s</h4></a><div class="collapsible">%s</div>',
            $view->blockAttachmentsForm($block, false, [], 1),
            $view->translate('Annotate image'),
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
