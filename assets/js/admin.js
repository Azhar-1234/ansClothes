/**
 * Media picker for the Collection card image.
 */
(function ($) {
	'use strict';

	var frame = null;

	$(document).on('click', '.ans-media-pick', function (e) {
		e.preventDefault();

		var target = $(this).data('target');
		var $input = $('#' + target);
		var $preview = $('[data-preview-for="' + target + '"]');

		frame = wp.media({
			title: 'Select card image',
			button: { text: 'Use this image' },
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

			$input.val(attachment.id);
			$preview.html('<img src="' + url + '" style="max-width:180px;height:auto;margin-top:10px;">');
		});

		frame.open();
	});

	$(document).on('click', '.ans-media-clear', function (e) {
		e.preventDefault();

		var target = $(this).data('target');

		$('#' + target).val('');
		$('[data-preview-for="' + target + '"]').empty();
	});

	// Clear the fields after a term is added via AJAX.
	$(document).on('ajaxComplete', function (event, xhr, settings) {
		if (settings.data && settings.data.indexOf('action=add-tag') !== -1) {
			$('#ans_term_image').val('');
			$('#ans_count_label').val('');
			$('[data-preview-for="ans_term_image"]').empty();
		}
	});

	/**
	 * Size chart measurement rows: add / remove.
	 */
	$(document).on('click', '.ans-size-chart-add-row', function (e) {
		e.preventDefault();

		var template = document.getElementById('ans-size-chart-row-template');
		if (!template) { return; }

		$(this).closest('p').prev('table').find('tbody')
			.append($(template.content.firstElementChild).clone());
	});

	$(document).on('click', '.ans-size-chart-remove-row', function (e) {
		e.preventDefault();

		var $rows = $(this).closest('table').find('tbody tr');
		if ($rows.length <= 1) { return; }

		$(this).closest('tr').remove();
	});
})(jQuery);
