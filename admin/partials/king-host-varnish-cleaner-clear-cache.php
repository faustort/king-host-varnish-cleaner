<?php

/**
 * @since      1.0.0
 *
 * @package    King_Host_Varnish_Cleaner
 * @subpackage King_Host_Varnish_Cleaner/admin/partials
 */

// Get King Host base URL 
$url_request = $this->king_host_url_api;

// Get the API key
$king_host_varnish_api_key = get_option('king_host_varnish_api_key');

// Get the path do clear (future implement)
$uri = $this->get_path_to_clean();
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <h2><?php
        _e('API Key', 'king-host-varnish-cleaner');
        echo " : " . $king_host_varnish_api_key;
        ?></h2>
    <?php

    //$uri = "*";
    $url_complete = $url_request . "?acao=purge_cache&hash=" . $king_host_varnish_api_key . "&uri=" . $uri;
    $request = wp_remote_get($url_complete);
    if (is_wp_error($request)) {
        _e('Wordpress is having difficult to access the K.H. Server', 'king-host-varnish-cleaner');
    }
    $body = wp_remote_retrieve_body($request);
    $data = json_decode($body);

    if (!empty($data)) {
        if (isset($data->status) && $data->status == "Erro") {
            _e("Something went wrong with server, this is the message returned:", 'king-host-varnish-cleaner');
            echo "<strong> " . $data->mensagem . "</strong>";
        } else {
            echo '<ul>';
            foreach ($data as $info) {
                echo '<li>';
                echo $info->dominio . " : " . $info->status;
                echo '</li>';
            }
            echo '</ul>';
        }
    }
    ?>
</div>