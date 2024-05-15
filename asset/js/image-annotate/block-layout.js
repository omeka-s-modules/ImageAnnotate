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
        ImageAnnotate.initEdit(container, [], data['thumbnail_display_urls']['large']);
        containerWrapper.data('mediaIdCurrent', mediaId);
    });
});

// Set the element that's selecting the asset.
let selectingAssetElement;
$('#content').on('click', '.asset-form-select', function () {
    selectingAssetElement = $(this).closest('.asset-form-element');
});
// Reduce the size of all selected asset images.
$('#content').find('.selected-asset-image').css('max-width', '100px');
// Handle an asset click for the imageAnnotateAsset block.
$('#content').on('click', '.asset-list .select-asset', function (e) {
    const block = selectingAssetElement.closest('.block');
    if ('imageAnnotateAsset' !== block.data('blockLayout')) {
        return; // This is not an imageAnnotateAsset block. Do nothing.
    }
    const containerWrapper = block.find('.image-annotate-container-wrapper');
    const container = block.find('.image-annotate-container');
    const assetId = parseInt(block.find('input[name$="[asset_id]"]').val(), 10);
    const assetIdCurrent = parseInt(containerWrapper.data('assetIdCurrent'), 10);
    // Reduce the size of the selected asset image.
    block.find('.selected-asset-image').css('max-width', '100px');
    if (assetId === assetIdCurrent) {
        // This is the same asset. Do nothing.
        return;
    }
    ImageAnnotate.initEdit(container, [], block.find('.selected-asset-image').attr('src'));
    containerWrapper.data('assetIdCurrent', assetId);
    // Unset the element that's selecting the asset.
    selectingAssetElement = null;
});

// Handle an annotation reset.
$('#content').on('o-module-image_annotate:reset', '.image-annotate-container', function(e) {
    const block = $(this).closest('.block');
    if ('imageAnnotateMedia' === block.data('blockLayout')) {
        // Update the attachment.
        const attachment = block.find('.attachment');
        const containerWrapper = block.find('.image-annotate-container-wrapper');
        const itemIdOriginal = parseInt(containerWrapper.data('itemIdOriginal'), 10);
        const mediaIdOriginal = parseInt(containerWrapper.data('mediaIdOriginal'), 10);
        const apiEndpointUrl = containerWrapper.data('apiEndpointUrl');
        $.get(`${apiEndpointUrl}/media/${mediaIdOriginal}`, function(data) {
            attachment.find('.item-title > img').attr('src', data['thumbnail_display_urls']['square']);
            attachment.find('input.item').val(itemIdOriginal);
            attachment.find('input.media').val(mediaIdOriginal);
        });
    }
    if ('imageAnnotateAsset' === block.data('blockLayout')) {
        // Update the asset.
        const containerWrapper = block.find('.image-annotate-container-wrapper');
        const assetIdOriginal = parseInt(containerWrapper.data('assetIdOriginal'), 10);
        const apiEndpointUrl = containerWrapper.data('apiEndpointUrl');
        $.get(`${apiEndpointUrl}/assets/${assetIdOriginal}`, function(data) {
            block.find('.selected-asset-image').attr('src', data['o:asset_url']);
            block.find('.selected-asset-name').text(data['o:name']);
            block.find('.asset-form-element > input').val(data['o:id']);
        });
    }
});

// @todo: Handle delete attachment?
// @todo: Handle asset clear?

});
