$(document).ready(function() {

    $('.image-annotate-container').each(function() {
        const container = $(this);
        const image = container.find('.image-annotate-image');
        const annotations = container.data('annotations');
        // Initiate Annotorious on load.
        var anno = Annotorious.init({
            image: image[0],
            readOnly: true
        });
        anno.setAnnotations(annotations);
    });

});
