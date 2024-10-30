<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/background-processing/wp-background-processing.php';
require_once(ABSPATH .'wp-includes/pluggable.php');

class WP_Screenshot_Request extends WP_Async_Request {

	protected $action = 'social-screenshots-http';

	protected function handle() {
		$id = $_POST['postID'];
		$url = get_permalink($id);
		if($url){
			$response = wp_remote_post( 'https://api.social-screenshots.haremo.de/v0.1/request', array(
					'method' => 'POST',
					'timeout' => 30,
					'sslverify'=>false,
					'body' => json_encode(array("url" => $url, "version" => $this->version))
			));

			update_post_meta($id, 'social-screenshot-debug', json_encode($response));
			if ( is_wp_error( $response ) ) {
		    update_post_meta($id, 'social-screenshot', json_encode(array("time" => time())));
			} else {
		  	$result = json_decode($response['body']);
			  if($result->url){
			    update_post_meta($id, 'social-screenshot', json_encode(array("time" => time(),"updatedAt"=>strtotime($result->updatedAt), "url"=> $result->url)));
			    return $result->url;
			  } else {
			    update_post_meta($id, 'social-screenshot', json_encode(array("time" => time())));
			    return false;
			  }
			}
		}
	}
}
