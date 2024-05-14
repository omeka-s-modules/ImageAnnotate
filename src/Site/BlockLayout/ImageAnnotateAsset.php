<?php
namespace ImageAnnotate\Site\BlockLayout;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Form\Element\Asset;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Site\BlockLayout\TemplateableBlockLayoutInterface;

class ImageAnnotateAsset extends AbstractBlockLayout implements TemplateableBlockLayoutInterface
{
    public function getLabel()
    {
        return 'Image annotate asset'; // @translate
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

        $assetId = null;
        if (isset($data['asset_id']) && is_numeric($data['asset_id'])) {
            $assetId = $data['asset_id'];
        }

        $imageSrc = null;
        if ($assetId && $asset = $view->api()->searchOne('assets', ['id' => $assetId])->getContent()) {
            $imageSrc = $asset->assetUrl();
        }

        $annotations = [];
        if (isset($data['annotations']) && is_string($data['annotations'])) {
            $annotations = json_decode($data['annotations'], true);
        }

        $assetElement = new Asset('o:block[__blockIndex__][o:data][asset_id]');
        $assetElement->setValue($assetId);

        return sprintf('
            <a href="#" class="collapse" aria-label="expand"><h4>%s</h4></a>
            <div class="collapsible">
                %s
            </div>
            <a href="#" class="expand" aria-label="expand"><h4>%s</h4></a>
            <div class="image-annotate-container-wrapper collapsible"
                data-asset-id-original="%s"
                data-asset-id-current="%s"
                data-api-endpoint-url="%s">
                %s
            </div>',
            $view->translate('Asset'),
            $view->formElement($assetElement),
            $view->translate('Annotate image'),
            $view->escapeHtml($assetId),
            $view->escapeHtml($assetId),
            $view->escapeHtml($view->url('api-local')),
            $view->partial('common/image-annotate', [
                'imageSrc' => $imageSrc,
                'annotations' => $annotations,
                'inputName' => 'o:block[__blockIndex__][o:data][annotations]',
            ])
        );
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block, $templateViewScript = 'common/block-layout/image-annotate-asset')
    {
    }
}
