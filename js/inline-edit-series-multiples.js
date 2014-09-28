jQuery(document).ready(function($) {
	$("label.inline-edit-tags").remove(":contains('Series')");
	$('#the-list').on( 'click', 'a.editinline', function() {
		var id, type, editRowData, rowData, series_ids, series_parts, series_texts, part_id, write_part=null, count=0;
		var r_id = inlineEditPost.getId(this);
		type = $('table.widefat').hasClass('page') ? 'page' : 'post';

		rowData = $('#inline_series_'+r_id);
		id = $('.series_post_id', rowData).text();
		$('#hidden_series_id', 'div.inline_edit_series_old').remove();
		$('div.inline_edit_series_old').attr('class', 'inline_edit_series_');
		$('div.inline_edit_series_').attr('class','inline_edit_series_'+id);
		editRowData = $('.inline_edit_series_'+id);

		if ( type == 'post' ) {
			series_ids = $('.series_inline_edit', rowData).text();
			/*series_parts =  $('.series_inline_part', rowData).text().split(',');*/
			series_texts = $('.series_inline_name', rowData).text().split(',');

			$('ul.series-checklist :checkbox', editRowData).val(series_ids.split(','));
			$('input[class|="series_part"]').val('');

			$(series_ids.split(',')).each(function() {
				write_part = $('.series_inline_part_'+this, rowData).text();
				$('input[name|="series_part['+this+']"]', editRowData).val(write_part);
			});
			/*$('input.series_part', editRowData).each(function() {
				part_id = $(this).attr('id').replace('series_part_', '');
				write_part = $('.series_inline_part_'+part_id, rowData).text() )
					$(this).val(write_part);

				/*var index = $.inArray(part_id, series_ids.split(','));
				if (series_parts[index] != null) $(this).val(series_parts[index]);
			});*/
		}

		$('label.inline-edit-series', editRowData).before('<div id="hidden_series_id" class="hidden">'+series_ids+'</div>');
		$(editRowData).attr('class', 'inline_edit_series_old');
	});
});
