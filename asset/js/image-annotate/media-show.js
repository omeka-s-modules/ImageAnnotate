$(document).ready(function() {

    const image = $('#image-annotate-image');

    // Initiate Annotorious on load.
    var anno = Annotorious.init({
        image: image[0],
        readOnly: true
    });
    anno.setAnnotations(image.data('annotations'));

});
