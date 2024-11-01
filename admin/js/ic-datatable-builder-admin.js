(function( $ ) {
	'use strict';

	//setup the admin tabs, very handy compared to different menus
	jQuery( "#icdb-tabs" ).tabs(); 
	
	//setup demo table if provided
	$("#pivot2_pivot").DataTable(
	{
		"stripeClasses": [ 'row1Color', 'row2Color' ]
	}
	);	
/*    $( "#icdb-columns-arranger1, #icdb-columns-arranger2" ).sortable({
      connectWith: ".connected_col_arranger"
    }).disableSelection();
	*/
	function icdb_build_shortcode()
	{
			var selectedCols = $( "#icdb-columns-arranger2" ).sortable( "toArray" );
			var tableID=$("#icdb_table_seq").val();
			if (tableID.length<1) tableID=101;
			var full_short_code="[crawlspider_table id="+tableID+" rows=10 cols="+selectedCols.join(",")+"]";
			$("#icdb_table_shortcode").val(full_short_code);
			console.log(selectedCols);
			console.log(full_short_code);			
	}
	
	function icdb_copy_shortcode()
	{
	    $("#icdb_table_shortcode").select();
		document.execCommand("copy");
		$("#icdb_shortcode_status").text("Shortcode copied to clipboard");
		$("#icdb_shortcode_status").show();
		$("#icdb_shortcode_status").fadeOut(6000);
	}
	
	//build the column dropper UI
    $( "#icdb-columns-arranger1, #icdb-columns-arranger2" ).sortable({
	connectWith: ".connected_col_arranger",
	stop: function( event, ui ) 
		{
			icdb_build_shortcode();
		}
	}).disableSelection();;
	
	$('#icdb_generate_shortcode').on
	(
		{
		 click: function(e) 
			{
				icdb_build_shortcode();
				icdb_copy_shortcode();
			}
		}	
	);

	$('#icdb_copy_shortcode').on
	(
		{
		 click: function(e) 
			{
				icdb_copy_shortcode();
			}
		}	
	);
	
})( jQuery );
