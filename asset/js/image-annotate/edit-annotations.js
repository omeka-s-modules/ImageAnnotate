$(document).ready(function() {

    $('.image-annotate-container').each(function() {
        const thisContainer = $(this);
        const annotations = thisContainer.data('annotations');
        ImageAnnotate.initEdit(thisContainer, annotations);
    });

});
