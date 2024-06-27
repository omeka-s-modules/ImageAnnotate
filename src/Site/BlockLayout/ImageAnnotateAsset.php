<?php
namespace ImageAnnotate\Site\BlockLayout;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Form\Element\Asset;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Site\BlockLayout\TemplateableBlockLayoutInterface;

class ImageAnnotateAsset extends AbstractBlockLayout implements TemplateableBlockLayoutInterface
{
    protected $defaultData = [
        'annotations' => '[]',
        'asset_id' => null,
        'caption' => null,
    ];

    public function getLabel()
    {
        return 'Image annotate asset'; // @translate
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

        // Get resource data.
        $assetId = $data['asset_id'];
        $imageSrc = null;
        if ($assetId && $asset = $view->api()->searchOne('assets', ['id' => $assetId])->getContent()) {
            $imageSrc = $asset->assetUrl();
        }

        // Get the annotations.
        $annotations = json_decode($data['annotations'], true);

        // Build the form
        $form = new Form('image_annotate_media_form');

        $element = new Asset('o:block[__blockIndex__][o:data][asset_id]');
        $element->setValue($assetId);
        $form->add($element);

        $element = new Element\Textarea('o:block[__blockIndex__][o:data][caption]');
        $element->setLabel('Caption'); // @translate
        $element->setValue($data['caption']);
        $form->add($element);

        return $view->partial('common/block-layout/image-annotate-asset-form', [
            'assetId' => $assetId,
            'form' => $form,
            'imageSrc' => $imageSrc,
            'annotations' => $annotations,
        ]);
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block, $templateViewScript = 'common/block-layout/image-annotate-asset')
    {
        $data = $this->getBlockData($block);

        $assetId = $data['asset_id'];
        $caption = $data['caption'];
        $annotations = json_decode($data['annotations'], true);

        $imageSrc = null;
        if ($assetId && $asset = $view->api()->searchOne('assets', ['id' => $assetId])->getContent()) {
            $imageSrc = $asset->assetUrl();
        }

        return $view->partial($templateViewScript, [
            'imageSrc' => $imageSrc,
            'caption' => $caption,
            'annotations' => $annotations,
        ]);
    }

    public function getBlockData(?SitePageBlockRepresentation $block)
    {
        $data = $block ? $block->data() : [];
        return array_merge($this->defaultData, $data);
    }
}
