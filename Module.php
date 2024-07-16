<?php
namespace ImageAnnotate;

use ImageAnnotate\Entity\ImageAnnotateMedia;
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
        $sql = <<<'SQL'
CREATE TABLE image_annotate_media (id INT UNSIGNED AUTO_INCREMENT NOT NULL, media_id INT NOT NULL, annotations LONGTEXT NOT NULL COMMENT '(DC2Type:json)', UNIQUE INDEX UNIQ_B55D6BEAEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE image_annotate_media ADD CONSTRAINT FK_B55D6BEAEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE;
SQL;
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec($sql);
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec('DROP TABLE IF EXISTS image_annotate_media;');
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.show.section_nav',
            [$this, 'viewShowSectionNav']
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.show.after',
            [$this, 'viewShowAfter']
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.edit.section_nav',
            [$this, 'viewEditSectionNav']
        );
        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.edit.form.after',
            [$this, 'viewEditFormAfter']
        );
        $sharedEventManager->attach(
            'Omeka\Api\Adapter\MediaAdapter',
            'api.update.post',
            [$this, 'apiUpdatePost']
        );
        $sharedEventManager->attach(
            '*',
            'api.context',
            [$this, 'apiContext']
        );
        $sharedEventManager->attach(
            'Omeka\Api\Representation\MediaRepresentation',
            'rep.resource.json',
            [$this, 'repResourceJson']
        );
        $sharedEventManager->attach(
            '*',
            'iiif_presentation.3.media.canvas',
            [$this, 'iiifPresentationMediaCanvas']
        );
    }

    /**
     * Add section nav to media show page.
     *
     * @param Event $event
     */
    public function viewShowSectionNav(Event $event)
    {
        $view = $event->getTarget();
        $media = $view->media;
        if (!$media->hasThumbnails()) {
            return;
        }

        // Get annotations, if any.
        $imageAnnotateMedia = $this->getImageAnnotateMedia($media->id());
        $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
        if (!$annotations) {
            return;
        }

        $sectionNavs = $event->getParam('section_nav');
        $sectionNavs['image-annotate-section'] = $view->translate('Image annotations');
        $event->setParam('section_nav', $sectionNavs);
    }

    /**
     * Add section to media show page.
     *
     * @param Event $event
     */
    public function viewShowAfter(Event $event)
    {
        $view = $event->getTarget();
        $media = $view->media;
        if (!$media->hasThumbnails()) {
            return;
        }

        // Get annotations, if any.
        $imageAnnotateMedia = $this->getImageAnnotateMedia($media->id());
        $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
        if (!$annotations) {
            return;
        }

        $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
        $view->headLink()->appendStylesheet($view->assetUrl('css/style.css', 'ImageAnnotate'));
        $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate.js', 'ImageAnnotate'));
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/show-annotations.js', 'ImageAnnotate'));
        echo sprintf(
            '<div id="image-annotate-section" class="section">%s</div>',
            $view->partial('common/image-annotate', [
                'imageSrc' => $media->thumbnailDisplayUrl('large'),
                'annotations' => $annotations,
            ])
        );
    }

    /**
     * Add section nav to media edit page.
     *
     * @param Event $event
     */
    public function viewEditSectionNav(Event $event)
    {
        $view = $event->getTarget();
        $media = $view->media;
        if (!$media->hasThumbnails()) {
            return;
        }

        $sectionNavs = $event->getParam('section_nav');
        $sectionNavs['image-annotate-section'] = $view->translate('Annotate image');
        $event->setParam('section_nav', $sectionNavs);
    }

    /**
     * Add section to media edit page.
     *
     * @param Event $event
     */
    public function viewEditFormAfter(Event $event)
    {
        $view = $event->getTarget();
        $media = $view->media;
        if (!$media->hasThumbnails()) {
            return;
        }

        // Get annotations, if any.
        $imageAnnotateMedia = $this->getImageAnnotateMedia($media->id());
        $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];

        $view->headLink()->appendStylesheet('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.css');
        $view->headLink()->appendStylesheet($view->assetUrl('css/style.css', 'ImageAnnotate'));
        $view->headScript()->appendFile('//cdn.jsdelivr.net/npm/@recogito/annotorious@2.7.13/dist/annotorious.min.js');
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate.js', 'ImageAnnotate'));
        $view->headScript()->appendFile($view->assetUrl('js/image-annotate/edit-annotations.js', 'ImageAnnotate'));
        echo sprintf(
            '<div id="image-annotate-section" class="section">%s</div>',
            $view->partial('common/image-annotate', [
                'imageSrc' => $media->thumbnailDisplayUrl('large'),
                'annotations' => $annotations,
                'inputName' => sprintf('image_annotate_annotations[%s]', $media->id()),
            ])
        );
    }

    /**
     * Persist media annotations.
     *
     * @param Event $event
     */
    public function apiUpdatePost(Event $event)
    {
        $requestData = $event->getParam('request')->getContent();
        $media = $event->getParam('response')->getContent();

        $annotations = $requestData['image_annotate_annotations'][$media->getId()] ?? null;
        $annotations = json_decode($annotations, true);
        if (!is_array($annotations)) {
            return;
        }

        $imageAnnotateMedia = $this->getImageAnnotateMedia($media->getId());
        if (!$imageAnnotateMedia) {
            $imageAnnotateMedia = new ImageAnnotateMedia;
            $imageAnnotateMedia->setMedia($media);
        }
        $imageAnnotateMedia->setAnnotations($annotations);
        $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
        $entityManager->persist($imageAnnotateMedia);
        $entityManager->flush();
    }

    /**
     * Get ImageAnnotateMedia entity.
     *
     * @param int $mediaId
     * @return ImageAnnotateMedia
     */
    public function getImageAnnotateMedia(int $mediaId) : ?ImageAnnotateMedia
    {
        return $this->getServiceLocator()
            ->get('Omeka\EntityManager')
            ->getRepository('ImageAnnotate\Entity\ImageAnnotateMedia')
            ->findOneBy(['media' => $mediaId]);
    }

    /**
     * Add "o-module-item_annotate" namespace to the API @context.
     *
     * @param Event $event
     */
    public function apiContext(Event $event)
    {
        $context = $event->getParam('context');
        $context['o-module-item_annotate'] = 'http://omeka.org/s/vocabs/module/item_annotate#';
        $event->setParam('context', $context);
    }

    /**
     * Add annotations to the API JSON-LD.
     *
     * @param Event $event
     */
    public function repResourceJson(Event $event)
    {
        $media = $event->getTarget();
        $jsonLd = $event->getParam('jsonLd');
        // Get annotations, if any.
        $imageAnnotateMedia = $this->getImageAnnotateMedia($media->id());
        $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
        if (!$annotations) {
            return;
        }
        $jsonLd['o-module-image_annotate:annotation'] = $annotations;
        $event->setParam('jsonLd', $jsonLd);
    }

    /**
     * Add annotations to IIIF output provided by the IIIF Presentation module.
     *
     * @param Event $event
     */
    public function iiifPresentationMediaCanvas(Event $event)
    {
        $canvas = $event->getParam('canvas');
        $media = $event->getParam('media');

        $imageAnnotateMedia = $this->getImageAnnotateMedia($media->id());
        $annotations = $imageAnnotateMedia ? $imageAnnotateMedia->getAnnotations() : [];
        if (!$annotations) {
            return;
        }

        // Add motivation and modify the target for each annotation. We must
        // modify the target provided by Annatorious to correctly reference the
        // canvas ID generated by IIIF Presentation. Here we append the percent
        // FragmentSelector generated by Annatorious to complete the target.
        // Note that as of time of development, Mirador does not support the
        // percent FragmentSelector (e.g. xywh=percent:10,20,30,40).
        foreach ($annotations as &$annotation) {
            $annotation['motivation'] = 'commenting';
            $annotation['target'] = sprintf(
                '%s#%s',
                $canvas['id'],
                $annotation['target']['selector']['value']
            );
        }
        $canvas['annotations'] = [
            'type' => 'AnnotationPage',
            'items' => $annotations,
        ];
        $event->setParam('canvas', $canvas);
    }
}
