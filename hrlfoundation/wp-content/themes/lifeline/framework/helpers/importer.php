<?php

function sh_xml_importer() {
    if ( isset( $_POST['action'] ) && $_POST['action'] == 'theme-install-demo-data' ) {

	$demo = sh_set( $_POST, 'data' );
                
        
        $fileUrl = 'https://s3.amazonaws.com/webinane/dummy/lifeline/'.$demo.'.zip';

//        if ( is_dir( ABSPATH . 'wp-content/uploads' ) ) {
//            sh_delete( ABSPATH . 'wp-content/uploads' );
//        }

        $response = wp_remote_get( $fileUrl, array( 'timeout' => 120, 'httpversion' => '1.1' ) );
        $zipdata  = wp_remote_retrieve_body( $response );
        $file     = $demo.'.zip';
        global $wp_filesystem;
        if ( empty( $wp_filesystem ) ) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        $wp_filesystem->put_contents( ABSPATH . 'wp-content/' . $file, $zipdata, 0777 );
       
        $path = realpath( ABSPATH . 'wp-content/' );
        $zip  = new ZipArchive;
        if ( $zip->open( ABSPATH . 'wp-content/' . $file ) === TRUE ) {
            $zip->extractTo( $path );
            $zip->close();
            unlink( ABSPATH . 'wp-content/' . $file );
        } else {
            echo 'failed';
            exit;
        }

        if ( file_exists( SH_ROOT . "framework/backup/demos/" . $demo . "/data.xml" ) ) {
            define( 'WP_LOAD_IMPORTERS', true );
            if ( !class_exists( 'WP_Import' ) ) {
                include ( SH_ROOT . 'framework/wordpress-importer/wordpress-importer.php');
            }

            $content_xml = SH_ROOT . "framework/backup/demos/" . $demo . "/data.xml";
            if ( !is_file( $content_xml ) ) {
                printr( 'wrong file' );
            } else {
                $GLOBALS['wp_import']                    = new WP_Import();
                $GLOBALS['wp_import']->fetch_attachments = true;
                $GLOBALS['wp_import']->import( $content_xml );

                include_once(SH_ROOT . 'framework/helpers/import_export.php');
		$obj = new SH_import_export($demo);
                $obj->import();
            }
        }
        die();
    }
}

function sh_delete( $path ) {
	if ( is_dir( $path ) === true ) {
		$files = array_diff( scandir( $path ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			sh_delete( realpath( $path ) . '/' . $file );
		}
		return rmdir( $path );
	} else if ( is_file( $path ) === true ) {
		return unlink( $path );
	}
	return false;
}

?>
