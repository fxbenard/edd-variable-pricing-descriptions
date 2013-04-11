<?php
/*
Plugin Name: EDD Variable Pricing Descriptions
Plugin URI: http://sumobi.com/store/edd-variable-pricing-descriptions/
Description: Adds a description field for each variable pricing option
Version: 1.0.1
Author: Andrew Munro - Sumobi
Author URI: http://sumobi.com
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

if ( !defined( 'EDDVPD_PLUGIN_DIR' ) ) {
	define( 'EDDVPD_PLUGIN_DIR', dirname( __FILE__ ) );
}

if ( !defined( 'EDDVPD_PLUGIN_URL' ) ) {
	define( 'EDDVPD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Internationalization
 */
function edd_vpd_textdomain() {
	load_plugin_textdomain( 'edd-vpd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'edd_vpd_textdomain' );


/**
 * Variable price output
 * Adds variable pricing description. You can further style it using the provided .edd-variable-pricing-desc CSS class
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function edd_vpd_purchase_variable_pricing( $download_id ) {
	$variable_pricing = edd_has_variable_prices( $download_id );

	if ( ! $variable_pricing )
		return;

	$prices = apply_filters( 'edd_purchase_variable_prices', edd_get_variable_prices( $download_id ), $download_id );

	$type   = edd_single_price_option_mode( $download_id ) ? 'checkbox' : 'radio';

	do_action( 'edd_before_price_options', $download_id ); ?>

	<div class="edd_price_options">
		<ul>
			<?php
	if ( $prices ):
		foreach ( $prices as $key => $price ) :
			$amount = $price[ 'amount' ];
		printf(
			'<li><label for="%3$s"><input type="%2$s" %1$s name="edd_options[price_id][]" id="%3$s" class="%4$s" value="%5$s" %7$s/> %6$s<p class="edd-variable-pricing-desc">%8$s</p></label></li>',
			checked( 0, $key, false ),
			$type,
			esc_attr( 'edd_price_option_' . $download_id . '_' . $key ),
			esc_attr( 'edd_price_option_' . $download_id ),
			esc_attr( $key ),
			esc_html( $price['name'] . ' - ' . edd_currency_filter( edd_format_amount( $amount ) ) ),
			checked( isset( $_GET['price_option'] ), $key, false ),
			esc_html( $price['description'] )
		);
	endforeach;
	endif;
	do_action( 'edd_after_price_options_list', $download_id, $prices, $type );
?>
		</ul>
	</div><!--end .edd_price_options-->
<?php
	add_action( 'edd_after_price_options', $download_id );
}

// remove the default EDD edd_purchase_variable_pricing function
remove_action( 'edd_purchase_link_top', 'edd_purchase_variable_pricing' );

// add our new custom function
add_action( 'edd_purchase_link_top', 'edd_vpd_purchase_variable_pricing' );


/**
 * Adds the table header
 *
 * @since 1.0
 */
function edd_vpd_download_price_table_head() { ?>

	<th><?php _e( 'Option Description', 'edd-vpd' ); ?></th>

<?php }
add_action( 'edd_download_price_table_head', 'edd_vpd_download_price_table_head' );


/**
 * Adds the table cell with description input field
 *
 * @since 1.0
 */
function edd_vpd_download_price_table_row( $post_id, $key, $args ) {
	$description = isset($args['description']) ? $args['description'] : null;
?>

	<td>
		<input type="text" class="edd_variable_prices_description" value="<?php echo esc_attr( $args['description'] ); ?>" placeholder="<?php _e( 'Option Description', 'edd-vpd' ); ?>" name="edd_variable_prices[<?php echo $key; ?>][description]" id="edd_variable_prices[<?php echo $key; ?>][description]" size="20" style="width:100%" />
	</td>

<?php }
add_action( 'edd_download_price_table_row', 'edd_vpd_download_price_table_row', 10, 3 );


/**
 * Add description field to edd_price_row_args
 *
 * @since 1.0
 */
function edd_vpd_price_row_args( $args, $value ) {

	$args['description'] = isset( $value['description'] ) ? $value['description'] : '';

	return $args;

}
add_filter( 'edd_price_row_args', 'edd_vpd_price_row_args', 10, 2 );