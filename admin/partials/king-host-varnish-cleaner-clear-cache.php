<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.nw2web.com.br
 * @since      1.0.0
 *
 * @package    King_Host_Varnish_Cleaner
 * @subpackage King_Host_Varnish_Cleaner/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php _e('Clear cache', 'king-host-varnish-cleaner'); ?></h2>
    
    <?php 
    
    echo $king_host_api_key = get_option('settings_page_king_host_api_key');
    
    /**
     * Thanks to @mymotherland 
     * Get from here: https://stackoverflow.com/questions/6516902/how-to-get-response-using-curl-in-php
     */
    $response = get_web_page('https://painel.kinghost.com.br/conector/varnish.php?acao=purge_cache&hash='.$king_host_api_key.'&uri=/*');
    $resArr = array();
    $resArr = json_decode($response);
    echo "<pre>";
    print_r($resArr);
    echo "</pre>";

    function get_web_page($url) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_USERAGENT      => "test", // name of client
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,    // time-out on response
        ); 
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content  = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
    
    ?>
</div>