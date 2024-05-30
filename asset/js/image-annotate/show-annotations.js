$(document).ready(function() {

    // Initialize annotation containers on document ready.
    $('.image-annotate-container').each(function() {
        const container = $(this);
        const annotations = container.data('annotations');
        const imageSrc = container.data('imageSrc');
        ImageAnnotate.initShow(container, annotations, imageSrc);
    });

});
