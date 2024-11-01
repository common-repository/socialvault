<?php
/**
 * Plugin Name: SocialVault
 * Plugin URI: https://socialvault.com
 * Description: Add your SocialVault snippet to your WordPress site
 * Author: SocialVault
 * Version: 1.2.7
 * Author URI: https://socialvault.com
 * Text Domain: socialvault
 * Domain Path: /languages/
 */

register_activation_hook(__FILE__,'svcv_install');
register_uninstall_hook (__FILE__,'svcv_uninstall');

/**
 * Since v 1.0.0
 * Update v 1.2.1
 * Install option config
 */
function svcv_install()
{
    if (!get_option('svcv_config', false)) {
        add_option('svcv_config',
                   array(
                       'widget_id'         => '',
                       'toggle_widget'     => '',
                       'templates_disable' => array(),
                       'widget_valid'      => false
                   ));
    }
}

/**
 * Since v 1.0.0
 * Uninstall option config
 */
function svcv_uninstall()
{
    delete_option('svcv_config');
}

if (!class_exists('CvSocialvault', false)) {

    add_action( 'plugins_loaded', 'socialvault_init' );
    function socialvault_init() {
        $sv = new CvSocialvault();
    }

    /**
     * Main SocialVault plugin class
     */
    final class CvSocialvault
    {
        public function __construct()
        {
            add_action('init',               array($this, 'initialize'));
            add_action('wp_enqueue_scripts', array($this, 'svcv_enqueue_script'));
            add_action('admin_print_styles', array($this, 'svcv_admin_stylesheet'));
        }

        /**
         * Since v 1.0.0
         * Init plugin config
         */
        function initialize()
        {
            if (current_user_can('administrator')) {
                load_plugin_textdomain( 'socialvault', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
                require('sv-admin.php');
                new SvCvAdmin();
                require('sv-adminController.php');
                new SvCvAdminController();
            }
        }

        /**
         * Since v 1.0.0
         * Update v 1.2.0
         * Enqueue Widget on front
         */
        function svcv_enqueue_script()
        {
            $svcv_config = (array)get_option('svcv_config');
            $display_widget = false;
            if (is_front_page() && !isset($svcv_config['templates_disable']['home_page'])) {
                $display_widget = true;
            }
            if (is_archive() && !isset($svcv_config['templates_disable']['archive'])){
                $display_widget = true;
            }
            if(!is_front_page() && !is_archive() && !isset($svcv_config['templates_disable'][get_post_type()])){
                $display_widget = true;
            }
            if($display_widget === true && $svcv_config['widget_id'] !== null && $svcv_config['toggle_widget'] == true && $svcv_config['widget_valid'] == true){
                wp_enqueue_script('svcv_front_widget', 'https://socialvault.com/snippet/' . $svcv_config['widget_id'] . '/socialvault.js', array('jquery'), false, true);
            }
        }

        /**
         * Since v 1.0.0
         * Enqueue Admin assets
         */
        function svcv_admin_stylesheet()
        {
            wp_enqueue_style('svcv_assets_css', plugins_url('/assets/css/socialvault.min.css', __FILE__));
        }
    }
}

?>
