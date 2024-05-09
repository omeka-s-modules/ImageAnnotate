$(document).ready(function() {

// Handle an "Apply changes" click for the imageAnnotateMedia block.
$('#attachment-confirm-panel button').on('click', function(e) {
    const block = $('.selecting-attachment').closest('.block');
    if ('imageAnnotateMedia' !== block.data('blockLayout')) {
        return; // This is not an imageAnnotateMedia block. Do nothing.
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

// Set the element that's selecting the asset.
let selectingAssetElement;
$('#content').on('click', '.asset-form-select', function () {
    selectingAssetElement = $(this).closest('.asset-form-element');
});
// Handle an asset click for the imageAnnotateAsset block.
$('#content').on('click', '.asset-list .select-asset', function (e) {
    const block = selectingAssetElement.closest('.block');
    if ('imageAnnotateAsset' !== block.data('blockLayout')) {
        return; // This is not an imageAnnotateAsset block. Do nothing.
    }
    const containerWrapper = block.find('.image-annotate-container-wrapper');
    const container = block.find('.image-annotate-container');
    const image = block.find('img.image-annotate-image');
    const assetId = parseInt(block.find('input[name$="[asset_id]"]').val(), 10);
    const assetIdCurrent = parseInt(containerWrapper.data('assetIdCurrent'), 10);
    const anno = container.data('anno');
    if (assetId === assetIdCurrent) {
        // This is the same asset. Do nothing.
        return;
    }
    // First, remove all load event handlers to prevent triggering multiple.
    // Then, on image load, destroy the current annotation interface and
    // initialize a new one.
    image.off('load');
    image.on('load', function() {
        if (anno) anno.destroy();
        ImageAnnotate.initEdit(container, []);
    });
    // Load the new image.
    image.attr('src', block.find('.selected-asset-image').attr('src'));
    containerWrapper.data('assetIdCurrent', assetId);
    // Unset the element that's selecting the asset.
    selectingAssetElement = null;
});

});
