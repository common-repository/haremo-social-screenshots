<?php

class Social_Screenshots_i18n {
	
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'social-screenshots',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
