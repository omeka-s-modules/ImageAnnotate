$(document).ready(function() {

    $('.image-annotate-container').each(function() {
        const container = $(this);
        const annotations = container.data('annotations');
        ImageAnnotate.initShow(container, annotations);
    });

});
