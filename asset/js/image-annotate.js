const ImageAnnotate = {

    /**
     * Initialize a show annotations container.
     *
     * @param {jQuery object} container
     * @param {array} annotations
     */
    initShow: function(container, annotations) {
        const image = container.find('.image-annotate-image');
        var anno = Annotorious.init({
            image: image[0],
            readOnly: true
        });
        anno.setAnnotations(annotations);
        container.data('anno', anno);
    },

    /**
     * Initialize an edit annotations container.
     *
     * @param {jQuery object} container
     * @param {array} annotations
     */
    initEdit: function(container, annotations) {
        const image = container.find('.image-annotate-image');
        const anno = Annotorious.init({
            image: image[0],
            // Use percent in the event the image dimensions change.
            fragmentUnit: 'percent',
            // Remove TAG widget by setting only COMMENT.
            widgets: ['COMMENT'],
        });
        anno.setAnnotations(annotations);
        container.data('anno', anno);
    },

    /**
     * Reinitialize an edit annotations container.
     *
     * @param {jQuery object} container
     * @param {array} annotations
     * @param {string} imageSrc
     */
    reinitEdit: function(container, annotations, imageSrc) {
        const image = container.find('img.image-annotate-image');
        const anno = container.data('anno');
        // First, remove all load event handlers to prevent triggering multiple.
        // Then, on image load, destroy the current annotation interface and
        // initialize a new one.
        image.off('load');
        image.on('load', function() {
            if (anno) anno.destroy();
            ImageAnnotate.initEdit(container, annotations);
        });
        image.attr('src', imageSrc);
    }
}
