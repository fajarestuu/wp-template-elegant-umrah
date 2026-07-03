/**
 * Admin JavaScript
 * Handles media uploader for image fields in meta boxes
 *
 * @package ElegantUmrah
 */

(function ($) {
    'use strict';

    // Image field handler
    $(document).on('click', '.eu-select-image', function (e) {
        e.preventDefault();

        const $wrap     = $(this).closest('.eu-image-field-wrap');
        const $idInput  = $wrap.find('.eu-image-id');
        const $preview  = $wrap.find('.eu-image-preview');
        const $prevWrap = $wrap.find('.eu-image-preview-wrap');
        const $selectBtn = $wrap.find('.eu-select-image');

        const frame = wp.media({
            title:    'Select Image',
            button:   { text: 'Use this image' },
            multiple: false,
            library:  { type: 'image' },
        });

        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();

            $idInput.val(attachment.id);
            $preview.attr('src', attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);
            $prevWrap.show();
            $selectBtn.hide();
        });

        frame.open();
    });

    // Remove image
    $(document).on('click', '.eu-remove-image', function (e) {
        e.preventDefault();

        const $wrap     = $(this).closest('.eu-image-field-wrap');
        const $idInput  = $wrap.find('.eu-image-id');
        const $preview  = $wrap.find('.eu-image-preview');
        const $prevWrap = $wrap.find('.eu-image-preview-wrap');
        const $selectBtn = $wrap.find('.eu-select-image');

        $idInput.val('');
        $preview.attr('src', '');
        $prevWrap.hide();
        $selectBtn.show();
    });

    // Color field preview
    $(document).on('input', 'input[type="color"]', function () {
        const $this = $(this);
        const fieldName = $this.attr('name');
        const $preview = $('[data-color-preview="' + fieldName + '"]');
        if ($preview.length) {
            $preview.css('background-color', $this.val());
        }
    });

})(jQuery);
