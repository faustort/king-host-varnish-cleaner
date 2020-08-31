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

    public function addPluginAdminMenu()
    {
        add_submenu_page(
            'options-general.php',
            __('King Host Varnish Settings', 'king-host-varnish-cleaner'),
            __('King Host Varnish', 'king-host-varnish-cleaner'),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'displayPluginAdminSettings')
        );
        add_submenu_page(
            $this->plugin_name . '-settings',
            __('Clear Varnish Cache', 'king-host-varnish-cleaner'),
            __('Clear Varnish Cache', 'king-host-varnish-cleaner'),
            'manage_options',
            $this->plugin_name . '-clear',
            array($this, 'displayClearVarnishCachePage')
        );
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function displayPluginAdminDashboard()
    {
        require_once 'partials/' . $this->plugin_name . '-admin-display.php';
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function displayClearVarnishCachePage()
    {
        include(plugin_dir_path(__FILE__) . 'partials/king-host-varnish-cleaner-clear-cache.php');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function displayPluginAdminSettings()
    {

        if (isset($_GET['error_message'])) {
            add_action('admin_notices', array($this, 'settingsPageSettingsMessages'));
            do_action('admin_notices', $_GET['error_message']);
        }
        include(plugin_dir_path(__FILE__) . 'partials/king-host-varnish-cleaner-admin-display.php');
    }

    /**
     * Undocumented function
     *
     * @param [type] $error_message
     * @return void
     */
    public function settingsPageSettingsMessages($error_message)
    {
        switch ($error_message) {
            case '1':
                $message = __('There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'king-host-varnish-cleaner');
                $err_code = esc_attr('settings_page_king_host_api_key');
                $setting_field = 'settings_page_king_host_api_key';
                break;
        }
        $type = 'error';
        add_settings_error(
            $setting_field,
            $err_code,
            $message,
            $type
        );
    }

    public function registerAndBuildFields()
    {
        /**
         * First, we add_settings_section. This is necessary since all future settings must belong to one.
         * Second, add_settings_field
         * Third, register_setting
         */
        add_settings_section(
            // ID used to identify this section and with which to register options
            'settings_page_general_section',
            // Title to be displayed on the administration page
            '',
            // Callback used to render the description of the section
            array($this, 'settings_page_display_general_account'),
            // Page on which to add this section of options
            'settings_page_general_settings'
        );
        unset($args);

        $args = array(
            'type'                  => 'input',
            'subtype'               => 'text',
            'id'                    => 'settings_page_king_host_api_key',
            'name'                  => 'settings_page_king_host_api_key',
            'required'              => 'true',
            'get_options_list'      => '',
            'value_type'            => 'normal',
            'wp_data'               => 'option'
        );

        add_settings_field(
            'settings_page_king_host_api_key',
            __('King Host API Key', 'king-host-varnish-cleaner'),
            array($this, 'settings_page_render_settings_field'),
            'settings_page_general_settings',
            'settings_page_general_section',
            $args
        );

        register_setting(
            'settings_page_general_settings',
            'settings_page_king_host_api_key'
        );
    }
    public function settings_page_display_general_account()
    {

        echo sprintf(
            '<div><p>%s</p><p>%s</p></div>',
            __('You need to specify the King Host Varnish API.', 'king-host-varnish-cleaner'),
            __("If you don't know yours API key you can ") .
                sprintf(
                    "<a target='_blank' href='https://king.host/wiki/artigo/varnish-cache/'>%s.</a>",
                    __("find more about here")
                )
        );
        
        $url_clear_cache = admin_url('admin.php?page=' . $this->plugin_name . '-clear');
        
        echo sprintf('<a class="button button-secondary" href="' . $url_clear_cache . '">%s</a>', __("Clear cache now", 'king-host-varnish-cleaner'));
        echo "<hr>";
    }
    public function settings_page_render_settings_field($args)
    {
        if ($args['wp_data'] == 'option') {
            $wp_data_value = get_option($args['name']);
        } elseif ($args['wp_data'] == 'post_meta') {
            $wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
        }

        switch ($args['type']) {

            case 'input':
                $value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
                if ($args['subtype'] != 'checkbox') {
                    $prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
                    $prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
                    $step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
                    $min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
                    $max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
                    if (isset($args['disabled'])) {
                        // hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
                        echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    } else {
                        echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    }
                } else {
                    $checked = ($value) ? 'checked' : '';
                    echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
                }
                break;
            default:
                # code...
                break;
        }
    }

    function add_toolbar_items($admin_bar)
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $admin_bar->add_menu(array(
            'id'    => 'menu-id',
            'parent' => null,
            'group'  => null,
            'title' => 'Clear Varnish', //you can use img tag with image link. it will show the image icon Instead of the title.
            'href'  => admin_url('admin.php?page=' . $this->plugin_name . '-clear'),
            'meta' => [
                'title' => __('Clear Varnish', 'king-host-varnish-cleaner'), //This title will show on hover
            ]
        ));
    }
}
