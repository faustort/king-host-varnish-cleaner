<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.nw2web.com.br
 * @since      1.0.0
 *
 * @package    King_Host_Varnish_Cleaner
 * @subpackage King_Host_Varnish_Cleaner/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    King_Host_Varnish_Cleaner
 * @subpackage King_Host_Varnish_Cleaner/admin
 * @author     Fausto Rodrigo Toloi <fausto@nw2web.com.br>
 */
class King_Host_Varnish_Cleaner_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The URL to clear Varnish Cache on King Host
     * based on documentation not public provided
     *
     * @var string
     */
    public $king_host_url_api = 'https://painel.kinghost.com.br/conector/varnish.php';

    /**
     * Undocumented variable
     *
     * @var string
     */
    public $path_relative;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function khvc_display_clear_varnish()
    {
        include(plugin_dir_path(__FILE__) . 'partials/king-host-varnish-cleaner-clear-cache.php');
    }

    /**
     * Return only the parameter as relative path needed for
     * clear cache on King Host. Based on documentation.
     */
    public function get_path_to_clean($link_to_clear = false)
    {
        if (!$link_to_clear) {
            $this->path_to_clear = "/*";
            return $this->path_to_clear;
        }

        // Avoid wrong cleaning process on subdirectories wp installations
        $url_server =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $site_url = get_site_url();
        $site_root = str_replace($url_server, "", $site_url);
        $path_relative = str_replace($site_url, "", $link_to_clear);

        $this->path_to_clear = $site_root . $path_relative;
        return  $this->path_to_clear;
    }

    /**
     * Undocumented function
     *
     * @param [type] $admin_bar
     * @return void
     */
    public function khvc_register_toolbar_item($wp_admin_bar)
    {
        if (!current_user_can('edit_posts') && !is_admin()) {
            return;
        }
        $wp_admin_bar->add_menu(array(
            'id' => $this->plugin_name . '_toolbar_clear',
            'parent' => null,
            'group'  => null,
            'title' => __('Clear Varnish', 'king-host-varnish-cleaner'),
            'href'  => menu_page_url($this->plugin_name . '-clear', false),
            'meta' => [
                'title' => __('Clear Varnish', 'king-host-varnish-cleaner'),
            ]
        ));
    }

    /**
     * Register Wordpress fields for plugin
     *
     * @return void
     */
    public function khvc_register_settings()
    {
        // Register the API Key variable
        register_setting(
            $this->plugin_name . '_group_config',
            'king_host_varnish_api_key',
            array(
                'sanitize_callback' => function ($value) {
                    if (!preg_match('/^[a-zA-Z0-9]{30,32}$/', $value)) {
                        add_settings_error(
                            'king_host_varnish_api_key',
                            esc_attr('king_host_varnish_api_key_erro'),
                            __('Your API key is not valid', 'king-host-varnish-cleaner'),
                            'error'
                        );
                        return get_option('king_host_varnish_api_key');
                    }
                    return $value;
                },
            )
        );

        // Create section for edit API Section
        add_settings_section(
            $this->plugin_name . '_section',
            __('King Host API Key', 'king-host-varnish-cleaner'),
            function ($args) {
                _e('Enter your King Host API Key', 'king-host-varnish-cleaner');
            },
            $this->plugin_name . '_group_config'
        );

        // Add API field
        add_settings_field(
            'king_host_varnish_api_key',
            __('King Host API Key', 'king-host-varnish-cleaner'),
            function ($args) {
                $kgv_api_key = get_option('king_host_varnish_api_key');
?>
            <input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="king_host_varnish_api_key" value="<?php echo esc_attr($kgv_api_key); ?>">
        <?php
            },
            $this->plugin_name . '_group_config',
            $this->plugin_name . '_section',
            [
                'label_for' => 'king_host_varnish_api_key_id',
                'class'     => 'king_host_varnish-class',
            ]
        );
    }

    /**
     * Register admin menus
     *
     * @return void
     */
    public function khvc_register_admin_menu()
    {
        add_options_page(
            __('King Host Varnish Settings', 'king-host-varnish-cleaner'),
            __('K.H. Varnish config.', 'king-host-varnish-cleaner'),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'khvc_display_settings_html')
        );
        add_options_page(
            __('Clear Varnish Cache', 'king-host-varnish-cleaner'),
            __('K.H. Clear Varnish.', 'king-host-varnish-cleaner'),
            'edit_posts',
            $this->plugin_name . '-clear',
            array($this, 'khvc_display_clear_varnish')
        );

        // Fix call menu_page_url too soon
        add_action('admin_bar_menu', array($this, 'khvc_register_toolbar_item'), 80);
    }

    /**
     * Display the plugin settings page
     *
     * @return void
     */
    public function khvc_display_settings_html()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields($this->plugin_name . '_group_config');
                do_settings_sections($this->plugin_name . '_group_config');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    /**
     * Create plugin action link to settings page
     *
     * @param [type] $links
     * @return void
     */
    public function khvc_create_plugin_action_link($links)
    {
        if (is_admin()) {
            // add settings button
            $settings_link = menu_page_url($this->plugin_name . '-settings', false);
            $settings_link_complete = '<a href="' . $settings_link . '">' . __('Settings', 'king-host-varnish-cleaner') . '</a>';

            array_unshift($links, $settings_link_complete);

            // add clear cache button
            $clear_link = menu_page_url($this->plugin_name . '-clear', false);
            $clear_link_complete = '<a href="' . $clear_link . '">' . __('Clear Varnish', 'king-host-varnish-cleaner') . '</a>';

            array_unshift($links, $clear_link_complete);
            return $links;
        }
    }
}
