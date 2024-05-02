$(document).ready(function() {

    $('.image-annotate-container').each(function() {
        const container = $(this);
        const image = container.find('.image-annotate-image');
        const annotations = container.data('annotations');
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
    });

});
