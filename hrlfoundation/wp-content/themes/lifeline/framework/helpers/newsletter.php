<?php
define('SUBSCRIBEPOPUP_COOKIE', 'dfwooproject');

class SH_Newsletter {
    
    static public function lifeline_web_subscribepopup_submit($options) {

        global $wpdb;

        $opt = get_option(SH_NAME);
        $email = trim($options['email']);
        if (get_magic_quotes_gpc()) {
            $email = stripslashes($email);
        }
        
        $errors = array();
        $notify = array();
        if ($email == '')
            $errors[] = esc_html__('Please Enter Valid Email Address', 'lifeline');
        if (sh_set($opt, 'mailchimp_list_id') == '')
            $errors[] = esc_html__('Please Enter mailchimp list id', 'lifeline');
        if (sh_set($opt, 'mailchimp_api_key') == '')
            $errors[] = esc_html__('Please Enter mailchimp api key', 'lifeline');
        
        if ($email != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email))
            $errors[] = esc_html__('Please Enter Valid Email Address', 'lifeline');
        if (sizeof($errors) == 0) {
            $tmp = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) AS total FROM {$wpdb->prefix}lifeline_web_newsletter WHERE deleted = %s AND email = %s", '0', $wpdb->_real_escape($email)), ARRAY_A);
            if ($tmp["total"] > 0) {
                $sql = $wpdb->prepare("UPDATE {$wpdb->prefix }lifeline_web_newsletter SET registered = %d WHERE deleted = %s AND email = %s", time(), '0', $wpdb->_real_escape($email));
                $wpdb->query($sql);
            } else {
                $sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}lifeline_web_newsletter ( email, registered, deleted) VALUES (%s, %d, %s)", $wpdb->_real_escape($email), time(), '0');
                $wpdb->query($sql);
            }
            
            if (in_array('curl', get_loaded_extensions())) {
                $list_id = sh_set($opt, 'mailchimp_list_id');
                $dc = "us1";
                if (strstr(sh_set($opt, 'mailchimp_api_key'), "-")) {
                    list($key, $dc) = explode("-", sh_set($opt, 'mailchimp_api_key'), 2);
                    if (!$dc)
                        $dc = "us1";
                }

                $apikey = sh_set($opt, 'mailchimp_api_key');
                $auth = base64_encode('user:' . $apikey);
                $get_name = explode('@', $email);
                $data = array(
                    'apikey' => $apikey,
                    'email_address' => $email,
                    'status' => 'subscribed',
                    'merge_fields' => array(
                        'FNAME' => sh_set($get_name, '0')
                    )
                );
                $json_data = json_encode($data);
                $request = array(
                    'headers' => array(
                        'Authorization' => 'Basic ' . $auth,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Content-Length' => strlen($json_data),
                    ),
                    'httpversion' => '1.0',
                    'timeout' => 10,
                    'method' => 'POST',
                    'user-agent' => 'PHP-MCAPI/2.0',
                    'sslverify' => false,
                    'body' => $json_data,
                );
                
                $req = wp_remote_post('https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/', $request);
                $r = json_decode(sh_set($req, 'body'));
                if (preg_match("/The requested resource could not be found./", sh_set($r, 'detail')) == true) {
                    $notify[] = '<div class="alert alert-warning"><strong>Invalid List ID.</div>';
                } elseif (preg_match("/{$email} is already a list member.  Use PUT to insert or update list members/", sh_set($r, 'detail')) == true) {
                    $notify[] = "<div class='alert alert-warning'><strong>{$email} is Already Exists.</div>";
                } else {
                    $notify[] = '<div class="alert alert-success"><strong>Thank you for subscribing with us.</div>';
                }
            }
            setcookie("subscribepopup", SUBSCRIBEPOPUP_COOKIE, time() + 3600 * 24 * 180, "/");
        } else {
            echo '<div class="alert alert-warning"><strong>' . esc_html__("ERROR: ", 'lifeline') . '</strong>' . implode(", ", $errors) . '</div>';
        }
        if (!empty($notify)) {
            foreach ($notify as $n) {
                $allowd_html = array(
                    'div' => array(
                        'class' => array(),
                    ),
                    'strong' => array(),
                );
                echo wp_kses($n, $allowd_html);
            }
        }
        exit;
    }

    static public function lifeline_web_newsletter_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . "lifeline_web_newsletter";
        $sql = "CREATE TABLE " . $table_name . " (
			id int(11) NOT NULL auto_increment,
			email varchar(255) collate utf8_unicode_ci NOT NULL,
			registered int(11) NOT NULL,
			deleted int(11) NOT NULL default '0',
			UNIQUE KEY  id (id)
		);";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        ($sql);
    }

}
