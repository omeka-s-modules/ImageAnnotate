const ImageAnnotate = {

    /**
     * Initialize a show annotations container.
     */
    initShow: function(container, annotations, imageSrc) {
        const annoConfig = {
            // Display annotations in read-only mode.
            readOnly: true
        };
        ImageAnnotate.init(container, annotations, imageSrc, annoConfig);
    },

    /**
     * Initialize an edit annotations container.
     */
    initEdit: function(container, annotations, imageSrc) {
        const annoConfig = {
            // Use percent in the event the image dimensions change.
            fragmentUnit: 'percent'
        };
        ImageAnnotate.init(container, annotations, imageSrc, annoConfig);
    },

    /**
     * Initialize an annotations container.
     *
     * @param {object} container A jQuery annoations container
     * @param {array} annotations An array of annotations in W3C Web Annotation format
     * @param {string} imageSrc A URL to the image
     * @param {object} annoConfig An Annotorious configuration object
     */
    init: function(container, annotations, imageSrc, annoConfig) {
        const image = container.find('.image-annotate-image');
        // On image load, destroy the current annotation interface, if any, and
        // initialize a new one. Note the use of one() to ensure the handler is
        // executed only once.
        image.one('load', function() {
            let anno = container.data('anno');
            if (anno) anno.destroy();
            // Set the image DOM element.
            annoConfig.image = image[0];
            // Remove TAG widget by setting only COMMENT.
            annoConfig.widgets = ['COMMENT'];
            anno = Annotorious.init(annoConfig);
            anno.setAnnotations(annotations);
            container.data('anno', anno);
        });
        // Set the image source, which triggers the image load event.
        image.attr('src', imageSrc);
    }
}
