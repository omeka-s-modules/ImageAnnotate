<?php
namespace ImageAnnotate\Entity;

use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Media;

/**
 * @Entity
 */
class ImageAnnotateMedia extends AbstractEntity
{
    /**
     * @Id
     * @Column(
     *     type="integer",
     *     options={
     *         "unsigned"=true
     *     }
     * )
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @OneToOne(
     *     targetEntity="Omeka\Entity\Media",
     * )
     * @JoinColumn(
     *     unique=true,
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $media;

    public function setMedia(Media $media) : void
    {
        $this->media = $media;
    }

    public function getMedia() : Media
    {
        return $this->media;
    }

    /**
     * @Column(
     *     type="json",
     *     nullable=false
     * )
     */
    protected $annotations;

    public function setAnnotations(array $annotations) : void
    {
        $this->annotations = $annotations;
    }

    public function getAnnotations() : array
    {
        return $this->annotations;
    }
}
