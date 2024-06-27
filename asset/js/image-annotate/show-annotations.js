$(document).ready(function() {

    // Initialize annotation containers on document ready.
    $('.image-annotate-container').each(function() {
        const container = $(this);
        const annotations = container.data('annotations');
        const imageSrc = container.data('imageSrc');
        ImageAnnotate.initShow(container, annotations, imageSrc);
    });

    $('.image-annotate-view-annotations').on('click', function(e) {
        const thisButton = $(this);
        const wrapper = thisButton.closest('.image-annotate-media-wrapper');
        wrapper.children('.image-annotate-media-annotations').show();
        wrapper.children('.image-annotate-media-render').hide();
        wrapper.children('.image-annotate-view-annotations').hide();
        wrapper.children('.image-annotate-view-media').show();
    });

    $('.image-annotate-view-media').on('click', function(e) {
        const thisButton = $(this);
        const wrapper = thisButton.closest('.image-annotate-media-wrapper');
        wrapper.children('.image-annotate-media-annotations').hide();
        wrapper.children('.image-annotate-media-render').show();
        wrapper.children('.image-annotate-view-annotations').show();
        wrapper.children('.image-annotate-view-media').hide();
    });

});
