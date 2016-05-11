<?php
/**
 * Plugin Name: AMP Google Analytics
 * Plugin URI: https://github.com/DenverDias/wp-amp-google-analytics
 * Description: An extension to AMP for WordPress that enables tracking pageviews on Google Analytics.
 * Author:      Denver
 * Author URI:  https://denverdias.com/
 * Version:     1.0
 * License:     GPLv2 or later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     AMP
 * @subpackage  Google Analytics
 * @author      Denver Dias
 * @version     1.0
 */

add_action( 'init', array( 'AMP_Google_Analytics', 'initialise' ) );
class AMP_Google_Analytics {

  function __construct() {
    add_action( 'amp_post_template_analytics', array( $this, 'add_analytics' ) );
    add_action( 'admin_menu', array( $this, 'add_analytics_options_page' ) );
    add_action( 'admin_init', array( $this, 'amp_analytics_register_settings' ) );
  }

  /**
   * Initialisation callback for AMP Google Analytics
   *
   * @since 1.0
   */
  public static function initialise() {
    static $instance;

    if ( ! $instance ) {
      $instance = new AMP_Google_Analytics();
    }
  }

  /**
   * Insert Google Analytics data for processing by the main AMP plugin
   *
   * @since 1.0
   */
  public function add_analytics( $array ) {
  	$options = get_option( 'amp_google_analytics' );

  	if ( isset( $options['tracking_id'] ) && '' !== $options['tracking_id'] ) {

      $array[ 'google-analytics' ] = array(
        'type' => 'googleanalytics',
        'attributes' => array(),
        'config_data' => array(
          'vars' => array(
            'account' => esc_js( $options['tracking_id'] )
          ),
          'triggers' => array(
            'trackPageView' => array(
              'on' => 'visible',
              'request' => 'pageview'
            )
          )
        )
      );
  	}
    return $array;
  }

  /**
   * Create Google Analytics option page
   *
   * @since 1.0
   */
  public function add_analytics_options_page() {
  	add_options_page( 'AMP Analytics', 'AMP Analytics', 'manage_options', 'amp-google-analytics', array( $this, 'options_page_contents' ) );
  }

  /**
   * Register all analytics related settings
   *
   * @since 1.0
   */
  public function amp_analytics_register_settings() {
  	add_settings_section( 'amp_analytics_google_analytics', 'Google Analytics Identifier', array( $this, 'section_callback' ), 'amp-google-analytics' );
  	add_settings_field( 'tracking_id', 'Google Analytics ID:', array( $this, 'tracking_id_callback' ), 'amp-google-analytics', 'amp_analytics_google_analytics' );
  	register_setting( 'amp_google_analytics', 'amp_google_analytics', array( $this, 'sanitize' ) );
  }

  public function options_page_contents() {
  ?>
  <h1>AMP Google Analytics</h1>

  <form action="options.php" method="POST">

    <?php

    settings_fields( 'amp_google_analytics' );

    do_settings_sections( 'amp-google-analytics' );

    submit_button();

    ?>

  </form>

  <?php
  }

  /**
   * Google Analytics Details Entry callback
   *
   * @since 1.0
   */
  public function section_callback() {
    print( 'Please enter your Google Analytics details here.' );
  }

  /**
   * Callback for Tracking ID input
   *
   * @since 1.0
   */
  public function tracking_id_callback() {
  	$options = get_option( 'amp_google_analytics' );
  	?>
  		<input type="text" name="amp_google_analytics[tracking_id]" value="<?php echo $options[ 'tracking_id' ]; ?>">
  	<?php
  }

  /**
   * Sanitizes settings before they get to the database.
   *
   * @since 1.0
   *
   * @param array $entry Google Analytics options array
   *
   * @return array Sanitized, database-ready options array.
   */
  public function sanitize( $entry ) {

  	if ( $entry['tracking_id'] ) {
  		$entry['tracking_id'] = sanitize_text_field( $entry['tracking_id'] );
  	}

  	return $entry;
  }
}
