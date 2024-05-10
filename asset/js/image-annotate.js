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
        container.data('anno', anno);
        anno.setAnnotations(annotations);
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
        container.data('anno', anno);
        anno.setAnnotations(annotations);
        // Package the annotations on submit.
        container.closest('form').on('submit', function(e) {
            // getAnnotations() will pick up drawn annotations that have no
            // comment. These annotations have no body in Annotorious' W3C Web
            // Annotation output. In what must be a bug, Annotorious provides no
            // way to remove body-less annotations from the image. We fix this
            // by omitting annotations without a body.
            // @see https://github.com/annotorious/annotorious/issues/399
            const annotations = anno.getAnnotations().filter((annotation) => annotation.body.length);
            container.find('input.image-annotate-annotations').val(JSON.stringify(annotations));
        });
    },
}
