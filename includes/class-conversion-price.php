<?php 


add_filter( 'wc_price', 'cris_conversion_price', 10, 3 );

function cris_conversion_price( $return, $price, $args){

	global $wpdb;
	$nombreTabla = $wpdb->prefix . "conversion";

  	$registros = $wpdb->get_results( "SELECT monto, moneda FROM $nombreTabla" );
 
   

$args = apply_filters('wc_price_args',wp_parse_args($args,
		array(
				'ex_tax_label'       => false,
				'currency'           => '',
				'decimal_separator'  => wc_get_price_decimal_separator(),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimals'           => wc_get_price_decimals(),
				'price_format'       => get_woocommerce_price_format(),
			)
		)
	);

	$unformatted_price = $price;
	$negative          = $price < 0;
	$price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
	
	$bs             = (apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) )*floatval($registros[0]->monto));
	
	$price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );


    $bs              = apply_filters( 'formatted_woocommerce_price', number_format( $bs, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $bs, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
		$price = wc_trim_zeros( $price );
	}

	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $args['currency'] ) . ' </span>', $price );
	
	$formatted_price_bs = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], '<span class="woocommerce-Price-currencySymbol">' . $registros[0]->moneda ." ". '</span>', $bs );
	
	$return          = '<span class="woocommerce-Price-amount amount">' . $formatted_price .'<br/>'.$formatted_price_bs.'</span>' ;

	if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
		$return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
	}

	/**
	 * Filters the string of price markup.
	 *
	 * @param string $return            Price HTML markup.
	 * @param string $price             Formatted price.
	 * @param array  $args              Pass on the args.
	 * @param float  $unformatted_price Price as float to allow plugins custom formatting. Since 3.2.0.
	 */
	return apply_filters( 'cris_conversion_price', $return, $price, $args, $unformatted_price );
}





 ?>