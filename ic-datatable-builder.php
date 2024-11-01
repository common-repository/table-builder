<?php

namespace icdatatable;

/**
 *
 * @link              https://www.crawlspider.com
 * @since             1.0.0
  *
 * @wordpress-plugin
 * Plugin Name:       Table Builder
 * Plugin URI:        https://www.crawlspider.com/table-builder-wordpress/
 * Description:       It allows you present a list of posts as table on any page or post. Provides quick way to pick the columns you wish to display and generates a shortcode 
 * Version:           1.0.0
 * Author:            Crawlspider
 * Author URI:        https://www.crawlspider.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       table-builder
 * Domain Path:       /languages
 */

//AUM Ganapataye Namah
// If this file is called directly, abort.
//CrawlSpider Table Builder – DataTables Plugin for WordPress
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ICDB_IC_DATATABLE_BUILDER_VERSION', '1.0.0' );
defined(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS') or define(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS','icdb-table-builder-options-'.__NAMESPACE__); 
defined(__NAMESPACE__.'ICDB_PLUGIN_NAME') or define(__NAMESPACE__.'ICDB_PLUGIN_NAME','CrawlSpider Table Builder – WP DataTables Plugin'); 

$icdb_phpglb=array();


// 1.1
// hint: registers all our custom shortcodes on init
add_action('init', __NAMESPACE__.'\icdb_register_shortcodes');



/*
// hint: register ajax actions
add_action('wp_ajax_nopriv_icdb_save_subscription', 'icdb_save_subscription'); // regular website visitor
add_action('wp_ajax_icdb_save_subscription', 'icdb_save_subscription'); // admin user
*/

// 1.5
// load external files to public website
add_action('wp_enqueue_scripts', __NAMESPACE__.'\icdb_public_scripts');

//**admin scripts are loaded only if the options page of this plugin is loaded
// 1.7 
// hint: register our custom menus
add_action('admin_menu', __NAMESPACE__.'\icdb_set_config_page'); //<-- This also loads admin_enqueue_scripts


// 1.9
// register plugin options
add_action('admin_init', __NAMESPACE__.'\icdb_register_options');


register_activation_hook( __FILE__, __NAMESPACE__.'\icdb_activate' );
register_deactivation_hook( __FILE__, __NAMESPACE__.'\icdb_deactivate' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ic-datatable-builder-activator.php
 */
function icdb_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ic-datatable-builder-activator.php';
	\Ic_Datatable_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ic-datatable-builder-deactivator.php
 */
function icdb_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ic-datatable-builder-deactivator.php';
	\Ic_Datatable_Builder_Deactivator::deactivate();
}





function icdb_set_config_page()
{


		global $icdb_ns_options;
		//add_submenu_page( string $parent_slug, string $page_title                           , string $menu_title                                 , string $capability   , string $menu_slug                             , callable $function = '' )
		//add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
		$ic_admin_page = add_options_page( 'Table Builder by CrawlSpider', 'Table Builder - CrawlSpider', 'manage_options', 'icdb-datatable-builder', __NAMESPACE__.'\icdb_show_plugin_options' );
	    
		
		// Load the JS conditionally only for the  admin page of CrawlSpider
        add_action( 'load-' . $ic_admin_page, __NAMESPACE__.'\icdb_load_onlyadmin_js' );		
		//add_action('admin_enqueue_scripts', __NAMESPACE__.'\icdb_woo_admin_scripts');
	
}

//load this only if icdash admin page is invoked
function icdb_load_onlyadmin_js()
{
	add_action('admin_enqueue_scripts', __NAMESPACE__.'\icdb_load_admin_js_scripts');
}


function icdb_show_plugin_options()
{

	global $icdb_phpglb;

	//check if admin user
	$icdb_phpglb['my_file_ver']  = date("ymd-Gis", filemtime(  __FILE__ )  );
	$icdb_phpglb['plugin_name']=constant(__NAMESPACE__.'ICDB_PLUGIN_NAME');  

	$current_user = wp_get_current_user();
	 if (!in_array('administrator',  $current_user->roles))
	 {
		$icdb_phpglb["is_admin_role"]="N";
	 }
	 else $icdb_phpglb["is_admin_role"]="Y";

	
	$current_version=ICDB_IC_DATATABLE_BUILDER_VERSION;
	//check if all installation went fine
	//collect some basic paths and urls
	$plugin_dir_path=plugin_dir_path( __FILE__ );
	$base_file= __FILE__;
	$plugin_dir=__DIR__;
	$dir_base=basename($plugin_dir);
	$plugin_base_url=plugins_url()."/".$dir_base;
    $button_style="font-size:28px;height:56px;";
	$content_dir_path = wp_normalize_path(WP_CONTENT_DIR);
	
	

	$debug_html=" ";
	$register_html=" ";
	
	$info_html=<<<EOD
		<ul>
		<li><a href="https://www.crawlspider.com?x=wpdatatables" target="_blank">CrawlSpider Website</a>
		<li><a href="https://www.crawlspider.com?x=wpdatatables" target="_blank">Plugin Page</a>
		</ul>
EOD;

$info_tab=<<<EOD
<div id="info-tab">
   {$info_html}
   {$register_html}
	
</div><!--id="info-tab"-->

EOD;
	
/*global $submenu;
$menu_list=print_r($submenu, true); 	*/
$config_html=<<<EOD
<div id="icdb-main" class="wrap">
<script>
icdb_global={};


</script>
	<h2>{$icdb_phpglb['plugin_name']} - v{$current_version}</h2>
	
</div><!--id="icdb-main"-->
	
<br class="clear" />


<div id="icdb-tabs">
  <ul>
    
    <li><a href="#setup-tab">Data/Setup</a></li>
    <li><a href="#info-tab">Info</a></li>
  </ul>
  
  
<br class="clear" />
EOD;

    echo $config_html;	
	
	
	icdb_options_admin_page(); //echo the admin settings
	echo $info_tab;	
	echo "</div> <!--icdb-tabs-->"; //end all the tabs wrapper
}




function icdb_load_admin_js_scripts() 
{
	$dir_base=basename(__DIR__);
	$plugin_base_url=plugins_url()."/".$dir_base;
  
	wp_enqueue_style('jquery-ui-css-style',$plugin_base_url. '/admin/css/jquery-ui.min.css', array(), false, 'screen');	

	wp_enqueue_style( 'jquery-ui-css-style' );	
	wp_enqueue_style('icdb-admin-ui', $plugin_base_url. '/admin/css/ic-datatable-builder-admin.css?v='.ICDB_IC_DATATABLE_BUILDER_VERSION, array(), false, 'screen');	

	wp_enqueue_script('jquery-ui-datepicker', '', array('jquery-ui-core', 'jquery'));
	wp_enqueue_script('jquery-ui-tabs', '', array('jquery-ui-core', 'jquery'));
	wp_enqueue_script('jquery-ui-dialog', '', array('jquery-ui-core', 'jquery'));
	wp_enqueue_script('jquery-ui-button', '', array('jquery-ui-core', 'jquery'));
	wp_enqueue_script('jquery-ui-sortable', '', array('jquery-ui-core', 'jquery'));


	wp_enqueue_style('icdb_infocaptor_datatables_css', plugins_url('/admin/js/datatables/datatables.min.css?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array(), false, 'screen');	
	wp_register_script('icdb_infocaptor_datatables_js', plugins_url('/admin/js/datatables/datatables.min.js?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array('jquery'),'',true);
	wp_enqueue_script('icdb_infocaptor_datatables_js');
	wp_register_script('icdb_table_builder_ajax', plugins_url('/admin/js/ic-datatable-builder-admin.js?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array('jquery'),'',true);
	wp_enqueue_script('icdb_table_builder_ajax');
	
}

function icdb_get_default_columns()
{
	//list all the columns the user has option to select from. This is what will be shown in the draggable selection
	$default_columns=array(
		'ID' => array ("title" =>"ID"),
		'post_author' => array ("title" =>"Author"),
		'post_date' => array ("title" =>"Date"),
		'post_title' => array ("title" =>"Topic"),
		'post_category' => array ("title" =>"Category"),
		'post_tag' => array ("title" =>"Tag"),
		'post_content' => array ("title" =>"Content"),
		'post_excerpt' => array ("title" =>"Summary"),
		'post_modified' => array ("title" =>"Modified"),
		'comment_count' => array ("title" =>"Comment #")
	);

	return $default_columns;

}

function icdb_get_initial_column_set()
{
	$init_col_set=array("ID","post_title","post_date","post_author","post_category");
	return $init_col_set;
}

function icdb_get_initial_columns()
{
	$available_columns=icdb_get_default_columns();
	$initial_set=icdb_get_initial_column_set();
	//select a list of columns to show as default selection to the user
	for ($ic=0;$ic<count($initial_set); $ic++)
	{
		$initial_columns[$initial_set[$ic]]=$available_columns[$initial_set[$ic]];	
	}
	return $initial_columns;
}

function icdb_get_default_column_property()
{

	
	return json_encode(icdb_get_default_columns(), JSON_PRETTY_PRINT);

}

// 6.9
// hint: returns the requested page option value or it's default
function icdb_get_option( $option_name ) 
{
	
	// setup return variable
	$option_value = '';	
	
	
	try 
	{
		
		// get default option values
		// get the requested option
		switch( $option_name ) 
		{
			
			case 'icdb_table_shortcode':
				//$option_value = (get_option('icdb_etl_start_date')) ? get_option('icdb_etl_start_date') : date('Y-m-d', strtotime('-2 year'));
				$option_value = get_option('icdb_table_shortcode');
				if (!$option_value)
				{
					$option_value="[crawlspider_table id=101 cols=ID,post_author,post_date,post_title,post_category]";
					update_option('icdb_table_shortcode', $option_value);
				}
				break;
			case 'icdb_column_property':			
				
				$option_value = get_option('icdb_column_property');
				icdb_write_debuglog(__FUNCTION__." get_option('icdb_column_property') = ".$option_value);
				
				if (!$option_value)
				{
					$option_value= icdb_get_default_column_property();
					icdb_write_debuglog(__FUNCTION__." ".__LINE__." Failed get_option('icdb_column_property') = ".$option_value);
					update_option('icdb_column_property', $option_value);
				}	
							
			break;
			case 'icdb_header_style':			
			
				$option_value = get_option('icdb_header_style');
				if (!$option_value)
				{
					$option_value='background-color:white;color:black;';
					update_option('icdb_header_style', $option_value);
				}				
			break;
			case 'icdb_content_length':			
		
				$option_value = get_option('icdb_content_length');
				if (!$option_value)
				{
					$option_value=30;
					update_option('icdb_content_length', $option_value);
				}				
			break;
			case 'icdb_excerpt_length':			
	
				$option_value = get_option('icdb_excerpt_length');
				if (!$option_value)
				{
					$option_value=30;
					update_option('icdb_excerpt_length', $option_value);
				}				
			break;																
			case 'icdb_table_seq':			
	
				$option_value = get_option('icdb_table_seq');
				if (!$option_value)
				{
					$option_value=101;
					update_option('icdb_table_seq', $option_value);
				}				
			break;				

			
		}
		
	} catch( Exception $e) {
		
		// php error
		
	}
	
	// return option value or it's default
	return $option_value;
	
}


// hint: get's the current options and returns values in associative array
function icdb_get_current_options() {
	
	// setup our return variable
	$current_options = array();
	
	try {
	
		// build our current options associative array
		$current_options = array(
			'icdb_table_shortcode'  => esc_attr(icdb_get_option('icdb_table_shortcode')),
			'icdb_header_style' 	=> esc_attr(icdb_get_option('icdb_header_style')),
			'icdb_content_length' 	=> icdb_get_option('icdb_content_length'),
			'icdb_excerpt_length' 	=> icdb_get_option('icdb_excerpt_length'),
			'icdb_column_property' 	=> icdb_get_option('icdb_column_property'),
			'icdb_table_seq' 		=> icdb_get_option('icdb_table_seq')
			
		);
	
	} catch( Exception $e ) {
		
		// php error
	
	}
	
	// return current options
	return $current_options;
	
}


// hint: registers all our plugin options
function icdb_register_options() 
{
	// plugin options: The group name should be matching the menu slug 
	register_setting(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'), 'icdb_table_shortcode');
	register_setting(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'), 'icdb_header_style');
	register_setting(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'), 'icdb_content_length');
	register_setting(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'), 'icdb_excerpt_length');
	register_setting(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'), 'icdb_column_property');
	register_setting(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'), 'icdb_table_seq');	
	

}

function icdb_get_column_property_array()
{
	try
	{
		icdb_write_debuglog(__FUNCTION__." TRY section ");
		$col_property=icdb_get_option('icdb_column_property');
		icdb_write_debuglog(__FUNCTION__." TRY section value =".$col_property);
	}
	catch (Exception $e) //JSON messed up so switch to default values
	{
		icdb_write_debuglog(__FUNCTION__." exception = ");
		$col_property=icdb_get_default_column_property(); //
		icdb_write_debuglog(__FUNCTION__." exception  value =".$col_property);
	}

	return json_decode($col_property,true);

}


function icdb_get_col_selector_ui()
{
	$available_cols=icdb_get_default_columns();
	$initial_cols = icdb_get_initial_columns();
	$available_html="";
	$initial_html="";
	
	foreach ($available_cols  as $col_id => $col_val)
	{
		if (isset($initial_cols[$col_id])) //if the column is part of initial set then take it out from the available columns
		{
			$initial_html.=<<<EOD
			<div id="{$col_id}" class="ui-widget-header ui-corner-all icdb-column-div">$col_id</div>
EOD;
			
		}
		else
		{
			$available_html.=<<<EOD
			<div id="{$col_id}" class="ui-widget-header ui-corner-all icdb-column-div">$col_id</div>
EOD;
		}	
	}

	$col_selector_html=<<<EOD
	<div id="icdb-column-sel-ui-parent">
		
		<p><strong>Available Columns</strong> ( Drag to the display columns section )</p>
		<div id="icdb-columns-arranger1" class="icdb-column-parent connected_col_arranger">
		{$available_html}
		</div>	
		<p><strong>Display Columns</strong></p>	
		<div id="icdb-columns-arranger2" class="icdb-column-parent connected_col_arranger">
		{$initial_html}
		</div>
	</div>

EOD;

	return $col_selector_html;
}

function icdb_write_debuglog($message)
{
	if (true === WP_DEBUG) 
	{
		error_log($message);
	}
}

// hint: plugin options admin page
function icdb_options_admin_page() 
{
	global $icdb_phpglb;
	
	// get the default values for our options
	$options = icdb_get_current_options();
	
	//NOTE add restriction for non admin roles $icdb_phpglb["is_admin_role"]="Y";
	
	//NOTE: Make sure any new options are added in the activator with default values
	echo('<div id="setup-tab"> <!--SETUP TAB BEGIN-->');
	echo icdb_get_col_selector_ui();	
	
	echo('<form action="options.php" id="icdb_settings_form" method="post">');
		
		
			icdb_write_debuglog("option value =".constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'));
			
			// outputs a unique nounce for our plugin options
			settings_fields(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'));
			// generates a unique hidden field with our form handling url
			//@do_settings_fields(constant(__NAMESPACE__.'ICDB_PLUGIN_OPTIONS'));
			
			echo('<table class="form-table">
			
				<tbody>

					<tr>
						<th scope="row"><label for="icdb_table_shortcode" class="icdb-input-label">Short Code</label></th>
						<td>
						<input id="icdb_table_shortcode" name="icdb_table_shortcode" class="icdb-input-field" value="'.$options['icdb_table_shortcode'].'">
						<button type="button" id="icdb_generate_shortcode" class="ui-button ui-widget ui-corner-all icdb-button">Generate Short Code</button>
						<button type="button" id="icdb_copy_shortcode" class="ui-button ui-widget ui-corner-all icdb-button">Copy Short Code</button>
						<span id="icdb_shortcode_status" style="font-size:90%;font-style:italic;color:steelblue;"> </span>
					

						</td>
					</tr>
					<tr>
						<th scope="row"><label for="icdb_column_property" class="icdb-input-label">Column Properties </label></th>
						<td>
						<p>These are property values for each column. </p>
							<textarea  id="icdb_column_property" name="icdb_column_property" rows="5" style="width:100%;font-family:Courier New;">'.($options['icdb_column_property']).'</textarea>

						</td>
					</tr>
					<tr>
						<th scope="row"><label for="icdb_header_style" class="icdb-input-label">Table Header Style </label></th>
						<td>
							<input  id="icdb_header_style" name="icdb_header_style" class="icdb-input-field" value="'.htmlspecialchars($options['icdb_header_style']).'">

						</td>
					</tr>					
					<tr>
						<th scope="row"><label for="icdb_table_seq" class="icdb-input-label">Table Sequence</label></th>
						<td>
							<input  type="number" id="icdb_table_seq" name="icdb_table_seq"  value="'.$options['icdb_table_seq'].'">
							<span id="icdb_table_seq_status" style="font-size:90%;font-style:italic;color:steelblue;">Increment the sequence if you use more than one shortcode on a single page or post</span>

						</td>
					</tr>					
					<tr>
						<th scope="row"><label for="icdb_content_length" class="icdb-input-label">Content Length (in words)</label></th>
						<td>
							<input  type="number" id="icdb_content_length" name="icdb_content_length"  value="'.$options['icdb_content_length'].'">

						</td>
					</tr>
					<tr>
						<th scope="row"><label for="icdb_excerpt_length" class="icdb-input-label">Excerpt Length (in words)</label></th>
						<td>
							<input  type="number" id="icdb_excerpt_length" name="icdb_excerpt_length"  value="'.$options['icdb_excerpt_length'].'">

						</td>
					</tr>								

			
				</tbody>
				
			  </table>

			');
		
			// outputs the WP submit button html
			@submit_button(); 
		
	   echo('	  
	   </form>
	   <div id="icdb-sample-table-parent">
	   </div>
	</div><!--id="setup-tab"-->
	');
	
}



function icdb_public_scripts()
{
//wp_enqueue_style( 'jquery-ui-css-style' );	
//	wp_enqueue_style('icdb-admin-ui', plugins_url('/admin/css/ic-datatable-builder-admin.css?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array(), false, 'screen');	

wp_enqueue_style('icdb_infocaptor_css_public', plugins_url('/admin/css/ic-datatable-builder-public.css?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array(), false, 'screen');	
wp_enqueue_style('icdb_infocaptor_datatables_css_public', plugins_url('/admin/js/datatables/datatables.min.css?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array(), false, 'screen');	
wp_register_script('icdb_infocaptor_datatables_js_public', plugins_url('/admin/js/datatables/datatables.min.js?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array('jquery'),'',true);
wp_enqueue_script('icdb_infocaptor_datatables_js_public');
wp_register_script('icdb_table_builder_public', plugins_url('/admin/js/ic-datatable-builder-public.js?v='.ICDB_IC_DATATABLE_BUILDER_VERSION,__FILE__), array('jquery'),'',true);
wp_enqueue_script('icdb_table_builder_public');

}


function icdb_register_shortcodes() 
{
	
	add_shortcode('crawlspider_table', __NAMESPACE__.'\icdb_crawlspider_table_handler');
	
}

function icdb_get_post_details()
{
	$args = array(
		'numberposts' => 10,
		'post_type'   => 'post'
	  );
	   
	  $post_list = get_posts( $args );
	  return $post_list;
}

function icdb_get_post_details_sql($table_args)
{
	global $wpdb;

	$result = $wpdb->get_results (
        "
        SELECT * 
        FROM  {$wpdb->posts}
		WHERE post_type =  'post'
		and post_status='publish'
		order by post_modified desc
		limit {$table_args['row_count']}
        "
		);
	return $result;	
}

function icdb_get_cell_value($post_record,$display_column)
{
	setup_postdata( $post_record );
	//$raw_cell_value=$post_record->$display_column;

	if ($display_column=="post_category")
	{

		$display_cell_value=get_the_category_list( ', ', '', $post_record->ID  );//,get_the_category( $raw_cell_value );
		
	}
	else if ($display_column=="post_tag")
	{

		$display_cell_value=get_the_tag_list( '', ' , ','', $post_record->ID  );//,get_the_category( $raw_cell_value );

	}	
	else if ($display_column=="post_author")
	{

		$author_url=esc_url( get_author_posts_url( $post_record->post_author ) );
		$author_display_name=get_the_author();
		$author_link_hover="Articles by {$author_display_name}";
		$display_cell_value="<a href='{$author_url}' title='{$author_link_hover}' rel='author'>{$author_display_name}</a>";

	}
	else if ($display_column=="post_title")
	{

		$post_title=get_the_title( $post_record );
		$post_link=get_permalink( $post_record );
		$display_cell_value = "<a href='{$post_link}'>{$post_title}</a>";

	}
	else if ($display_column=="post_date")
	{

		$post_date=get_the_date( 'Y-m-d' );
		$display_cell_value=$post_date;

	}
	else if ($display_column=="add_to_cart")
	{

		$home_url=get_home_url();
		$display_cell_value="<a href='{$home_url}/?add-to-cart={$post_record->ID}'>Add to Cart</a>";

	}	
	
	else if ($display_column=="post_content")
	{
		$post_content=$post_record->post_content;
		$content_length=icdb_get_option( 'icdb_content_length' ) ;
		$display_cell_value=wp_trim_words( $post_content, $content_length, '...' );
	}
	else if ($display_column=="post_excerpt")
	{
		$post_content=$post_record->post_excerpt;
		$content_length=icdb_get_option( 'icdb_excerpt_length' ) ;
		$display_cell_value=wp_trim_words( $post_content, $content_length, '...' );
	}			
	else
	{

		$display_cell_value=$post_record->$display_column;

	}

	return $display_cell_value;

}

function icdb_get_table_col_width()
{
	$table_column_width=<<<EOD
	<colgroup>
	  <col  style="width: 5%;">
	  <col  style="width: 5%;">
	  <col  style="width: 10%;">
	  <col  style="width: 50%;">
	  <col  style="width: 20%;">
	  <col  style="width: 10%;">
	 </colgroup>
EOD;
	return $table_column_width;
}

function icdb_get_table_header($table_args)
{
	
  $column_property=icdb_get_column_property_array();
  icdb_write_debuglog(__FUNCTION__." column properties = ".print_r($column_property,true));
  $table_header_style=icdb_get_option('icdb_header_style');
  $table_header="	 <thead style='{$table_header_style}'>";
  
  $table_header."<tr>";

  for ($dc=0; $dc < count($table_args['display_columns']) ; $dc++)
  {
	  $display_column_name=$table_args['display_columns'][$dc]; //get the column name

	  //now use the column name to find the display title from json property
	  $display_title=$column_property[$display_column_name]["title"]; //grab the title index from the multi array 
	  $table_header.="<td>".$display_title."</td>";//<tr><td>ID</td> <td>Author</td> <td>Date</td> <td>Title</td> <td>Category</td> <td>Tags</td></tr>
  }	  
  $table_header."</tr>";
  
  $table_header.="</thead>";
  return $table_header;
}

function icdb_build_table($table_args)
{

	$table_id=$table_args['table_elem_id'];
	$display_columns=$table_args['display_columns'];
	$display_row_count=$table_args['display_columns'];


	  $post_list =  icdb_get_post_details_sql($table_args);//icdb_get_post_details_sql();
	  $table_row=" ";
	  $table_header=icdb_get_table_header($table_args);
	/*  "	 <thead style='background-color:steelblue;color:white;'>
		  		<tr><td>ID</td> <td>Author</td> <td>Date</td> <td>Title</td> <td>Category</td> <td>Tags</td></tr>
  		</thead>
	  ";
  */
	  $table_body="<tbody>";
	  $row_class=array('odd','even');
	  for ($p=0; $p<count($post_list); $p++)
	  {

		  $row_stripe_class=$row_class[$p % 2];
		  
		  $table_row.="<tr>";
		  for ($dc=0;$dc<count($display_columns); $dc++)
		  {
			  $table_cell_value=icdb_get_cell_value($post_list[$p],$display_columns[$dc]);
			$table_row.="<td>{$table_cell_value}</td>";
		  }	  
		  $table_row.="</tr>"; 
		  $table_body.=$table_row;
		  
		  $table_row=" ";
	  }
	  $table_body.="</tbody>";
	  wp_reset_postdata();
	  $table_html="<table id='{$table_id}' id_name='{$table_id}' cellpadding='5' cellspacing='0' style='font-size:90%;' class='ic_pivot crawlspider_datatable display' >"
	  			//.icdb_get_table_col_width()
	  			.$table_header.$table_body."</table>";
	  return $table_html;
}

function icdb_get_table_style($table_id)
{
	$table_style=<<<EOD
		<style>
			#{$table_id} {
				font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
				border-collapse: collapse;
				width: 100%;
			}
			
			#{$table_id} td, #customers th {
				border: 1px solid #ddd;
				padding: 8px;
			}
			
			#{$table_id} tr:nth-child(even){background-color: yellow;}
			
			#{$table_id} tr:hover {background-color: #ddd;}
			
			#{$table_id} th {
				padding-top: 12px;
				padding-bottom: 12px;
				text-align: left;
				background-color: #4CAF50;
				color: white;
			}
			.row1Color {background-color:green;}
			.row2Color {background-color:yellow;}
		  </style>
EOD;
return $table_style;
}

function icdb_crawlspider_table_handler($args, $content="")
{
	if( isset($args['id']) ) $shortcode_id = $args['id'];
	if( isset($args['cols']) ) $column_str = $args['cols'];
	if( isset($args['rows']) ) $row_count = intval($args['rows']);
	if ($row_count<=0) $row_count=10;

	$column_arry=explode(",",$column_str);
	$table_args["shortcode_id"]=$shortcode_id;
	$table_args["display_columns"]=$column_arry;
	$table_elem_id="ic-datatable-{$shortcode_id}";
	$table_args["table_elem_id"]=$table_elem_id;
	$table_args["row_count"]=$row_count;

$table_html=icdb_build_table($table_args);
  return $table_html;
}

?>