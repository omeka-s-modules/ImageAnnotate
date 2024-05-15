$(document).ready(function() {

    // Initialize annotation containers on document ready.
    $('.image-annotate-container').each(function() {
        const container = $(this);
        const annotations = container.data('annotations');
        const imageSrc = container.data('imageSrc');
        ImageAnnotate.initEdit(container, annotations, imageSrc);
    });

    // Package the annotations on form submit.
    $('form').on('submit', function() {
        $('.image-annotate-container').each(function() {
            const container = $(this);
            const anno = container.data('anno');
            // getAnnotations() will pick up drawn annotations that have no
            // comment. These annotations have no body in Annotorious' W3C Web
            // Annotation output. In what must be a bug, Annotorious provides no
            // way to remove body-less annotations from the image. We fix this
            // by omitting annotations without a body.
            // @see https://github.com/annotorious/annotorious/issues/399
            const annotations = anno.getAnnotations().filter((annotation) => annotation.body.length);
            container.find('input.image-annotate-annotations').val(JSON.stringify(annotations));
        });
    });

    // Reset an annotation container on button click.
    $('.image-annotate-reset-button').on('click', function(e) {
        e.preventDefault();
        const container = $(this).closest('.image-annotate-container');
        const annotations = container.data('annotations');
        const imageSrc = container.data('imageSrc');
        ImageAnnotate.initEdit(container, annotations, imageSrc);
        container.trigger('o-module-image_annotate:reset');
    });

});
