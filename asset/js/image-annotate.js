$(document).ready(function() {

    // Initiate Annotorious on load.
    var anno = Annotorious.init({
        image: document.getElementById('image-annotate-image'),
        widgets: ['COMMENT'], // Remove TAG widget by setting only COMMENT
    });
    anno.setAnnotations($('image-annotate-annotations').val());

    // Package the annotations on submit.
    $('.resource-form').on('submit', function(e) {
        $('#image-annotate-annotations').val(JSON.stringify(anno.getAnnotations()));
    });

});
