<script type="text/javascript">
$(document).ready(function () {
	function sel_chart_OnChange() {
		if ($("#sel_chart" ).val() == 5)  {
			$("#sel_programs" ).val('0');
			$("#sel_programType").val('0');			
			$("#divExport").show();
		} else {
			$("#divExport").hide();
		}
		
		if ($("#sel_chart" ).val() == 5 || $("#sel_chart" ).val() == 1) {
			$("#sel_years").removeAttr('multiple');
			$("#sel_years").attr('size', '1');
		}
		else {
			$("#sel_years").attr('multiple', 'multiple');
			$("#sel_years").attr('size', '5');
		}
		
		$("#sel_programs").prop("disabled", ($( "#sel_chart" ).val() == 5));
		$("#sel_programType").prop("disabled", ($( "#sel_chart" ).val() == 5));		
	};
	sel_chart_OnChange();
	$('#sel_chart').change( function() { sel_chart_OnChange(); } );
});
</script>