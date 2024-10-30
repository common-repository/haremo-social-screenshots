<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/background-processing/wp-background-processing.php';
require_once(ABSPATH .'wp-includes/pluggable.php');

class WP_Screenshot_Request extends WP_Background_Process {
	/**
	 * @var string
	 */
	protected $action = 'social-screenshots-request';
	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
	  echo "<!-- REQUEST SCREENSHOT: ".$this->url." -->";

		$response = wp_remote_post( 'https://api.social-screenshots.haremo.de/v0.1/request', array(
				'method' => 'POST',
				'timeout' => 5,
				'sslverify'=>false,
				'body' => json_encode(array("url"=> $this->url, "version" => $this->version))
		));

		if ( is_wp_error( $response ) ) {
	    update_option('screenshot_'.$this->url, json_encode(array("time" => time())));
		} else {
	  	$result = json_decode($response);
		  if($result->url){
		    update_option('screenshot_'.$this->url, json_encode(array("time" => time(), "url"=> $result->url)));
		  } else {
		    update_option('screenshot_'.$this->url, json_encode(array("time" => time())));
		  }
		}
		return false;
	}
	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
		// Show notice to user or perform some other arbitrary task...
	}
}
