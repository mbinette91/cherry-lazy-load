<?php
/*
  Plugin Name: Cherry Lazy Load Boxes Plugin
  Version: 1.0
  Plugin URI: http://www.cherryframework.com/
  Description: Create blocks with lazy load effect
  Author: Cherry Team.
  Author URI: http://www.cherryframework.com/
  Text Domain: cherry-lazy-load
  Domain Path: languages/
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) )
exit;

class cherry_lazy_load {

  public $version = '1.0';

  function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
    add_filter( 'cherry_plugin_shortcode_output', array( $this, 'add_lazy_load_wrap' ), 9, 3 );
    add_shortcode( 'lazy_load_box', array( $this, 'lazy_load_shortcode' ) );
  }

  function assets() {
    if ( is_singular() ) {
      wp_enqueue_script( 'cherry-lazy-load', $this->url('js/cherry.lazy-load.js'), array('jquery'), $this->version, true );
      wp_enqueue_script('device-check', $this->url('js/device.min.js'), array('jquery'), '1.0.0', true );
      wp_enqueue_style( 'cherry-lazy-load', $this->url('css/lazy-load.css'), '', $this->version );
    }
  }

  /**
   * return plugin url
   */
  function url( $path = null ) {
    $base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
    if ( !$path ) {
      return $base_url;
    } else {
      return esc_url( $base_url . '/' . $path );
    }
  }

  /**
   * return plugin dir
   */
  function dir( $path = null ) {
    $base_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
    if ( !$path ) {
      return $base_dir;
    } else {
      return esc_url( $base_dir . '/' . $path );
    }
  }

  /**
   * Wraps to the Lazy Load container.
   *
   * @param string $output        Returned shortcode's HTML.
   * @param array  $atts          Shortcode attributes.
   * @param string $shortcodename Shortcode name.
   */
  public function add_lazy_load_wrap( $output, $atts, $shortcodename ) {

    if ( empty( $atts['custom_class'] ) ) {
      return $output;
    }

    $custom_classes = explode( ' ', trim( $atts['custom_class'] ) );

    if ( !is_array( $custom_classes ) || empty( $custom_classes ) ) {
      return $output;
    }

    /**
     * Filters the default array of Lazy Load parameters.
     *
     * @param array.
     */
    $lazy_params = apply_filters( 'cherry_lazy_params', array(
      'delay'  => '0',
      'speed'  => '600',
      ) );

    $effect_class = '';

    foreach ( $custom_classes as $class ) {

      if ( false === strpos( sanitize_html_class( $class ), 'effect-' ) ) {
        continue;
      }

      $effect_class = $class;

    }

    if ( empty( $effect_class ) ) {
      return $output;
    }

    $default_css = '-webkit-transition: all ' . $lazy_params['speed'] . 'ms ease; -moz-transition: all ' . $lazy_params['speed'] . 'ms ease; -ms-transition: all ' . $lazy_params['speed'] . 'ms ease; -o-transition: all ' . $lazy_params['speed'] . 'ms ease; transition: all ' . $lazy_params['speed'] . 'ms ease;';
    $default_css = apply_filters( 'cherry_add_lazy_load_default_css', $default_css, $shortcodename );


    $output = '<div class="lazy-load-box trigger ' . esc_attr( $effect_class ) . '" data-delay="' . $lazy_params['delay'] . '" data-speed="' . $lazy_params['speed'] . '" style="' . $default_css . '">' . $output . '</div>';

    return $output;
  }

  /**
   * Shortcode
   */
  function lazy_load_shortcode( $atts, $content = null ) {
    extract(shortcode_atts( array(
        'effect'       => 'fade',
        'delay'        => '0',
        'speed'        => '600',
        'custom_class' => ''
      ),
      $atts,
      'lazy_load_box'
    ));

    $default_css = '-webkit-transition: all ' . $speed . 'ms ease; -moz-transition: all ' . $speed . 'ms ease; -ms-transition: all ' . $speed . 'ms ease; -o-transition: all ' . $speed . 'ms ease; transition: all ' . $speed . 'ms ease;';
    $default_css = apply_filters( 'cherry_lazy_load_default_css', $default_css );
    $result = '<section class="lazy-load-box trigger effect-'  . esc_attr( $effect ) . ' ' . esc_attr( $custom_class ) . '" data-delay="' . $delay . '" data-speed="' . $speed . '" style="' . $default_css . '">';
      $result .= do_shortcode( $content );
    $result .= '</section>';

    $result = apply_filters( 'cherry_plugin_shortcode_output', $result, $atts, 'lazy_load_box' );

    return $result;
  }

}

global $cherry_lazy_load;
if ( !isset($cherry_lazy_load) )
    $cherry_lazy_load = new cherry_lazy_load();

?>