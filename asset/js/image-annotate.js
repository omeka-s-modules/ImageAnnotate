const ImageAnnotate = {

    /**
     * Initialize a show annotations container.
     */
    initShow: function(container, annotations, imageSrc) {
        const annoConfig = {
            // Display annotations in read-only mode.
            readOnly: true
        };
        ImageAnnotate.init(container, annotations, imageSrc, annoConfig);
    },

    /**
     * Initialize an edit annotations container.
     */
    initEdit: function(container, annotations, imageSrc) {
        const annoConfig = {
            // Use percent in the event the image dimensions change.
            fragmentUnit: 'percent'
        };
        ImageAnnotate.init(container, annotations, imageSrc, annoConfig);
    },

    /**
     * Initialize an annotations container.
     *
     * @param {object} container A jQuery annoations container
     * @param {array} annotations An array of annotations in W3C Web Annotation format
     * @param {string} imageSrc A URL to the image
     * @param {object} annoConfig An Annotorious configuration object
     */
    init: function(container, annotations, imageSrc, annoConfig) {
        const image = container.find('.image-annotate-image');
        // On image load, destroy the current annotation interface, if any, and
        // initialize a new one. Note the use of one() to ensure the handler is
        // executed only once.
        image.one('load', function() {
            let anno = container.data('anno');
            if (anno) anno.destroy();
            // Set the image DOM element.
            annoConfig.image = image[0];
            // Remove TAG widget by setting only COMMENT.
            annoConfig.widgets = ['COMMENT', ImageAnnotate.linkingWidget];
            anno = Annotorious.init(annoConfig);
            anno.setAnnotations(annotations);
            container.data('anno', anno);
        });
        // Set the image source, which triggers the image load event.
        image.attr('src', imageSrc);
    },

    /**
     * Enable a widget that adds a linking URL to annotations.
     *
     * @see https://annotorious.github.io/guides/editor-widgets/
     * @see https://www.w3.org/TR/annotation-model/#purpose-for-external-web-resources
     * @param {object} args
     * @returns DOM object
     */
    linkingWidget: function(args) {
        let previousBody;
        if (args.annotation) {
            previousBody = args.annotation.bodies.find(function(body) {
                return 'SpecificResource' === body.type && 'linking' === body.purpose;
            });
        }
        const widget = $('<div>', {class: 'linking-widget'});
        // Render the read-only widget.
        if (args.readOnly) {
            let url;
            try {
                url = new URL(previousBody ? previousBody.source : '');
            } catch (err) {
                // The URL is invalid. Return the empty widget.
                return widget[0];
            }
            const link = $('<a>', {href: url.href, target: '_blank'});
            link.text(url.href);
            link.appendTo(widget);
            return widget[0];
        }
        // Render the editable widget.
        const label = $('<label>', {class: 'link-label'});
        const input = $('<input>', {type: 'text', class: 'link-input'})
        label.text('Link');
        label.appendTo(widget);
        input.val(previousBody ? previousBody.source : '');
        input.appendTo(label);
        // Handle a change event on the link input.
        input.on('change', function(e) {
            let url;
            try {
                url = new URL($(this).val());
            } catch (err) {
                // The URL is invalid. Remove the body from the annoation.
                args.onRemoveBody(previousBody);
                return;
            }
            // Set the body to the annotation.
            const updatedBody = {
                type: 'SpecificResource',
                purpose: 'linking',
                source: url.href
            };
            if (previousBody) {
                args.onUpdateBody(previousBody, updatedBody);
            } else {
                args.onAppendBody(updatedBody);
            }
        });
        return widget[0];
    }
}
