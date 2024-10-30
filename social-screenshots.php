<?php

/**

 * @link              https://wordpress.org/plugins/haremo-social-screenshots/
 * @since             0.1
 * @package           Social_Screenshots
 *
 * @wordpress-plugin
 * Plugin Name:       HAREMO Social Screenshots
 * Plugin URI:        https://wordpress.org/plugins/haremo-social-screenshots/
 * Description:       Create beautiful Facebook Share Images! We create screenshots of your Articles and Pages and use these as Facebook's Share Image
 * Version:           1.0.2
 * Author:            Hamid Reza Monadjem
 * Author URI:        https://haremo.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       haremo-social-screenshots
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

function social_screenshots_cron() {
		$args = array(
		  'numberposts' => 10,
		  'post_type'   => 'any'
		);
		$page_ids = get_posts( $args );
    foreach ($page_ids as $page) {
				$imageDataRaw = get_post_meta($page->ID, 'social-screenshot', true);
				$isDisabled = get_post_meta($post->ID, 'social-screenshots-disable', true);
				if(empty($isDisabled)){
				  $imageData = json_decode($imageDataRaw, true);
					if(!$imageData['url']){
		        $url = get_permalink($page);
						if($url){
						$response = wp_remote_post( 'https://api.social-screenshots.haremo.de/v0.1/request', array(
								'method' => 'POST',
								'timeout' => 30,
								'sslverify'=>false,
								'body' => json_encode(array("url"=> $url))
						));

							update_post_meta($page, 'social-screenshot-debug', json_encode($response));
							if ( is_wp_error( $response ) ) {
						    update_post_meta($page, 'social-screenshot', json_encode(array("time" => time())));
							} else {
						  	$result = json_decode($response['body']);
							  if($result->url){
							    update_post_meta($page, 'social-screenshot', json_encode(array("time" => time(), "url"=> $result->url)));
							  } else {
							    update_post_meta($page->ID, 'social-screenshot', json_encode(array("time" => time())));
							  }
							}
						}
				}
			}
    }
}

function social_screenshots_activate() {
		wp_schedule_event(time(), 'hourly',  'social_screenshots_daily' );
}

function social_screenshots_deactivate() {
		wp_clear_scheduled_hook( 'social_screenshots_daily'  );
}

add_action('social_screenshots_daily', 'social_screenshots_cron');

register_activation_hook( __FILE__, 'social_screenshots_activate' );
register_deactivation_hook( __FILE__, 'social_screenshots_deactivate' );
require plugin_dir_path( __FILE__ ) . 'includes/class-social-screenshots.php';

$plugin = new Social_Screenshots();
$plugin->run();
