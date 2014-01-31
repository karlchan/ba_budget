$(document).ready(function() {
	function sel_chart_OnChange() {
		if ($("#sel_chart" ).val() == 5)  {
			$("#sel_programs" ).val('0');
			$("#sel_programType").val('0');
		}
		$("#sel_years").attr('size', ($("#sel_chart" ).val() == 5 || $("#sel_chart" ).val() == 1) ? '1' : '5');
		if ($("#sel_chart" ).val() == 5 || $("#sel_chart" ).val() == 1) {
			$("#sel_years").removeAttr('multiple');
		}
		else {
			$("#sel_years").attr('multiple', 'multiple');
		}
		
		$("#sel_programs").prop("disabled", ($( "#sel_chart" ).val() == 5));
		$("#sel_programType").prop("disabled", ($( "#sel_chart" ).val() == 5));
		
	};
	sel_chart_OnChange();
	$('#sel_chart').click( function() { sel_chart_OnChange(); } );	
});
