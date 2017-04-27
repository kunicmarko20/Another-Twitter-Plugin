(function( $ ) {
	'use strict';

     $(window).load(function() {
		 $('.twitter-attributes button').click(function(){
			var value = '['+$(this).data('name')+']';
			var $textarea = jQuery("#dt_atp_style");
			var caret_position = $textarea[0].selectionStart;
			var textarea_value = $textarea.val();
			$textarea.val(textarea_value.substring(0, caret_position) + value + textarea_value.substring(caret_position) );
		 });
		 $('input[type=submit]').click(function(){
			 $('#loader').show();
		 });
     });



})( jQuery );
