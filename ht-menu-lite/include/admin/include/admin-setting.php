<?php
namespace HtMenu\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class HTMega_Menu_Admin_Settings {

    private $settings_api;

    function __construct() {
        $this->settings_api = new \HTMega_Menu_Settings_API();

        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 220 );
        add_action( 'wsa_form_bottom_htmegamenu_general_tabs', array( $this, 'htmega_menu_html_general_tabs' ) );
        $this->plugin_recommendations();
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->htmega_menu_admin_get_settings_sections() );
        $this->settings_api->set_fields( $this->htmega_menu_admin_fields_settings() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    // Plugins menu Register
    function admin_menu() {
        add_menu_page( 
            __( 'HT Menu', 'htmega-menu' ),
            __( 'HT Menu', 'htmega-menu' ),
            'manage_options',
            'htmegamenu',
            array ( $this, 'plugin_page' ),
            'dashicons-welcome-widgets-menus',
            100
        );
    }

    /**
     * [plugin_recommendations]
     * @return [void]
     */
    public function plugin_recommendations(){

        $get_instance = Recommended_Plugins::instance( 
            array( 
                'text_domain'       => 'htmega-menu', 
                'parent_menu_slug'  => 'htmegamenu', 
                'menu_capability'   => 'manage_options', 
                'menu_page_slug'    => 'htmenu-recommendations',
                'priority'          => 226,
                'assets_url'        => HTMEGA_MENU_PL_URL.'include/admin/assets',
                'hook_suffix'       => 'ht-menu_page_htmenu-recommendations'
            )
        );

        // Tab titles/names use esc_html__() — build them on init (priority 20, after this
        // plugin's own i18n() on init@10 loads the text domain) instead of at construction
        // time, which runs on plugins_loaded, before any text domain is loaded.
        add_action( 'init', function() use ( $get_instance ) {

        // Only recommend the WooCommerce-only builder (ShopLentor) when WooCommerce is installed.
        $woocommerce_active = class_exists( 'WooCommerce' );

        $get_instance->add_new_tab( array(
            'title' => esc_html__( 'Recommended Plugins', 'htmegamenu' ),
            'active' => true,
            'plugins' => array_merge(
                $woocommerce_active ? array(
                    array(
                        'slug'      => 'woolentor-addons',
                        'location'  => 'woolentor_addons_elementor.php',
                        'name'      => esc_html__( 'ShopLentor – All-in-One WooCommerce Growth & Store Enhancement Plugin', 'htmegamenu' )
                    ),
                ) : array(),
                array(
                    array(
                        'slug'      => 'ht-mega-for-elementor',
                        'location'  => 'htmega_addons_elementor.php',
                        'name'      => esc_html__( 'HT Mega – Absolute Addons for Elementor Page Builder', 'htmegamenu' )
                    ),
                    array(
                        'slug'      => 'kelune-crm',
                        'location'  => 'kelune-crm.php',
                        'name'      => esc_html__( 'Kelune CRM – Contact Management, Email Marketing, Newsletter & Marketing Automation', 'htmegamenu' )
                    ),
                    array(
                        'slug'      => 'support-genix-lite',
                        'location'  => 'support-genix-lite.php',
                        'name'      => esc_html__( 'Support Genix – Helpdesk, AI Chatbot, Knowledge Base & Customer Support Ticketing System', 'htmegamenu' )
                    ),
                    array(
                        'slug'      => 'hashbar-wp-notification-bar',
                        'location'  => 'init.php',
                        'name'      => esc_html__( 'Notification Bar for WordPress', 'htmegamenu' )
                    ),
                    array(
                        'slug'      => 'wp-plugin-manager',
                        'location'  => 'plugin-main.php',
                        'name'      => esc_html__( 'WP Plugin Manager', 'htmegamenu' )
                    ),
                    array(
                        'slug'      => 'cookieray',
                        'location'  => 'cookieray.php',
                        'name'      => esc_html__( 'CookieRay – Cookie Banner for Cookie Consent (GDPR/CCPA Compliant)', 'htmegamenu' )
                    ),
                    array(
                        'slug'      => 'pixelavo',
                        'location'  => 'pixelavo.php',
                        'name'      => esc_html__( 'Pixelavo – Server Side Tracking & Pixel + AI Ads Tools', 'htmegamenu' )
                    ),
                )
            )
        ) );

        $get_instance->add_new_tab( array(
            'title' => esc_html__( 'WooCommerce', 'htmegamenu' ),
            'plugins' => array(
                array(
                    'slug'      => 'woolentor-addons',
                    'location'  => 'woolentor_addons_elementor.php',
                    'name'      => esc_html__( 'ShopLentor – All-in-One WooCommerce Growth & Store Enhancement Plugin', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'whols',
                    'location'  => 'whols.php',
                    'name'      => esc_html__( 'Whols – Wholesale Prices and B2B Store Solution for WooCommerce', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'swatchly',
                    'location'  => 'swatchly.php',
                    'name'      => esc_html__( 'Swatchly – Product Variation Swatches for WooCommerce', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'recurio',
                    'location'  => 'recurio.php',
                    'name'      => esc_html__( 'Recurio – Ultimate Subscription for WooCommerce', 'htmegamenu' )
                ),
            )
        ) );

        $get_instance->add_new_tab( array(
            'title' => esc_html__( 'Other Plugins', 'htmegamenu' ),
            'plugins' => array(
                array(
                    'slug'      => 'wp-plugin-manager',
                    'location'  => 'plugin-main.php',
                    'name'      => esc_html__( 'WP Plugin Manager', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'ht-easy-google-analytics',
                    'location'  => 'ht-easy-google-analytics.php',
                    'name'      => esc_html__( 'HT Easy GA4 ( Google Analytics 4 )', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'ht-contactform',
                    'location'  => 'contact-form-widget-elementor.php',
                    'name'      => esc_html__( 'HT Contact Form – Drag & Drop Form Builder for WordPress', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'cookieray',
                    'location'  => 'cookieray.php',
                    'name'      => esc_html__( 'CookieRay – Cookie Banner for Cookie Consent (GDPR/CCPA Compliant)', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'insert-headers-and-footers-script',
                    'location'  => 'init.php',
                    'name'      => esc_html__( 'Insert Headers and Footers Code', 'htmegamenu' )
                ),
                array(
                    'slug'      => 'courseglade-lms',
                    'location'  => 'courseglade-lms.php',
                    'name'      => esc_html__( 'CourseGlade LMS – Online Course & eLearning Platform', 'htmegamenu' )
                ),
            )
        ) );

        }, 20 );

    }

    // Options page Section register
    function htmega_menu_admin_get_settings_sections() {
        $sections = array(
            
            array(
                'id'    => 'htmegamenu_general_tabs',
                'title' => esc_html__( 'General', 'htmega-menu' )
            ),

            array(
                'id'    => 'htmegamenu_style_tabs',
                'title' => esc_html__( 'Style', 'htmega-menu' )
            ),

        );
        return $sections;
    }

    // Options page field register
    protected function htmega_menu_admin_fields_settings() {

        $settings_fields = array(

            'htmegamenu_general_tabs' => array(),
            
            'htmegamenu_style_tabs' => array(

                array(
                    'name'  => 'menu_items_color',
                    'label' => __( 'Menu Items Color', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Menu Items color.', 'htmega-menu' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'menu_items_hover_color',
                    'label' => __( 'Menu Hover & Active Color', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Menu Items Hover color.', 'htmega-menu' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'sub_menu_width',
                    'label' => __( 'Sub Menu Width', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Sub Menu Width.', 'htmega-menu' ),
                    'min'               => 0,
                    'max'               => 1000,
                    'step'              => '1',
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ),

                array(
                    'name'  => 'sub_menu_bg_color',
                    'label' => __( 'Sub Menu Background Color', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Menu Background Color.', 'htmega-menu' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'sub_menu_items_color',
                    'label' => __( 'Sub Menu Items Color', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Sub Menu Items Color.', 'htmega-menu' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'sub_menu_items_hover_color',
                    'label' => __( 'Sub Menu Items Hover Color', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Sub Menu Items Hover Color.', 'htmega-menu' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'mega_menu_width',
                    'label' => __( 'Mega Menu Width', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Mega Menu Width.', 'htmega-menu' ),
                    'min'               => 0,
                    'max'               => 1500,
                    'step'              => '1',
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ),

                array(
                    'name'  => 'mega_menu_bg_color',
                    'label' => __( 'Mega Menu Background Color', 'htmega-menu' ),
                    'desc' => wp_kses_post( 'Mega Menu Background Color.', 'htmega-menu' ),
                    'type' => 'color',
                ),

            ),

        );
        
        return array_merge( $settings_fields );
    }


    function plugin_page() {

        echo '<div class="wrap">';
            echo '<h2>'.esc_html__( 'HT Menu Settings','htmega-menu' ).'</h2>';
            $this->save_message();
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();
        echo '</div>';

    }
    function save_message() {
        if( isset($_GET['settings-updated']) ) { ?>
            <div class="updated notice is-dismissible"> 
                <p><strong><?php esc_html_e('Successfully Settings Saved.', 'htmega-menu') ?></strong></p>
            </div>
            <?php
        }
    }

    // General tab
    function htmega_menu_html_general_tabs(){
        ob_start();
        ?>
            <div class="htmegamenu-general-tabs">

                <div class="htmegamenu-document-section">
                    <div class="htmegamenu-column">
                        <a href="https://www.youtube.com/watch?v=oAND7tZFidI" target="_blank">
                            <img src="<?php echo HTMEGA_MENU_PL_URL; ?>include/admin/assets/images/video-tutorial.jpg" alt="<?php esc_attr_e( 'Video Tutorial', 'htmega-menu' ); ?>">
                        </a>
                    </div>
                    <div class="htmegamenu-column">
                        <a href="https://demo.hasthemes.com/doc/ht-menu/index.html" target="_blank">
                            <img src="<?php echo HTMEGA_MENU_PL_URL; ?>include/admin/assets/images/online-documentation.jpg" alt="<?php esc_attr_e( 'Online Documentation', 'htmega-menu' ); ?>">
                        </a>
                    </div>
                    <div class="htmegamenu-column">
                        <a href="https://hasthemes.com/contact-us/" target="_blank">
                            <img src="<?php echo HTMEGA_MENU_PL_URL; ?>include/admin/assets/images/genral-contact-us.jpg" alt="<?php esc_attr_e( 'Contact Us', 'htmega-menu' ); ?>">
                        </a>
                    </div>
                </div>

                <div class="menudifferent-pro-free">
                    <h3 class="htmegamenu-section-title"><?php echo esc_html__( 'HT Menu Free VS HT Menu Pro.', 'htmega-menu' ); ?></h3>

                    <div class="htmegamenu-admin-row">
                        <div class="features-list-area">
                            <h3><?php echo esc_html__( 'HT Menu Free', 'htmega-menu' ); ?></h3>
                            <ul>
                                <li><?php echo esc_html__( 'Menu Template Option', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Individual Menu Width Control Option', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Sub Menu Position', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( '5 Menu Layouts', 'htmega-menu' ); ?></li>
                                <li class="htdel"><del><?php echo esc_html__( 'Menu Icon Picker', 'htmega-menu' ); ?></del></li>
                                <li class="htdel"><del><?php echo esc_html__( 'Menu Icon Color', 'htmega-menu' ); ?></del></li>
                                <li class="htdel"><del><?php echo esc_html__( 'Menu Badge', 'htmega-menu' ); ?></del></li>
                                <li class="htdel"><del><?php echo esc_html__( 'Menu Badge Color', 'htmega-menu' ); ?></del></li>
                                <li class="htdel"><del><?php echo esc_html__( 'Menu Badge Background Color', 'htmega-menu' ); ?></del></li>
                            </ul>
                            <a class="button button-primary" href="<?php echo esc_url( admin_url() ); ?>plugin-install.php" target="_blank"><?php echo esc_html__( 'Install Now', 'htmega-menu' ); ?></a>
                        </div>
                        <div class="features-list-area">
                            <h3><?php echo esc_html__( 'HT Menu Pro', 'htmega-menu' ); ?></h3>
                            <ul>
                                <li><?php echo esc_html__( 'Menu Template Option', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Individual Menu Width Control Option', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Sub Menu Position', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( '10 Menu Layouts', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Menu Icon Picker', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Menu Icon Color', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Menu Badge', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Menu Badge Color', 'htmega-menu' ); ?></li>
                                <li><?php echo esc_html__( 'Menu Badge Background Color', 'htmega-menu' ); ?></li>
                            </ul>
                            <a class="button button-primary" href="https://hasthemes.com/ht-mega-menu-for-elementor-page-builder/" target="_blank"><?php echo esc_html__( 'Buy Now', 'htmega-menu' ); ?></a>
                        </div>
                    </div>

                </div>

            </div>
        <?php
        echo ob_get_clean();
    }
    
}

new HTMega_Menu_Admin_Settings();