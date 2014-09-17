<?php
/*
Plugin Name: Dashboard Instant Finder
Plugin URI: http://thewpchef.com/dasboard-instant-finder
Description: Dashboard widget to find posts and pages by their titles instantly. Start typing and you'll get the suggestions right away.
Version: 1.0
Author: WP Chef
Author URI: http://thewpchef.com
*/


/**
 * Register widget
 * @return void
 */
function daif_add_dashboard_widget() {
	wp_add_dashboard_widget( 'dif_widget', 'Instant Finder', 'daif_dashboard_widget_function' );
}
add_action( 'wp_dashboard_setup', 'daif_add_dashboard_widget' );


/**
 * load JS and the data (admin dashboard only)
 * @param  string $hook current page
 * @return void
 */
function daif_enqueue_scripts( $hook ) {
	global $wpdb;

	// Only on dashboard, only for users having most of capabilities
	if( 'index.php' != $hook )  return;

	$user_ID = get_current_user_id();
	if ( current_user_can( 'edit_others_pages' ) ) {
		$query = 'SELECT ID AS value, post_title AS label FROM ' . $wpdb->posts . 
		        ' WHERE post_status IN ( "publish", "draft" )';
	} elseif ( current_user_can( 'edit_pages' ) ) {
		$query = 'SELECT ID AS value, post_title AS label FROM ' . $wpdb->posts . 
		        ' WHERE post_author = ' . $user_ID . ' post_status IN ( "publish", "draft" )';
	}

	wp_enqueue_script( 'daif', plugin_dir_url( __FILE__ ) . 'assets/js/widget.js', 
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete' ) );

	$posts = $wpdb->get_results( $query, ARRAY_A );
	if( $posts ) { 
		wp_localize_script( 'daif', 'daif_data', $posts );
	} else { 
		wp_localize_script( 'daif', 'daif_data', null );
	}
	wp_localize_script( 'daif', 'daif_consts', array(
			'admin_url'         => admin_url(),
			'home_url'          => home_url( '/' ),
			'str_nothing_found' => __( 'Nothing found', 'daif' )
		) 
	);
}
add_action( 'admin_enqueue_scripts', 'daif_enqueue_scripts' );



/**
 * Display widget
 * @return void
 */
function daif_dashboard_widget_function() { 
?>
	<p>
		<input type="text" style="width:100%;" placeholder="<?php _e( 'To find a page or post, start typing here', 'daif' ); ?>" id="daif_tags" />
	</p>
	<ul id="daif_results"></ul>
<?php
}
