$(document).ready(function() {

    const image = $('#image-annotate-image');

    // Initiate Annotorious on load.
    var anno = Annotorious.init({
        image: image[0],
        widgets: ['COMMENT'], // Remove TAG widget by setting only COMMENT
    });
    anno.setAnnotations(image.data('annotations'));

    // Package the annotations on submit.
    $('.resource-form').on('submit', function(e) {
        $('#image-annotate-annotations').val(JSON.stringify(anno.getAnnotations()));
    });

});
