<?php
/**
 * Plugin Name: ACFist
 * Description: Empower ACF with more features.
 * Version: 1.0.1
 * Author: WPizard
 * Text Domain: acfist
 */

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'ACFist' ) ) {

    class ACFist {

        private static $version;

        private static $plugin_basename;

        private static $plugin_name;

        private static $plugin_slug;

        private static $plugin_dir;

        private static $plugin_url;

        public function __construct() {
            $this->define_constants();
            $this->add_actions();
        }

        protected function define_constants() {
            $plugin_data = get_file_data( __FILE__, [ 'Plugin Name', 'Version' ], 'acfist' );

            self::$plugin_basename = plugin_basename( __FILE__ );
            self::$plugin_name = array_shift( $plugin_data );
            self::$plugin_slug = strtolower( self::$plugin_name );
            self::$version = array_shift( $plugin_data );
            self::$plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
            self::$plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
        }

        protected function add_actions() {
            add_action( 'plugins_loaded', [ $this, 'init' ] );
        }

        public function init() {
            if ( ! $this->is_acf_active() ) {
                add_action( "after_plugin_row_{$this->plugin_basename()}", [ $this, 'add_plugin_notice' ] );

                return;
            }

            load_plugin_textdomain( 'acfist', false, $this->plugin_dir() . '/languages' );

            add_action( 'admin_init', [ $this, 'admin_init' ] );

            do_action( 'acfist_init', $this );
        }

        public function admin_init() {
            $this->load_files( [
                'admin-column/class',
            ] );
        }

        public function add_plugin_notice() {
            ?>
            <style>
                .plugins tr[data-slug="<?php echo $this->plugin_slug(); ?>"] th,
                .plugins tr[data-slug="<?php echo $this->plugin_slug(); ?>"] td {
                    box-shadow: none;
                    padding-bottom: 5px;
                }
            </style>
            <tr class="plugin-update-tr active" data-slug="<?php echo self::$plugin_slug; ?>" data-plugin="<?php echo self::$plugin_basename; ?>">
                <td colspan="4" class="plugin-update colspanchange">
                    <div class="update-message notice inline notice-error notice-alt">
                        <p>ACFist requires <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields</a> v5.9.0+</p>
                    </div>
                </td>
            </tr>
            <?php
        }

        public function is_acf_active() {
            if ( empty( defined( 'ACF_VERSION' ) ) || version_compare( ACF_VERSION, '5.9.0', '<' ) ) {
                return false;
            }

            return true;
        }

        public function version() {
            return self::$version;
        }

        public function plugin_basename() {
            return self::$plugin_basename;
        }

        public function plugin_slug() {
            return self::$plugin_slug;
        }

        public function plugin_name() {
            return self::$plugin_name;
        }

        public function plugin_dir() {
            $plugin_dir = apply_filters( 'acfist_plugin_dir', self::$plugin_dir );

            return $plugin_dir;
        }

        public function plugin_url() {
            $plugin_url = apply_filters( 'acfist_plugin_url', self::$plugin_url );

            return $plugin_url;
        }

        public function load_directory( $directory_name ) {
            $path = trailingslashit( $this->plugin_dir() . 'includes/' . $directory_name );
            $file_names = glob( $path . '*.php' );

            foreach ( $file_names as $filename ) {
                if ( file_exists( $filename ) ) {
                    require_once $filename;
                }
            }
        }

        public function load_files( $file_names = array() ) {
            foreach ( $file_names as $file_name ) {
                $this->load_file( $file_name );
            }
        }

        public function load_file( $file_name = '' ) {
            if ( file_exists( $path = $this->plugin_dir() . 'includes/' . $file_name . '.php' ) ) {
                require_once $path;
            }
        }
    }
}

function acfist() {
    return new ACFist();
}

acfist();
