<?php

if ( !defined( "SH_DIR" ) )
	die( '!!!' );

class SH_server_auth {

	protected $url = "http://webinane.net/";
	protected $package_name;
	protected $version;
	protected $stiings;
	protected $purchase_code;

	public function __construct() {
		$this->stiings = get_option( SH_NAME );
		$theme = wp_get_theme();
		$theme_name = "lifeline";
		$theme_version = $theme->Version;
		$this->package_name = str_replace( ' ', '', $theme_name );

		$this->version = $theme_version;
		$this->purchase_code = sh_set( $this->stiings, 'purchase_code' );
	}

	public function sh_verification( $type = '' ) {
		$return = '';
		if ( !empty( $this->purchase_code ) ) {
			$url = $this->url . $type;
			$fields = 'name=' . $this->package_name . '&version=' . $this->version . '&code=' . $this->purchase_code . '&_METHOD=POST';
			$data = array( 'name' => $this->package_name, 'version' => $this->version, 'code' => $this->purchase_code, '_METHOD' => 'POST' );
			$post = curl_init();
			curl_setopt( $post, CURLOPT_URL, $url );
			curl_setopt( $post, CURLOPT_POST, count( $data ) );
			curl_setopt( $post, CURLOPT_POSTFIELDS, $fields );
			curl_setopt( $post, CURLOPT_RETURNTRANSFER, 1 );
			$result = curl_exec( $post );
			$error = curl_error( $post );
			curl_close( $post );
			if ( $error ) {
				return $error;
			} else {
				if ( $type == 'check' ) {
					if ( $result != 'Confirmed' ) {
						$return .= '<div class="wrap"><h2>';
						$return .= $result;
						$return .= '</h2></br2><a href=' . admin_url( 'themes.php?page=install-required-plugins' ) . '>' . __( 'Go Back to Plugin Page', 'wp_blog' ) . '</a>';
						$return .= '</div>';

						return $return;
					} else {
						return $result;
					}
				} else {
					return $result;
				}
			}
		} else {
			$return .= '<div class="wrap"><h2>';
			$return .= __( 'Please Enter your purchase code in theme options', 'wp_blog' );
			$return .= '</h2></br><a href=' . admin_url( 'themes.php?page=install-required-plugins' ) . '>' . __( 'Go Back to Plugin Page', 'wp_blog' ) . '</a>';
			$return .= '</div>';

			return $return;
		}
	}

	public function sh_get_plugin( $name ) {
		$return = '';
		if ( !empty( $this->purchase_code ) ) {
			$url = $this->url . 'get_plugin';
			$fields = 'name=' . str_replace( ' ', '', ucfirst( $name ) ) . '&_METHOD=POST';
			$data = array( 'name' => str_replace( ' ', '', ucfirst( $name ) ), '_METHOD' => 'POST' );
			$post = curl_init();
			curl_setopt( $post, CURLOPT_URL, $url );
			curl_setopt( $post, CURLOPT_POST, count( $data ) );
			curl_setopt( $post, CURLOPT_POSTFIELDS, $fields );
			curl_setopt( $post, CURLOPT_RETURNTRANSFER, 1 );
			$result = curl_exec( $post );
			$error = curl_error( $post );
			curl_close( $post );
			if ( $error ) {
				return $error;
			} else {
				return $result;
			}
		} else {
			$return .= '<div class="wrap"><h2>';
			$return .= __( 'Please Enter your purchase code in theme options', 'wp_blog' );
			$return .= '</h2></br><a href=' . admin_url( 'themes.php?page=install-required-plugins' ) . '>' . __( 'Go Back to Plugin Page', 'wp_blog' ) . '</a>';
			$return .= '</div>';

			return $return;
		}
	}

	public function sh_curl( $url, $name ) {
		$return = '';
		$ch = curl_init();
		$options = array(
			CURLOPT_URL => strip_tags( $url ),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HEADER => false,
			//CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_REFERER => strip_tags( $url ),
			CURLOPT_RETURNTRANSFER => true,
		);
		curl_setopt_array( $ch, $options );
		$result = curl_exec( $ch );
		$error = curl_error( $ch );
		curl_close( $ch );
		if ( $error ) {
			$return .= '<div class="wrap"><h2>';
			$return .= $error;
			$return .= '</h2></br><a href=' . admin_url( 'themes.php?page=install-required-plugins' ) . '>' . __( 'Go Back to Plugin Page', 'wp_blog' ) . '</a>';
			$return .= '</div>';
			echo $return;
			exit;
		} else {
			$file = fopen( SH_ROOT . 'temp/' . $name . '.zip', "w+" );
			fputs( $file, $result );
			fclose( $file );
		}
	}

}
