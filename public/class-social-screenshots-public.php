<?php
require_once plugin_dir_path( __FILE__ ) . '../includes/class-social-screenshots-http.php';

class Social_Screenshots_Public {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->request_screenshot = new WP_Screenshot_Request();
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function debug($print){
		if($_GET['haremo_debug']){
			echo $print."\n";
		}
	}

	public function buffer_start() {
		global $post;
		if (is_singular()){
				$imageDataRaw = get_post_meta($post->ID, 'social-screenshot', true);
			  $imageData = json_decode($imageDataRaw, true);
				$isDisabled = get_post_meta($post->ID, 'social-screenshots-disable', true);
			  $this->image = $imageData['url'];
				$debug = get_post_meta($post->ID, 'social-screenshot-debug', true);
				$this->debug("<!-- DEBUG SOCIAL SCREENSHOTS PLUGIN \n".$debug."\n -->");
				if(!empty($isDisabled)){
					$this->debug("<!-- IS DEACTIVATED - ".$isDisabled." - ".$imageDataRaw." -->");
					$this->image = false;
				}
				if(!$this->image ){
					$this->debug("<!-- NO SCREENSHOT - ".$imageDataRaw." -->");
				} else {
					$this->debug("<!-- IMAGE: ".$this->image." -->");
				}
			  if((time() > ($imageData['time']+(60*15))) || (!$imageData['url'] && time() > ($imageData['time']+(60*1)))){
					$this->debug("<!-- REQUEST NEW SCREENSHOT -->");
					$this->request_screenshot->data( array( 'postID' => $post->ID) )->dispatch();
			  } else if($_GET['haremo_debug'] && $_GET['haremo_force']){
					$this->debug("<!-- FORCING NEW SCREENSHOT -->");
					$this->request_screenshot->data( array( 'postID' => $post->ID) )->dispatch();
				}
		}
	 	ob_start();
	}

	public function buffer_end() {
		$buffer = ob_get_contents();
		ob_end_clean();
		if($this->image){
			echo preg_replace("/(<.*og:image.*>)/i", "", $buffer);
		} else {
			echo $buffer;
		}
	}

	public function add_opengraph_image() {
		if($this->image){
			echo "<meta property='og:image' content='" . $this->image . "'/>";
		}
	}
}
