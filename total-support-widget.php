<?php
/*
 * Plugin Name: Total Support Widget
 * Plugin URI: https://www.ampodcastnetwork.com/forum
 * Description: The Total Support Widget gives you Patreon, 20 social & support links, and Sendy or Mailchimp signup.
 * Version: 1.0.7
 * Author: AM Podcast Network
 * Author URI: https://www.ampodcastnetwork.com
 */


include_once('PSW/Form.php');
include_once('PSW/Form/PodcastSupportUs.php');
include_once('PSW/Widget/PodcastSupportUs.php');

class AM_Socials_Widget {

    private $_widget;
    /**
     * Register widget with WordPress.
     */
    function __construct() {        
        add_action('wp_head', array($this, 'load_wp_scripts_css'));
        add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts_css'));
        
        add_action('wp_ajax_sendy_subscribe', array($this, 'sendySubscribeAjax'));
        add_action('wp_ajax_nopriv_sendy_subscribe', array($this, 'sendySubscribeAjax'));

        add_action('wp_head', array($this, 'defineAjaxUrl'));

        add_action( 'widgets_init', array($this, 'register_am_socials_widget'));        
    }
    
    // register Foo_Widget widget
    function register_am_socials_widget() {
        register_widget('PSW_Widget_PodcasrSupportUs');
    }


    function defineAjaxUrl() {
        ?>
        <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    }    
    
    public function load_wp_scripts_css()
    {
        wp_enqueue_script('am-socials-wp-script1', plugin_dir_url(__FILE__) . 'scripts/wp.js');
        wp_enqueue_style('am-socials-wp-style1', plugin_dir_url(__FILE__) . 'styles/wp-styles.css');
        wp_enqueue_style('am-socials-font-awesome', plugin_dir_url(__FILE__) . 'styles/font-awesome.min.css');
    }

    public function load_admin_scripts_css()
    {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_media();
        
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script(
            'wp-color-picker',
            admin_url( 'js/color-picker.min.js'),
            array( 'iris' ),
            false,
            1
        ); 
    
        wp_enqueue_script('am-socials-admin-script1', plugin_dir_url(__FILE__) . 'scripts/admin.js');
        wp_enqueue_style('am-socials-admin-jquery-ui', plugin_dir_url(__FILE__) . 'styles/jquery.ui.css');
        wp_enqueue_style('am-socials-admin-style1', plugin_dir_url(__FILE__) . 'styles/admin.css');
    }

    function sendySubscribeAjax()
    {
        $json_data = $this->sendySubscribe();
        print_r(json_encode($json_data));
        exit;            
    }
    function sendySubscribe() {
        $guid = $_POST['guid'];
        $am_socials = get_option('am-newsletter-options');
        $type = $am_socials[$guid]['type'];

        $success = $am_socials[$guid]['success'];
        if ($type == 'sendy') {
            $sendy_url = $am_socials[$guid]['sendy']['url'];
            $sendy_key = $am_socials[$guid]['sendy']['key'];
            $sendy_list = $am_socials[$guid]['sendy']['list'];            
        
            $url = $sendy_url . '/subscribe';

			//POST variables
			$name = $_POST['fname'];
			$email = $_POST['email'];
			$boolean = 'true';
			
			//Check fields
			if($email=='') {
				$json_data['success'] = false;
				$json_data['message'] = 'Please fill in all fields.';
			}
			else {
				//Subscribe
				$postdata = http_build_query(
					array(
					'name' => $name,
					'email' => $email,
					'list' => $sendy_list,
					'boolean' => 'true'
					)
				);

				$opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
				$context  = stream_context_create($opts);
				$result = file_get_contents($url, false, $context);
				
				//check result
				if ($result) {
					$json_data['success'] = true;
					$json_data['message'] = $success;
				}
				else {
					$json_data['message'] = $result;
				}
			}
        }
        else if ($type == 'mailchimp') {
            $mailchimp_key = $am_socials[$guid]['mailchimp']['key'];
            $mailchimp_list = $am_socials[$guid]['mailchimp']['list'];            
            require_once plugin_dir_path(__FILE__) . 'includes/mcapi.class.php';
            $api = new MCAPI($mailchimp_key);
            //$retval = $api->lists();
            $email = $_POST['email']; // Enter subscriber email address
            $name = $_POST['fname']; // Enter subscriber first name
            $lname = $_POST['lname']; // Enter subscriber last name

            // By default this sends a confirmation email - you will not see new members
            // until the link contained in it is clicked!

            $merge_vars = array('FNAME' => $name, 'LNAME' => $lname);
            if($api->listSubscribe($mailchimp_list, $email,$merge_vars) === true) {
                $json_data['success'] = true;
                $json_data['message'] = $success;
            }
            else {
                $json_data['message'] = 'An error occured!';
            }
        }
        else {
            $json_data['message'] = 'An error occured!';
        }
        
        return $json_data;    
    }
} // AM_Socials_Widget

new AM_Socials_Widget();