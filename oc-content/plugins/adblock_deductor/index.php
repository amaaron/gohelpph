<?php
/*
Plugin Name: Adblock Deductor
Plugin URI: http://forums.osclass.org/plugins/(new-plugin)-adblock-deductor/
Description: This plugin detects whether the visitor to this site has AdBlockPlus enabled browser and then display a alert message to disable adblock software.
Version: 1.1.0
Author: RajaSekar
Author URI: http://www.osclass.org/
Short Name: adblock_deductor
Plugin update URI: adblock-deductor
*/

    // NO NEED TO MODIFY ANYTHING ON THIS FILE

    function adblock_load_files() {
        osc_enqueue_style('jquery-confirm-css', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'jquery.confirm.css');

        osc_register_script('jquery-confirm', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'jquery.confirm.js', array('jquery'));
        osc_register_script('jquery-block', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'blockBlock.jquery.js', array('jquery'));
        osc_register_script('adblock-test', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'advertisement.js', array('jquery'));
        osc_enqueue_script('jquery-confirm');
        osc_enqueue_script('jquery-block');
        osc_enqueue_script('adblock-test');
    }

    function adblock_deductor() { ?>
        <script>
            $(function(){
                if($.adblock){
                    $.confirm({
                        'title'		: '<?php echo osc_esc_js(__('Adblocker active!', 'adblock_deductor')); ?>',
                        'message'	: '<?php echo osc_esc_js(__('You are running an adblocker extension in your browser. You made a kitten cry. We need money to operate the site, and almost all of that comes from our online advertising. If you wish to continue to this website you might consider disabling it.', 'adblock_deductor')); ?>',
                        'buttons'	: {
                            '<?php echo osc_esc_js(__('I will do it!', 'adblock_deductor')); ?>'	: {
                                'class'	: 'blue',
                                'action': function(){
                                    $.ajax({
                                        type: "POST",
                                        url: '<?php echo osc_base_url(true); ?>',
                                        dataType: 'json',
                                        data: {
                                            'page':'ajax',
                                            'action':'runhook',
                                            'hook':'adblock'
                                        },
                                        success: function(data) {}
                                    });

                                    return;
                                }
                            }
                        }
                    });
                }
            });
        </script>

    <?php
    }

    function adblock_set_cookie() {
        Session::newInstance()->_set('adblock_time', time());
    }

    osc_register_plugin(osc_plugin_path(__FILE__), '');

    //Show the message every 5 minutes (300 seconds)
    if(time()-Session::newInstance()->_get('adblock_time')>=300) {
        osc_add_hook('before_html', 'adblock_load_files');
        osc_add_hook('footer', 'adblock_deductor');
        osc_add_hook('ajax_adblock', 'adblock_set_cookie');
    }



?>