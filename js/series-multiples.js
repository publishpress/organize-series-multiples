jQuery( function($) {
	
	$('#jaxseries').prepend('<span id="series-multiples-add"><input type="text" name="newseries" id="newseries" size="16" autocomplete="off"/><input type="button" name="Button" class="add:serieschecklist:jaxseries" id="seriesadd" value="' + seriesL10n.add + '" /><input type="hidden"/><input type="hidden"/><span id="howto">' + seriesL10n.how + '</span></span><span id="series-ajax-response"></span><span id="add-series-nonce" class="hidden">' + seriesL10n.addnonce + '</span>')
	
	$('#seriesadd').click( function() {
		$('input','#serieschecklist').removeProp('checked');
		/* console.log($('#newseries').val()); /**/
		
		var data = {
			action: 'add_series',
			newseries: $('#newseries').val(),
			addnonce: $('#add-series-nonce').text()
		}
		$.post(ajaxurl, data, function(response) {
			console.log(response); /**/
			var resp = $.parseJSON(response);
			console.log(resp); /**/
			if ( !resp.error ) {
				$('#newseries').val('');
				$('ul#serieschecklist li:first').after(resp.html);
				$('#series-'+resp.id).animate({backgroundColor: "transparent"}, 3000);
				$('#add-series-nonce').text(resp.new_nonce);
			} else {
				$('#series-ajax-response').html(resp.error);
			}
		});
	});
});