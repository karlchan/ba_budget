//(function($) {
	$(document).ready(function() {
		function sel_chart_OnChange() {
			if ($("#sel_chart" ).val() == 5)  {
				$("#sel_programs" ).val('0');
				$("#sel_programType").val('0');
			}
			$("#sel_programs").prop("disabled", ($( "#sel_chart" ).val() == 5));
			$("#sel_programType").prop("disabled", ($( "#sel_chart" ).val() == 5));
		};
		sel_chart_OnChange();
		$('#sel_chart').click( function(){ sel_chart_OnChange() } );
	});
//})(jQuery);
