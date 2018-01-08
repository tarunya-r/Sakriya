<?php
$feature_list = ( empty($this->cc->feature_list) ? null : $this->cc->feature_list );
$dplugin_link = 'http://berocket.com/product/' . $this->cc->values['premium_slug'];
$dplugin_lic  = 35;

if ( ! empty( $feature_list ) && count( $feature_list ) > 0 ) { ?>
    <div class="paid_features">
        <?php
        $feature_text = '';

        foreach ( $feature_list as $feature ) {
            $feature_text .= '<li>' . $feature . '</li>';
        }

        $text = '<h3>Unlock all the features with Premium version!</h3>
            <div>
            <ul>
                %feature_list%
            </ul>
            </div>
            <div class="premium_buttons">
                <span>Read more about</span>
                <a class="get_premium_version" href="%link%" target="_blank">PREMIUM VERSION</a>
                <span class="divider">OR</span>
                <a class="buy_premium_version" href="http://berocket.com/checkout/%licence%/promo/SAVE15" target="_blank">BUY NOW</a>
                <span>and get <b>15% discount</b></span>
            </div>
            <p>Support the plugin by purchasing paid version. This will provide faster growth, better support and
            much more functionality for the plugin!</p>';
        $text = str_replace( '%feature_list%', $feature_text,                                                       $text );
        $text = str_replace( '%link%',         $dplugin_link,                                                       $text );
        $text = str_replace( '%licence%',      $dplugin_lic,                                                        $text );
        $text = str_replace( '%plugin_name%',  (empty($plugin_info['Name']) ? '' : $plugin_info['Name']),           $text );
        $text = str_replace( '%plugin_link%',  (empty($plugin_info['PluginURI']) ? '' : $plugin_info['PluginURI']), $text );
        echo $text;
        ?>
    </div>
    <?php
    $subscribed = get_option('berocket_email_subscribed');
    if( ! $subscribed ) {
        $user_email = wp_get_current_user();
        if( isset($user_email->user_email) ) {
            $user_email = $user_email->user_email;
        } else {
            $user_email = '';
        }
        ?>
        <div class="berocket_subscribe berocket_subscribe_form" method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
            <h3>OUR NEWSLETTER</h3>
            <p>Get awesome content delivered straight to your inbox.</p>
            <input type="hidden" name="action" value="berocket_subscribe_email">
            <input class="berocket_subscribe_email" type="email" name="email" placeholder="Enter your email address" value="<?php echo $user_email; ?>">
            <p class="error" style="display: none;">Incorrect EMail. Please check it and try again</p>
            <input type="submit" class="button-primary button berocket_notice_submit" value="GET UPDATE">
        </div>
        <?php
        berocket_admin_notices::echo_jquery_functions();
    }
}
