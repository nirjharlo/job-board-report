<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Implimentation of WordPress inbuilt functions for plugin activation.
 */
if ( ! class_exists( 'JBR_INSTALL' ) ) {

	final class JBR_INSTALL {

		//@string
		public $textDomin;
		//@string
		public $phpVerAllowed;
		/**
		$pluginPageLinks = array(
								array(
									'slug' => '',
									'label' => ''
								),
							);
		*/
		public $pluginPageLinks;



		public function execute() {
			add_action( 'plugins_loaded', array( $this, 'text_domain_cb' ) );
			add_action( 'admin_notices', array( $this, 'php_ver_incompatible' ) );
			add_filter( 'plugin_action_links', array( $this, 'menu_page_link' ), 10, 2 );
		}



		//Load plugin textdomain
		public function text_domain_cb() {

			load_plugin_textdomain( $this->textDomin, false, PLUGIN_LN );
		}



		//Define low php verson errors
		public function php_ver_incompatible() {

			if ( version_compare( phpversion(), $this->phpVerAllowed, '<' ) ) :
				$text = __( 'The Plugin can\'t be activated because your PHP version', 'jbr' );
				$text_last = __( 'is less than required '.$this->phpVerAllowed.'. See more information', 'jbr' );
				$text_link = 'php.net/eol.php'; ?>

				<div id="message" class="updated notice notice-success is-dismissible"><p><?php echo $text . ' ' . phpversion() . ' ' . $text_last . ': '; ?><a href="http://php.net/eol.php/" target="_blank"><?php echo $text_link; ?></a></p></div>
			<?php endif; return;
		}



		// Add settings link to plugin page
		public function menu_page_link( $links, $file ) {

			if ($this->pluginPageLinks) {
				static $this_plugin;
				if ( ! $this_plugin ) {
					$this_plugin = JBR_FILE;
				}
				if ( $file == $this_plugin ) {
					$shift_link = array();
					foreach ($this->pluginPageLinks as $value) {
						$shift_link[] = '<a href="'.$value['slug'].'">'.$value['label'].'</a>';
					}
					foreach( $shift_link as $val ) {
						array_unshift( $links, $val );
					}
				}
				return $links;
			}
		}
	}
} ?>