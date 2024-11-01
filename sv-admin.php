<?php

if (!class_exists('SvCvAdmin')) {
    class SvCvAdmin
    {
        public function __construct()
        {
            //Init plugin's configuration menu
            add_action('admin_menu', array($this, 'svcv_plugin_menu'));
            //Init plugin's inputs
            add_action('admin_init', array($this, 'svcv_init_admin_section'));
            // Init plugin's configuration page
            add_action( 'settings_content', array($this,'svcv_input_settings'));
        }

        /**
         * Since v 1.0.0
         * Initialize admin menu
         */
        function svcv_plugin_menu()
        {
            add_options_page('SocialVault', 'SocialVault', 'manage_options', 'settings_socialvault', array($this,'svcv_render_option_page'));
        }

        /**
         * Since v 1.0.0
         * Create plugin option page
         */
        function svcv_render_option_page()
        {
            ?>
            <div class="wrap">
                <form action="options.php" method="post">
                    <?php
                    settings_fields( 'settings_socialvault' );
                    do_settings_sections( 'settings_socialvault' );

                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Since v 1.0.0
         * Initialize admin settings
         */
        function svcv_init_admin_section()
        {

            $svcv_config = (array)get_option('svcv_config');

            register_setting( 'settings_socialvault', 'svcv_config' );

            add_settings_section(
                'svcv_options_edit_callback',
                __("SocialVault Options", 'socialvault'),
                array($this,'svcv_options_edit_callback'),
                'settings_socialvault'
            );

            $this->svcv_init_admin_form($svcv_config);

        }

        /**
         * Since v 1.0.0
         * Update v 1.2.7
         * Generate plugin's form
         * @param $svcv_config
         */
        function svcv_init_admin_form($svcv_config){
            add_settings_field(
                'toggle_widget',
                __("Enable widget", 'socialvault'),
                array($this, 'svcv_input_settings'),
                'settings_socialvault',
                'svcv_options_edit_callback',
                array(
                    'type'  => 'bool',
                    'value' => $svcv_config['toggle_widget'],
                    'name'  => 'svcv_config[toggle_widget]',
                    'id'    => 'svcv_config_toggle_widget'
                )
            );

            $widget_id_class = $svcv_config['widget_valid'] == false && $svcv_config['toggle_widget'] == true ? 'widget_id_not_valid' : '';

            add_settings_field(
                'svcv_config_widget_id',
                __("Your widget ID", 'socialvault'),
                array($this, 'svcv_input_settings'),
                'settings_socialvault',
                'svcv_options_edit_callback',
                array(
                    'type'  => 'widget_id',
                    'value' => $svcv_config['widget_id'],
                    'name'  => 'svcv_config[widget_id]',
                    'id'    => 'svcv_config_widget_id',
                    'class' => $widget_id_class
                )
            );

            // Retrieve all types of templates available
            $templates = array(
                'home_page' => __("Home page", 'socialvault'),
                'archive'  => __("Archives", 'socialvault'),
            );
            $custom_post_types = get_post_types(array('public'   => true));
            foreach ($custom_post_types as $custom_post_type){
                if($custom_post_type !== 'attachment'){
                    $templates[$custom_post_type] = ucfirst($custom_post_type);
                }
            }

            // If none of the checkboxes has been checked, then templates disable will not exist resulting in a php warning.
            $svcvTemplatesDisableValue = array_key_exists('templates_disable', $svcv_config) ? $svcv_config['templates_disable'] : '';

            add_settings_field(
                'svcv_config_templates_disable',
                __("Disable Locations", 'socialvault'),
                array($this, 'svcv_input_settings'),
                'settings_socialvault',
                'svcv_options_edit_callback',
                array(
                    'type'      => 'templates',
                    'value'     => $svcvTemplatesDisableValue,
                    'name'      => 'svcv_config[templates_disable]',
                    'id'        => 'svcv_config_templates_disable',
                    'templates' => $templates
                )
            );
        }

        /**
         * Since v 1.0.0
         * Update v 1.2.0
         * Init plugin's fields, passed by fn svcv_init_admin_section()
         * @param $args array
         */
        function svcv_input_settings($args)
        {
            switch ($args['type']) {
                case 'bool':
                {
                    echo '<input name="' . $args['name'] . '" id="' . $args['id'] . '" type="checkbox" value="true" ';
                    echo $args['value'] == true ? 'checked' : '';
                    echo ' >';
                    break;
                }
                case 'widget_id':
                {
                    echo $args['class'] == 'widget_id_not_valid' ? '<p>' . __("Your widget ID isn't valid..", 'socialvault') . ' </p>' : '' ;
                    echo '<input name="' . $args['name'] . '" id="' . $args['id'] . '" type="' . $args['type'] . '" value="' . $args['value'] . '"';
                    echo !empty($args['class']) ? $args['class'] : '';
                    echo ' ">';
                    break;
                }
                case 'templates':
                {
                    $n = 0;
                    foreach ($args['templates'] as $slug => $name){
                        echo $n !== 0 ? '<br><br>' : '';
                        echo '<input type="checkbox"';
                        if(is_array($args['value'])){
                            echo array_key_exists($slug, $args['value']) ? ' checked ' : '';
                        }
                        echo 'id="' . $slug . '" name="' . $args['name'] . '[' . $slug . ']" value="true"><label for="' . $slug . '">' . $name . '</label>';
                        $n = 1;
                    }
                    break;
                }
                default:
                {
                    echo '<input name="' . $args['name'] . '" id="' . $args['id'] . '" type="' . $args['type'] . '" value="' . $args['value'] . '">';
                }
            }
        }

        function svcv_options_edit_callback(){}

    }
}