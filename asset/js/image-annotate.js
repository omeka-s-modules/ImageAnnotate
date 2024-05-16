const ImageAnnotate = {

    /**
     * Initialize a show annotations container.
     *
     * @param {jQuery object} container
     * @param {array} annotations
     * @param {string} imageSrc
     */
    initShow: function(container, annotations, imageSrc) {
        const getAnno = function(image) {
            return Annotorious.init({
                image: image[0],
                // Display annotations in read-only mode.
                readOnly: true,
                // Remove TAG widget by setting only COMMENT.
                widgets: ['COMMENT'],
            });
        };
        ImageAnnotate.init(container, annotations, imageSrc, getAnno);
    },

    /**
     * Initialize an edit annotations container.
     *
     * @param {jQuery object} container
     * @param {array} annotations
     * @param {string} imageSrc
     */
    initEdit: function(container, annotations, imageSrc) {
        const getAnno = function(image) {
            return Annotorious.init({
                image: image[0],
                // Use percent in the event the image dimensions change.
                fragmentUnit: 'percent',
                // Remove TAG widget by setting only COMMENT.
                widgets: ['COMMENT'],
            });
        };
        ImageAnnotate.init(container, annotations, imageSrc, getAnno);
    },

    /**
     * Initialize an annotations container.
     *
     * @param {jQuery object} container An annoations container
     * @param {array} annotations An array of annotations in W3C Web Annotation format
     * @param {string} imageSrc A URL to the image
     * @param {function} getAnno A function that returns the Annotorious object
     */
    init: function(container, annotations, imageSrc, getAnno) {
        const image = container.find('.image-annotate-image');
        // On image load, destroy the current annotation interface, if any, and
        // initialize a new one. Note the use of one() to ensure the handler is
        // executed only once.
        image.one('load', function() {
            let anno = container.data('anno');
            if (anno) anno.destroy();
            anno = getAnno(image);
            anno.setAnnotations(annotations);
            container.data('anno', anno);
        });
        // Set the image source, which triggers the image load event.
        image.attr('src', imageSrc);
    }
}
