$(document).ready(function() {

// Handle
$('#attachment-confirm-panel button').on('click', function(e) {
    const block = $('.selecting-attachment').closest('.block');
    if ('imageAnnotate' !== block.data('blockLayout')) {
        return; // This is not an imageAnnotate block. Do nothing.
    }
    const containerWrapper = block.find('.image-annotate-container-wrapper');
    const container = block.find('.image-annotate-container');
    const image = block.find('img.image-annotate-image');
    const mediaId = parseInt(block.find('input.media').val(), 10);
    const mediaIdCurrent = parseInt(containerWrapper.data('mediaIdCurrent'), 10);
    const apiEndpointUrl = containerWrapper.data('apiEndpointUrl');
    const anno = container.data('anno');
    if (!mediaId) {
        // This item has no media.
        image.off('load');
        image.attr('src', '');
        containerWrapper.data('mediaIdCurrent', '');
        return;
    }
    if (mediaId === mediaIdCurrent) {
        // This is the same media. Do nothing.
        return;
    }
    // Get the large thumbnail URL from the API.
    $.get(`${apiEndpointUrl}/media/${mediaId}`, function(data) {
        // First, remove all load event handlers to prevent triggering multiple.
        // Then, on image load, destroy the current annotation interface and
        // initialize a new one.
        image.off('load');
        image.on('load', function() {
            if (anno) anno.destroy();
            ImageAnnotate.initEdit(container, []);
        });
        // Load the new image.
        image.attr('src', data['thumbnail_display_urls']['large']);
        containerWrapper.data('mediaIdCurrent', mediaId);
    });
});

});
