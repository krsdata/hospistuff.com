<?php
if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_Update_notifier {

	private $stiings;
	private $notifier;
	private $interval;
	private $package_name;
	private $version;
	private $xml;

	public function __construct() {
		$this->stiings = get_option( SH_NAME );
		$this->notifier = sh_set( $this->stiings, 'xml_update_notifier' );
		$this->interval = sh_set( $this->stiings, 'update_xml_notifier' );
		$theme = wp_get_theme();
		$theme_name = ( preg_match_all( "/\bChild/", $theme->Name, $output_array ) ) ? $theme->Template : "Lifeline";
		if ( is_child_theme() ) {
			$theme = wp_get_theme( $theme_name );
		} else {
			$theme = wp_get_theme();
		}
		$theme_version = $theme->Version;
		$this->package_name = $theme_name;
		$this->version = $theme_version;
		$this->xml = $this->get_latest_theme_version( $this->interval * 3600 );
		$page = sh_set( $_GET, 'page' );
		if ( $this->notifier == 1 && $page != 'install-required-plugins' ) {
			add_action( 'admin_menu', array( $this, 'sh_update_notifier_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'sh_udpate_script' ) );
			add_action( 'admin_bar_menu', array( $this, 'sh_update_top_bar_notifier' ), 999 );

			if ( (string) $this->version < (string) $this->xml->latest )
				add_action( 'admin_notices', array( $this, 'sh_xml_update_notice' ), 9 );
		}
	}

	public function sh_update_notifier_menu() {
		if ( (string) $this->version < (string) $this->xml->latest ) {
			add_dashboard_page( $this->package_name . 'Theme Updates', $this->package_name . '<span class="update-plugins count-1"><span class="update-count">' . __( 'New Update', 'wp_blog' ) . '</span></span>', 'administrator', str_replace( ' ', '-', strtolower( $this->package_name ) ) . '-updates', array( $this, 'sh_update_notifier' ) );
		}
	}

	public function sh_update_top_bar_notifier() {
		global $wp_admin_bar;
		$href = get_admin_url() . 'index.php?page=' . str_replace( ' ', '-', strtolower( $this->package_name ) ) . '-updates';
		$args = array(
			'id' => str_replace( ' ', '-', strtolower( $this->package_name ) ),
			'title' => $this->package_name . __( ' New Update', 'wp_blog' ),
			'href' => $href,
			'meta' => array( 'class' => 'my-toolbar-page' )
		);
		$wp_admin_bar->add_node( $args );
	}

	public function sh_udpate_script() {
		if ( sh_set( $_GET, 'page' ) == str_replace( ' ', '-', strtolower( $this->package_name ) ) . '-updates' ) {
			wp_enqueue_style( 'SH_update_notifier', SH_URL . 'css/notifier.css', array(), SH_VERSION, 'all' );
			wp_enqueue_style( 'SH_loader', SH_URL . 'css/ball-scale-ripple-multiple.css', array(), SH_VERSION, 'all' );
		}
	}

	public function sh_xml_update_notice() {
		?>
		<div id="message" class="updated">
			<p>
				<strong>
					<?php _e( 'There is a new version of the', 'wp_blog' ) ?> <?php echo $this->package_name; ?> <?php _e( 'theme available.', 'wp_blog' ) ?>
				</strong>
				<?php _e( 'You have version', 'wp_blog' ) ?> <?php echo $this->version; ?> <?php _e( 'installed. Update to version.', 'wp_blog' ) ?> <?php echo $this->xml->latest; ?>.&nbsp;<a href="<?php echo get_admin_url() . 'index.php?page=' . str_replace( ' ', '-', strtolower( $this->package_name ) ) . '-updates' ?>"><?php _e( 'View Change log', 'wp_blog' ) ?></a>
			</p>
		</div>
		<?php
	}

	public function sh_update_notifier() {
		?>
		<div class="wrap">
			<div class="overlay" style="display: none !important;"><div class="loader-center loader-inner ball-scale-ripple-multiple"><div></div><div></div><div></div></div></div>
			<span class="dashicons dashicons-share-alt"></span>
			<h2 class="update_title"><?php echo $this->package_name; ?> <?php _e( 'Theme Updates', 'wp_blog' ) ?></h2>
			<img src="<?php echo SH_URL . 'screenshot.png'; ?>" />

			<div id="instructions">
				<h3 class="title"><?php _e( 'Update Download and Instructions', 'wp_blog' ) ?></h3>
				<p>
					<strong><?php _e( 'Please note:', 'wp_blog' ) ?></strong> 
					<?php _e( 'make a', 'wp_blog' ) ?> 
					<strong><?php _e( 'backup', 'wp_blog' ) ?></strong> 
					<?php _e( 'of the Theme inside your WordPress installation folder', 'wp_blog' ) ?> 
					<strong><?php _e( '/wp-content/themes/', 'wp_blog' ) ?><?php echo strtolower( $this->package_name ); ?>/</strong>
				</p>
				<p><?php _e( 'Or if you want to make', 'wp_blog' ) ?> 
					<strong><?php _e( 'auto backup', 'wp_blog' ) ?></strong>, 
					<?php _e( 'please make sure in Theme Options go to Auto Update section and chek is Backup option in on? if not then on', 'wp_blog' ) ?>
				</p>
			</div>

			<h3 class="title"><?php _e( 'Changelog', 'wp_blog' ) ?></h3>
			<?php echo $this->xml->changelog; ?>
			<div class="update_btn"><a id="update_notifier_inner" href="javascript:void(0)" class="button"><?php _e( 'Update', 'wp_blog' ) ?></a></div>
		</div>

		<?php
	}

	public function get_latest_theme_version( $interval ) {
		$notifier_file_url = 'http://webinane.net/server/functions/projects/project_xml/' . str_replace( ' ', '', strtolower( $this->package_name ) ) . '.xml';
		$db_cache_field = 'contempo-notifier-cache';
		$db_cache_field_last_updated = 'contempo-notifier-last-updated';

		$last = get_option( $db_cache_field_last_updated );
		$now = time();

		if ( !$last || ( ( $now - $last ) > $interval ) ) {
			if ( function_exists( 'curl_init' ) ) {
				$cache = self::sh_getcurl_data( $notifier_file_url );
			} else {
				$cache = file_get_contents( $notifier_file_url );
			}

			if ( $cache ) {
				update_option( $db_cache_field, $cache );
				update_option( $db_cache_field_last_updated, time() );
			}
			$notifier_data = get_option( $db_cache_field );
		} else {
			$notifier_data = get_option( $db_cache_field );
		}

		$xml = simplexml_load_string( $notifier_data );
		return $xml;
	}

	public function sh_getcurl_data( $url ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_REFERER, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		$result = curl_exec( $ch );
		curl_close( $ch );
		return $result;
	}

}
