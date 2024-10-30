<?php
require_once plugin_dir_path( __FILE__ ) . '../includes/class-social-screenshots-http.php';

class Social_Screenshots_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->request_screenshot = new WP_Screenshot_Request();
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/social-screenshots-admin.css', array(), $this->version, 'all' );
	}
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/social-screenshots-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function parse_request (){
		global $post;
		if($_POST['haremo_social_screenshot_image'] && $_POST['haremo_social_screenshot_url']){
			 $postid = url_to_postid( $_POST['haremo_social_screenshot_url'] );
			 if($postid){
				 	$data = json_encode(array("time" => time(),"updatedAt"=>strtotime($_POST['haremo_social_screenshot_updated_at']), "url"=> $_POST['haremo_social_screenshot_image']));
					$success = update_post_meta($postid, 'social-screenshot', $data);
					echo json_encode(array("success" => $success, "data" => $data));
					exit;
			 }
		}
	}

	public function save_post($postid) {
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;
	    if ( !current_user_can( 'edit_page', $postid ) ) return false;
	    if(empty($postid)) return false;
	    if(isset($_POST['social-screenshots-disable'])){
        update_post_meta($postid, 'social-screenshots-disable', 1, true );
	    } else {
        delete_post_meta($postid, 'social-screenshots-disable');
	    }
	}

	public function add_to_publishbox() {
    global $post;
		$isPublished = ($post->post_status == "publish");
		$imageDataRaw = get_post_meta($post->ID, 'social-screenshot', true);
	  $imageData = json_decode($imageDataRaw);
		$debug = get_post_meta($post->ID, 'social-screenshot', true);
		$value = get_post_meta($post->ID, 'social-screenshots-disable', true);
    ?>
			<?php if($isPublished){ ?>
				<div class="misc-pub-section">
					<label><input type="checkbox"<?php echo (!empty($value) ? ' checked="checked"' : null) ?> value="1" name="social-screenshots-disable" /> Disable HAREMO Social Screenshots</label>
				</div>
				<?php if($imageData->url) { ?>
			    <div class="misc-pub-section" style="padding-bottom: 0px;">
						<div style="text-align: center;">Preview of your Screenshot:</div>
						<div class="haremo-image-wrapper">
							<img src="<?=$imageData->url?>" />
						</div>
			    </div>
				<?php } ?>
				<!--<div class="misc-pub-section" style="text-align: center;padding-top: 0px;">
					<a class="button haremo-refresh-button">
						<span class="dashicons dashicons-backup"></span> Refresh Screenshot
					</a>
				</div>-->
			<?php } else { ?>
		    <div class="misc-pub-section">
		        <label style="text-align: center;">HAREMO Social Screenshots works only when your post is published</label>
		    </div>
		<?php } ?>
	<?php
	}
}
