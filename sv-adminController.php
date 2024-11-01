<?php

class SvCvAdminController
{
    public function __construct()
    {
        add_action('pre_update_option', array($this, 'checkWidgetid'), 10, 3);
    }


    /**
     * Since v 1.0.0
     * Controll if the widget id is valid
     * @param $value
     * @param $option
     * @param $old_value
     * @return array
     * @throws Exception
     */
    public function checkWidgetid($value, $option, $old_value)
    {
        if($option === 'svcv_config'){
            $request = wp_remote_get('https://socialvault.com/snippet_check/' . $value['widget_id'] . '/socialvault.js');
            $newValue = array();
            if(is_array($request) && ! is_wp_error($request)){
                // We check if widget id is valid
                $newValue['widget_valid'] = json_decode($request['body'])->response;
            }else{
                throw new Exception(__('An internal error as occured with curl request.', 'socialvault'));
            }
            // We make sure that toggle_widget is registered in sv options
            $newValue['toggle_widget'] = !isset($value['toogle_widget']) ? false : true;
            $value = wp_parse_args($value, $newValue);
        }
        return $value;
    }
}