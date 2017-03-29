<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_Xml_importer extends SH_server_auth {

	private static $_instance = null;
	private $demo;

	public function __construct() {
		if ( !is_dir( ABSPATH . 'wp-content/themes/backup/' ) ) {
			mkdir( ABSPATH . 'wp-content/themes/backup/' );
		}
		parent::__construct();
		add_action( 'admin_enqueue_scripts', array( $this, 'sh_importer_script' ) );
	}

	public function sh_demo_importer( $options ) {
		if ( $this->purchase_code != '' ) {
			if ( sh_set( $options, 'data' ) != 'undefined' ) {
				$this->demo = sh_set( $options, 'data' );
			} else {
				$this->demo = 'single';
			}
			$verify = parent::sh_verification( 'check' );
			if ( $verify == 'Confirmed' ) {
				$link = $this->sh_get_xml();
				$this->sh_getcurl_data( $link );
				$backup = ABSPATH . 'wp-content/themes/backup/backup.zip';
				if ( file_exists( $backup ) ) {
					if ( extension_loaded( 'zip' ) != false ) {
						$zip = new ZipArchive();
						$res = $zip->open( ABSPATH . 'wp-content/themes/backup/backup.zip' );
						if ( $res === true ) {
							$zip->extractTo( ABSPATH . 'wp-content/themes/backup/' );
							$zip->close();
							unlink( ABSPATH . 'wp-content/themes/backup/backup.zip' );
							define( 'WP_LOAD_IMPORTERS', true );
							if ( !class_exists( 'WP_Import' ) )
								include ( SH_ROOT . 'framework/wordpress-importer/wordpress-importer.php');

							$content_xml = ABSPATH . 'wp-content/themes/backup/backup/data.xml';
							if ( !is_file( $content_xml ) ) {
								printr( 'wrong file' );
							} else {
								$GLOBALS['wp_import'] = new WP_Import();
								$GLOBALS['wp_import']->fetch_attachments = true;
								$GLOBALS['wp_import']->import( $content_xml );
								require_once(SH_ROOT . 'framework/update/import_export.php');
								$importer = new SH_import_export();
								$importer->import();
							}
						}
					} else {
						echo __( 'The Zip Archive Extention is not enable, please contact yoru hosting administrator.', SH_NAME );
					}
				}
			} else {
				echo __( 'This Purchase Code is not matched with Current product', SH_NAME );
				exit;
			}
		} else {
			echo __( 'Please Enter your purchase code in theme options', SH_NAME );
			exit;
		}
	}

	function sh_remove_backup( $path ) {
		if ( is_dir( $path ) === true ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ), RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $files as $file ) {
				if ( in_array( $file->getBasename(), array( '.', '..' ) ) !== true ) {
					if ( $file->isDir() === true ) {
						rmdir( $file->getPathName() );
					} else if ( ($file->isFile() === true) || ($file->isLink() === true) ) {
						unlink( $file->getPathname() );
					}
				}
			}
			return rmdir( $path );
		} else if ( (is_file( $path ) === true) || (is_link( $path ) === true) ) {
			return unlink( $path );
		}

		return false;
	}

	public function sh_get_xml() {
		$url = "http://webinane.net/xml_check";
		$fields = 'name=' . strtolower( $this->package_name ) . '&sub_demo=' . $this->demo . '&version=' . $this->version . '&code=' . $this->purchase_code . '&_METHOD=POST';
		$data = array( 'name' => strtolower( $this->package_name ), 'sub_demo' => $this->demo, 'version' => $this->version, 'code' => $this->purchase_code, '_METHOD' => 'POST' );
		$post = curl_init();
		curl_setopt( $post, CURLOPT_URL, $url );
		curl_setopt( $post, CURLOPT_POST, count( $data ) );
		curl_setopt( $post, CURLOPT_POSTFIELDS, $fields );
		curl_setopt( $post, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $post );
		$error = curl_errno( $post );
		if ( $error ) {
			echo $error;
			exit;
		} else {
			return $result;
		}
	}

	public function sh_importer_script() {
		wp_enqueue_style( 'SH_imorter', SH_URL . 'css/dropdown.css' );
		$script = array(
			'dropdown' => 'js/dropdown.js',
			'print_element' => 'js/print_element.js',
		);
		foreach ( $script as $key => $s ) {
			wp_enqueue_script( $key, SH_URL . $s, array(), SH_VERSION, true );
		}
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
		$file = fopen( ABSPATH . 'wp-content/themes/backup/backup.zip', "w+" );
		fputs( $file, $result );
		fclose( $file );
	}

	static public function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}
