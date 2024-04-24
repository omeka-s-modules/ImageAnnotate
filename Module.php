<?php
namespace ImageAnnotate;

use Omeka\Module\AbstractModule;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * An Omeka S module for annotating images.
 *
 * Uses Annotorious version 2.7 (3.0 unreleased at time of development).
 *
 * @see https://annotorious.github.io/
 * @see https://github.com/annotorious/annotorious
 */
class Module extends AbstractModule
{
    public function getConfig()
    {
        return include sprintf('%s/config/module.config.php', __DIR__);
    }

    public function install(ServiceLocatorInterface $services)
    {
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.show.section_nav',
            function (Event $event) {
                $view = $event->getTarget();
                $media = $view->media;
                if (!$media->hasThumbnails()) {
                    return;
                }
                // @todo: Get annotations from data store.
                $annotations = [];
                if (!$annotations) {
                    return;
                }
                $sectionNavs = $event->getParam('section_nav');
                $sectionNavs['image-annotate-section'] = $view->translate('Image annotations');
                $event->setParam('section_nav', $sectionNavs);
            }
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.show.after',
            function (Event $event) {
                $view = $event->getTarget();
                $media = $view->media;
                if (!$media->hasThumbnails()) {
                    return;
                }
                // @todo: Get annotations from data store.
                $annotations = [];
                if (!$annotations) {
                    return;
                }

                $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
                $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
                $view->headScript()->appendFile($view->assetUrl('js/image-annotate/media-show.js', 'ImageAnnotate'));

                echo sprintf(
                    '<div id="image-annotate-section" class="section">
                        <div id="image-annotate-container">
                            <div id="image-annotate-image-wrapper">
                                <img id="image-annotate-image" src="%s" data-annotations="%s">
                            </div>
                        </div>
                    </div>',
                    $view->escapeHtml($media->thumbnailUrl('large')),
                    $view->escapeHtml(json_encode($annotations))
                );
            }
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.edit.section_nav',
            function (Event $event) {
                $view = $event->getTarget();
                $media = $view->media;
                if (!$media->hasThumbnails()) {
                    return;
                }
                $sectionNavs = $event->getParam('section_nav');
                $sectionNavs['image-annotate-section'] = $view->translate('Image annotate');
                $event->setParam('section_nav', $sectionNavs);
            }
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.edit.form.after',
            function (Event $event) {
                $view = $event->getTarget();
                $media = $view->media;
                if (!$media->hasThumbnails()) {
                    return;
                }

                // @todo: Get annotations from data store.
                $annotations = [];

                $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
                $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
                $view->headScript()->appendFile($view->assetUrl('js/image-annotate/media-edit.js', 'ImageAnnotate'));

                echo sprintf(
                    '<div id="image-annotate-section" class="section">
                        <div id="image-annotate-container">
                            <div id="image-annotate-image-wrapper">
                                <img id="image-annotate-image" src="%s" data-annotations="%s">
                            </div>
                            <input id="image-annotate-annotations" name="image_annotate_annotations" type="hidden">
                        </div>
                    </div>',
                    $view->escapeHtml($media->thumbnailUrl('large')),
                    $view->escapeHtml(json_encode($annotations))
                );
            }
        );
        $sharedEventManager->attach(
            'Omeka\Api\Adapter\MediaAdapter',
            'api.update.post',
            function (Event $event) {
                $requestData = $event->getParam('request')->getContent();
                $annotations = json_decode($requestData['image_annotate_annotations'] ?? '[]', true);
                echo '<pre>';print_r($annotations);exit;
                // @todo: Persist annotations in data store.
            }
        );
    }
}
