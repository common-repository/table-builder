<?php

/**
 * Fired during plugin deactivation
 *
 *
 * @package    Ic_Datatable_Builder
 * @subpackage Ic_Datatable_Builder/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ic_Datatable_Builder
 * @subpackage Ic_Datatable_Builder/includes
 */
class Ic_Datatable_Builder_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() 
	{
	
		delete_option( 'icdb_table_shortcode' );
		delete_option( 'icdb_header_style' );
		delete_option( 'icdb_content_length' );
		delete_option( 'icdb_excerpt_length' );
		delete_option( 'icdb_column_property' );
		delete_option( 'icdb_table_seq' );


	}

}

?>