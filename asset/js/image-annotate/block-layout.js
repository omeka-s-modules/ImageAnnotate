$(document).ready(function() {

// @todo: Handle initial "Apply changes"
$('#attachment-confirm-panel button').on('click', function(e) {
    const block = $('.selecting-attachment').closest('.block');
    if ('imageAnnotate' !== block.data('blockLayout')) {
        return; // This is not an imageAnnotate block. Do nothing.
    }
    const containerWrapper = block.find('.image-annotate-container-wrapper');
    const mediaId = parseInt(block.find('input.media').val(), 10);
    const mediaIdCurrent = parseInt(containerWrapper.data('mediaIdCurrent'), 10);
    const apiEndpointUrl = containerWrapper.data('apiEndpointUrl');
    if (mediaId === mediaIdCurrent) {
        return; // This is the same media. Do nothing.
    }
    // Get the large thumbnail URL from the API.
    $.get(`${apiEndpointUrl}/media/${mediaId}`, function(data) {
        const container = block.find('.image-annotate-container');
        const image = block.find('img.image-annotate-image');
        // Destroy the current annotation interface and initialize a new one.
        image.off('load');
        image.on('load', function() {
            const anno = container.data('anno');
            anno.destroy();
            ImageAnnotate.initEdit(container, []);
            containerWrapper.data('mediaIdCurrent', mediaId);
        });
        image.attr('src', data['o:thumbnail_urls']['large']);
    });
});

// @todo: Handle "Delete attachment"
$('#blocks').on('click', '.delete', function(e) {
    const thisButton = $(this);
    const block = thisButton.closest('.block');
    if ('imageAnnotate' !== block.data('blockLayout')) {
        return;
    }
});

// @todo: Handle "Open attachment options" > "Apply changes" i.e. change item/media

});
