<?php
/**
 * Plugin Name: SaveYourInternet Protest Page
 */

include( plugin_dir_path( __FILE__ ) . 'defaults.php');
include( plugin_dir_path( __FILE__ ) . 'options.php');

if ( !class_exists( 'SYIAD' ) ) {
    class SYIAD
    {

        private $options;
        
        /**
         * Start up
         */
        public function __construct()
        {
            add_action('init', array($this, 'init'));
        }

        function init() {
            $this->options = get_option( 'syiad_option' );
            if ((true === $this->options['general']['enable']) || (true === $this->options['debug']['testmode'])) {
                add_action( "wp_footer", array($this, "show_off_page") );
            }
            if(!session_id())
                session_start();
        }
        
        function show_off_page() {
            if ((true !== $this->options['debug']['testmode']) && (current_time("Y-m-d") != $this->options['general']['date'])) {
                return;
            }
            
            if (isset($this->options['general']['exclude'])) {
                $excludeids = explode(',', $this->options['general']['exclude']);
                if (in_array(get_the_id(), $excludeids)) return;
            }
           
            if (true !== $this->options['general']['notclosable']) {
                if ($_REQUEST['SYIAD_disable'] == "true") {
                    $_SESSION['SYIAD_disable'] = true;
                }
                if (true === $_SESSION['SYIAD_disable']) {
                    return;
                }
            }
                
            wp_enqueue_style( 'SYIAD', plugins_url('/css/syiad.css', __FILE__ ) );
            
            echo '<div id="syiad"><div id="syiad_box">';
            
            if (true !== $this->options['general']['notclosable']) {
                echo '<a title="Schließen" id="syiad_closelink" href="'.esc_url( add_query_arg( 'SYIAD_disable', 'true' ) ).'">&times;</a>';
            }
            
            echo '<a id="syiad_headline" href="'.(isset($this->options['format']['link']) ? $this->options['format']['link'] : SYIAD_DEFAULTLINK).'"><img src="'.plugins_url('/images/headline.png', __FILE__ ).'" alt="SaveYourInternet"></a>';
            echo ((isset($this->options['format']['customtext'], $this->options['format']['text']) &&  (true === $this->options['format']['customtext'])) ? $this->options['format']['text'] : SYIAD_DEFAULTTEXT);
            if (!isset($this->options['format']['showlogo']) || (true === $this->options['format']['showlogo'])) {
                echo '<p><a id="syiad_logo" href="'.(isset($this->options['format']['link']) ? $this->options['format']['link'] : SYIAD_DEFAULTLINK).'"><img src="'.plugins_url('/images/logo.png', __FILE__ ).'" alt="Piratenpartei"></a></p>';
            }
            echo '</div></div>';
        }
        
    }
}

$syiad_instance = new SYIAD();