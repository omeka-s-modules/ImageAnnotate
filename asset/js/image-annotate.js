const ImageAnnotate = {
    /**
     * Initialize a show annotations container.
     *
     * @param {jQuery object} container
     */
    initShow: function(container, annotations) {
        const image = container.find('.image-annotate-image');
        // Initiate Annotorious on load.
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
     */
    initEdit: function(container, annotations) {
        const image = container.find('.image-annotate-image');
        // Initiate Annotorious on load.
        const anno = Annotorious.init({
            image: image[0],
            widgets: ['COMMENT'], // Remove TAG widget by setting only COMMENT
        });
        anno.setAnnotations(annotations);
        // Package the annotations on submit.
        container.closest('form').on('submit', function(e) {
            container.find('input.image-annotate-annotations')
                .val(JSON.stringify(anno.getAnnotations()));
        });
        container.data('anno', anno);
    },
}
