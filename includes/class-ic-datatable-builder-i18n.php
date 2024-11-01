<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.infocaptor.com
 * @since      1.0.0
 *
 * @package    Ic_Datatable_Builder
 * @subpackage Ic_Datatable_Builder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ic_Datatable_Builder
 * @subpackage Ic_Datatable_Builder/includes
 * @author     Nilesh Jethwa <contact@infocaptor.com>
 */
class Ic_Datatable_Builder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ic-datatable-builder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
