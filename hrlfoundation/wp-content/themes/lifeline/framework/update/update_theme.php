<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_Update_theme extends SH_server_auth {

	static $instance;
	private static $_instance = null;
	private $current_theme;
	private $backup;
	private $delete_backup;

	public function __construct() {
		self::$instance = &$this;
		if ( !is_dir( ABSPATH . 'wp-content/themes/temp_update/' ) ) {
			mkdir( ABSPATH . 'wp-content/themes/temp_update/' );
			mkdir( ABSPATH . 'wp-content/themes/temp_update/new/' );
		}
		parent::__construct();
		$this->backup = sh_set( $this->stiings, 'create_backup' );
		$this->delete_backup = sh_set( $this->stiings, 'delete_old_backup' );
		$theme = wp_get_theme();

		$this->current_theme = ( preg_match_all( "/\bChild/", $theme->Name, $output_array ) ) ? $theme->Template : strtolower( str_replace( ' ', '', $theme->Name ) );
	}

	public function sh_update_theme_module() {
		if ( $this->purchase_code != '' ) {
			$verify = parent::sh_verification( 'check' );
			$sourcePath = ABSPATH . 'wp-content/themes/' . $this->current_theme;
			$outZipPath = ABSPATH . 'wp-content/themes/backup_' . $this->current_theme . '.zip';
			if ( $verify == 'Confirmed' ) {
				$link = parent::sh_verification( 'update' );
				$this->sh_getcurl_data( $link );
				$target_file = ABSPATH . 'wp-content/themes/temp_update/' . $this->current_theme . '.zip';
				if ( file_exists( $target_file ) ) {
					if ( $this->delete_backup == 1 ) {
						$this->sh_unlink_backups();
					}
					if ( $this->backup == 1 ) {
						SH_Update_theme::$_instance->zipDir( $sourcePath, $outZipPath );
					}

					if ( extension_loaded( 'zip' ) ) {
						$zip = new ZipArchive();
						$file = ABSPATH . 'wp-content/themes/temp_update/' . $this->current_theme . '.zip';

						$res = $zip->open( $file );
						if ( $res === TRUE ) {
							$test = '';
							$zip->extractTo( ABSPATH . 'wp-content/themes/temp_update/new/' );
							$zip->close();
							$tem_dir = scandir( ABSPATH . 'wp-content/themes/temp_update/new/' );
							if ( $tem_dir && is_array( $tem_dir ) ) {
								unset( $tem_dir[0] );
								unset( $tem_dir[1] );
							}
							foreach ( $tem_dir as $k => $v ) {

								if ( substr( $v, -4 ) == '.zip' ) {
									unset( $tem_dir[$k] );
									$test = $k;
								}
							}
							$file_key = array_keys( $tem_dir );
							$del_scann = scandir( ABSPATH . 'wp-content/themes/temp_update/new' );
							rename( ABSPATH . 'wp-content/themes/temp_update/new/' . sh_set( $tem_dir, sh_set( $file_key, 0 ) ), ABSPATH . 'wp-content/themes/temp_update/new/' . $this->current_theme );
							if ( !copy( ABSPATH . 'wp-content/themes/' . $this->current_theme . '/style.css', ABSPATH . 'wp-content/themes/temp_update/new/' . $this->current_theme . '/style.css' ) ) {
								$errors = error_get_last();
								echo "COPY ERROR: " . $errors['type'];
								echo "<br />\n" . $errors['message'];
								echo "<br />\n" . __( 'The stylesheet code not be updated please update it as per your requirement', 'wp_blog' );
							} else {

								$latest_version = get_option( 'contempo-notifier-cache' );
								$xml = simplexml_load_string( $latest_version );
								if ( is_child_theme() ) {
									$old_theme = wp_get_theme( $this->current_theme );
								} else {
									$old_theme = wp_get_theme();
								}
								$new_theme = $xml->latest;
								$contents = '';
								$file = new SplFileObject( ABSPATH . 'wp-content/themes/temp_update/new/' . $this->current_theme . '/style.css', 'r' );
								while ( !$file->eof() ) {
									$contents .= $file->fgets();
								}
								$file = null;
								$file = new SplFileObject( ABSPATH . 'wp-content/themes/temp_update/new/' . $this->current_theme . '/style.css', 'w+' );

								$line = str_replace( 'Version: ' . $old_theme->version, 'Version: ' . $new_theme, $contents );

								$file->fwrite( $line );
								$file = null;
							}
							$unset_file = ABSPATH . 'wp-content/themes/temp_update/new' . sh_set( $del_scann, $test );
							$zip_theme = new ZipArchive();
							self::zipDir( ABSPATH . 'wp-content/themes/temp_update/new/' . $this->current_theme, ABSPATH . 'wp-content/themes/temp_update/new/' . $this->current_theme . '.zip' );
							$zip_theme->open( ABSPATH . 'wp-content/themes/temp_update/new/' . $this->current_theme . '.zip' );
							//->extractTo(ABSPATH . 'wp-content/themes/');
							$zip_theme->close();
							$this->switch_theme( get_option( 'template', true ), get_option( 'stylesheet', true ) );
							$this->sh_cleanup();
							echo __( 'Theme Update Successfully', 'wp_blog' );
						} else {
							echo __( 'There is an error during update', 'wp_blog' );
						}
					}
				}
			} else {
				echo __( 'This Purchase Code is not matched with Current product', 'wp_blog' );
				exit;
			}
		} else {
			echo __( 'Please Enter your purchase code in theme options', 'wp_blog' );
			exit;
		}
	}

	public function sh_cleanup() {
		$this->sh_delete_dir( ABSPATH . 'wp-content/themes/temp_update/' );
	}

	public function switch_theme( $template, $stylesheet ) {
		global $wp_theme_directories, $sidebars_widgets;
		if ( is_array( $sidebars_widgets ) )
			set_theme_mod( 'sidebars_widgets', array( 'time' => time(), 'data' => $sidebars_widgets ) );

		$old_theme = wp_get_theme();
		update_option( 'template', $template );
		update_option( 'stylesheet', $stylesheet );
		if ( count( $wp_theme_directories ) > 1 ) {
			update_option( 'template_root', get_raw_theme_root( $template, true ) );
			update_option( 'stylesheet_root', get_raw_theme_root( $stylesheet, true ) );
		}

		delete_option( 'current_theme' );
		$theme = wp_get_theme();
		if ( is_admin() && false === get_option( "theme_mods_$stylesheet" ) ) {
			$default_theme_mods = (array) get_option( "mods_$theme" );
			add_option( "theme_mods_$stylesheet", $default_theme_mods );
		}
		update_option( 'theme_switched', $old_theme );
		do_action( 'switch_theme', $theme );
	}

	private static function folderToZip( $folder, &$zipFile, $exclusiveLength ) {
		$handle = opendir( $folder );
		while ( false !== $f = readdir( $handle ) ) {
			if ( $f != '.' && $f != '..' ) {
				$filePath = "$folder/$f";
				$localPath = substr( $filePath, $exclusiveLength );
				if ( is_file( $filePath ) ) {
					$zipFile->addFile( $filePath, $localPath );
				} elseif ( is_dir( $filePath ) ) {
					$zipFile->addEmptyDir( $localPath );
					self::folderToZip( $filePath, $zipFile, $exclusiveLength );
				}
			}
		}
		closedir( $handle );
	}

	public static function zipDir( $sourcePath, $outZipPath ) {
		$pathInfo = pathInfo( $sourcePath );
		$parentPath = $pathInfo['dirname'];
		$dirName = $pathInfo['basename'];
		$z = new ZipArchive();
		$z->open( $outZipPath, ZIPARCHIVE::CREATE );
		$z->addEmptyDir( $dirName );
		self::folderToZip( $sourcePath, $z, strlen( "$parentPath/" ) );
		$z->close();
	}

	public function sh_unlink_backups() {
		$directory = ABSPATH . 'wp-content/themes/';
		$data = @scandir( $directory );
		if ( !$data )
			return array();
		if ( $data && is_array( $data ) )
			unset( $data[0], $data[1] );
		$return = array();
		foreach ( $data as $d ) {
			if ( substr( $d, -4 ) == '.zip' ) {
				$name = substr( $d, 0, ( strlen( $d ) - 3 ) );
				$return[] = $name;
			}
		}
		foreach ( $return as $theme ) {
			if ( strpos( $theme, 'backup_' . strtolower( $this->current_theme ) ) !== false ) {
				unlink( ABSPATH . 'wp-content/themes/' . $theme . 'zip' );
			}
		}
	}

	public function sh_getcurl_data( $url ) {
		$safe = ini_get( 'safe_mode' );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		if ( !$safe_mode )
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_REFERER, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		$result = curl_exec( $ch );
		curl_close( $ch );
		$file = fopen( ABSPATH . 'wp-content/themes/temp_update/' . $this->current_theme . '.zip', "w+" );
		fputs( $file, $result );
		fclose( $file );
	}

	public function sh_delete_dir( $path ) {
		if ( is_dir( $path ) === true ) {
			$files = array_diff( scandir( $path ), array( '.', '..' ) );

			foreach ( $files as $file ) {
				$this->sh_delete_dir( realpath( $path ) . '/' . $file );
			}

			return rmdir( $path );
		} else if ( is_file( $path ) === true ) {
			return unlink( $path );
		}

		return false;
	}

	static public function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}
