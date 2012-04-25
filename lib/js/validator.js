/*******************************************************
Simple Form Validator
By Peter Paul Sevilla

HTML

	'fill' - field is required
		- 0 means no limit
	'number' - numeric field
	<input name="" class="_validate" filter="number[0,10]|fill[0]" />

JS
	var isValid = $('form').validateForm(); //returns boolean

	if(isValid) $('form').submit();

********************************************************/

(function($) {
	$.fn.validateForm = function(settings) 
	{
		var config = { 'border' : '2px solid #dc4e22', 'color' : '#dc4e22', 'border_default' : '1px solid silver' };		
		var msg = { 'err_invalid' : ' * invalid input', 'err_range_min' : ' * minimum of ', 'err_range_max' : ' * maximum of ', 'err_exceed' : ' * characters should not exceed ' };
        var errCount = 0;
		var err_ind;
		var isValid = true;
		var id = $(this).attr('id');

		if (settings) $.extend(config, settings);

		$('#' + id + ' [class^=_validate]:visible').each(function(k, i)
        {
			var input = $.trim($(this).val());
            var filter = $(this).attr('filter');
			var option = filter.split('|');
			var selector = this;

			$.each(option, function(key, value){

				var code = value.substr(0, 1);
				
				switch(code) 
				{
					case 'n':

						if(input.length > 0) {
							var isNum = (input - 0) == input && input.length > 0;
							if(!isNum) {
								$(selector).css('border', config.border);
								$(selector).next('.vld_err_num').remove();
								$(selector).after('<span class="vld_err_num">' + msg.err_invalid +'</span>');
								$('[class^=vld_err_num]').css('color', config.color);
								errCount++;
								if(errCount == 1) err_ind = k;
								return false;
							} 

							var num = parseInt(input);
							var bs_ind = value.indexOf('[');
							var rnge = value.slice(bs_ind+1, value.length-1);
							var arnge = rnge.split(',');
							var min = parseInt(arnge[0]);
							var max = parseInt(arnge[1]);

							if(input < min) {
								$(selector).css('border', config.border);
								$(selector).next('.vld_err_num').remove();
								$(selector).next('.vld_err_fill').remove();
								$(selector).after('<span class="vld_err_num">' + msg.err_range_min + ' ' + min + '</span>');
								$('[class^=vld_err_num]').css('color', config.color);
								errCount++;
								if(errCount == 1) err_ind = k;
								return false;
							} else if(input > max) {
								$(selector).css('border', config.border);
								$(selector).next('.vld_err_num').remove();
								$(selector).next('.vld_err_fill').remove();
								$(selector).after('<span class="vld_err_num">' + msg.err_range_max + ' ' + max + '</span>');
								$('[class^=vld_err_num]').css('color', config.color);
								errCount++;
								if(errCount == 1) err_ind = k;
								return false;
							} else {
								$(selector).css('border', config.border_default);
								$(selector).next('.vld_err_num').remove();
								$(selector).next('.vld_err_fill').remove();
							}
						}

						break;

					case 'f':
						var bs_ind = value.indexOf('[');
						var lim = parseInt(value.slice(bs_ind+1, value.length-1));					
						
						if(input.length == 0) {
							$(selector).css('border', config.border);
							$(selector).next('.vld_err_fill').remove();
							$(selector).next('.vld_err_num').remove();
							errCount++;
							if(errCount == 1) err_ind = k;
							return false;
						} else {
							$(selector).next('.vld_err_fill').remove();
							$(selector).next('.vld_err_num').remove();
							$(selector).css('border', config.border_default);
						}		

						if(lim != 0 && input.length > lim) {
							$(selector).css('border', config.border);
							$(selector).next('.vld_err_fill').remove();
							$(selector).next('.vld_err_num').remove();
							$(selector).after('<span class="vld_err_fill">' + msg.err_exceed + ' ' + lim + '</span>');
							$('[class^=vld_err_fill]').css('color', config.color);
							errCount++;
							if(errCount == 1) err_ind = k;
							return false;
						} 
						break;
					
					default:
						break;
				}

			});
			
		});

		if(errCount > 0) isValid = false;
		$('#' + id + ' [class^=_validate]:visible').eq(err_ind).focus();

		return isValid;
	};
 })(jQuery);