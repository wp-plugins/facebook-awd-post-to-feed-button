<?php
/*
Plugin Name: Facebook AWD Post To Feed
Plugin URI: http://facebook-awd.ahwebdev.fr/plugins/post-to-feed/
Description: This plugin will help you to create Share button and add them on your posts, page by widgets or shortcodes.
Version: 1.0
Author: AHWEBDEV
Author URI: http://facebook-awd.ahwebdev.fr
License: Copywrite AHWEBDEV
Text Domain: AWD_facebook_post_to_feed
Last modification: 18/03/2012
*/

/**
 *
 * @author alexhermann
 *
 */
add_action('plugins_loaded', 'initial_post_to_feed');
function initial_post_to_feed()
{
	global $AWD_facebook;
	if(is_object($AWD_facebook)){
		$model_path = $AWD_facebook->get_plugins_model_path();
		require_once($model_path);
		require_once(dirname(__FILE__).'/inc/classes/class.AWD_facebook_post_to_feed.php');
		$AWD_facebook_app_requests = new AWD_facebook_post_to_feed(__FILE__,$AWD_facebook,array('connect' => 1));
	}
}
?>