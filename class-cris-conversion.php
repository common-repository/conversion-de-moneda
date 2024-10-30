<?php
/**
 * @package WooCommerce/Functions
 * @version 2.1.0
 */
/*
Plugin Name: Conversión de Moneda
Plugin URI: 
Description: Este plugin permite hacer conversion de moneda en su tienda.
Author: Cristian Aguilera
Version: 1.0
Author URI: 
*/

if ( ! function_exists( 'cdm_fs' ) ) {
    // Create a helper function for easy SDK access.
    function cdm_fs() {
        global $cdm_fs;

        if ( ! isset( $cdm_fs ) ) {
            // Activate multisite network integration.
            if ( ! defined( 'WP_FS__PRODUCT_6350_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_6350_MULTISITE', true );
            }

            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $cdm_fs = fs_dynamic_init( array(
                'id'                  => '6350',
                'slug'                => 'conversion-de-moneda',
                'type'                => 'plugin',
                'public_key'          => 'pk_fc7b8a8d5a000e775771519f94706',
                'is_premium'          => true,
                'premium_suffix'      => 'PRO',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'test_menu_slug',
                    'support'        => false,
                ),
                // Set the SDK to work in a sandbox mode (for development & testing).
                // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
                'secret_key'          => 'sk_vBwkfQ:qkg~M(A4]Qk<r2*c&+h=_:',
            ) );
        }

        return $cdm_fs;
    }

    // Init Freemius.
    cdm_fs();
    // Signal that SDK was initiated.
    do_action( 'cdm_fs_loaded' );
}


require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once plugin_dir_path( __FILE__ ) . 'includes/class-conversion-price.php';
// include( ABSPATH . 'wp-admin/admin-header.php' );



//Comprobar que el plugin woocommerce esta activo
register_activation_hook( __FILE__, 'cris_woo_active' );

function cris_woo_active(){

    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) 
        and current_user_can( 'activate_plugins' ) ) {
           // Para las máquinas y muestra un error
           wp_die('Uppsss. Este plugin necesita que esté activado el plugin "Woocommerce" por lo que no se puede activar.. <br>
                    <a href="' . admin_url( 'plugins.php' ) . '">&laquo; 
                    Volver a la página de Plugins</a>');
    }
}

// ejemplo de plugin para crear una tabla en WordPress 


function cris_db_conversion() {

  global $wpdb;
  $nombreTabla = $wpdb->prefix . "conversion";
  
  $created = dbDelta(  
    "CREATE TABLE $nombreTabla (
      ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      moneda varchar(4) NOT NULL,
      monto decimal(11,2) NOT NULL,
      UNIQUE(monto),
      PRIMARY KEY (ID)
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;"
  );
  
  $wpdb->insert( $nombreTabla, 
  
    array( 
 
      'moneda' => 'USD',
      'monto'=> '00' 
    )
  );

} 

register_activation_hook( __FILE__, 'cris_db_conversion' );

add_action("admin_menu", "cris_crear_menu");
function cris_crear_menu() {
  add_menu_page('Conversion de Dolares a Bolivres', 'Conversión de moneda', 'manage_options', 'test_menu_slug', 'cris_output_menu');
  add_submenu_page('test_menu_slug', 'Proximamente', 'Proximamente', 'manage_options', 'test_submenu_slug', 'cris_output_submenu');
}


function cris_output_menu() {

  $select_cris_moneda=sanitize_text_field($_POST['moneda']);
  update_post_meta($post->ID, 'moneda', $select_cris_moneda);

  $input_cris_monto=sanitize_text_field($_POST['monto']);
  update_post_meta($post->ID, 'monto', $input_cris_monto);

  $input_cris_id=sanitize_text_field($_POST['dolar_checkbox']);
  update_post_meta($post->ID, 'dolar_checkbox', $input_cris_monto);


    global $wpdb;
    $nombreTabla = $wpdb->prefix . "conversion";
  $wpdb->update( $nombreTabla, 
    // Datos que se remplazarán
    array( 
     
      'moneda' => $select_cris_moneda,
      'monto' => $input_cris_monto,

      
    ),
    // Cuando el ID del campo es igual al número 1
    array( 'ID' => $input_cris_id )
  );

    $registros = $wpdb->get_results( "SELECT monto,moneda FROM $nombreTabla" );
  ?>

      <div class=''><h1>Conversion de moneda</h1>
        <h2>Monto actual en tu pagina: <h2>
        <h1 style='color:blue;'> <?php esc_html_e($registros[0]->moneda ." ". $registros[0]->monto)  ?></h1>

  
        <br>
        <form method="POST">
            <h3>Ingresa monto para la conversión : </h3>
            <select id="moneda" name="moneda">
                                        <option value="AED">United Arab Emirates dirham (د.إ)</option>
                                        <option value="AFN">Afghan afghani (؋)</option>
                                        <option value="ALL">Albanian lek (L)</option>
                                        <option value="AMD">Armenian dram (AMD)</option>
                                        <option value="ANG">Netherlands Antillean guilder (ƒ)</option>
                                        <option value="AOA">Angolan kwanza (Kz)</option>
                                        <option value="ARS">Argentine peso ($)</option>
                                        <option value="AUD">Australian dollar ($)</option>
                                        <option value="AWG">Aruban florin (Afl.)</option>
                                        <option value="AZN">Azerbaijani manat (AZN)</option>
                                        <option value="BAM">Bosnia and Herzegovina convertible mark (KM)</option>
                                        <option value="BBD">Barbadian dollar ($)</option>
                                        <option value="BDT">Bangladeshi taka (৳&nbsp;)</option>
                                        <option value="BGN">Bulgarian lev (лв.)</option>
                                        <option value="BHD">Bahraini dinar (.د.ب)</option>
                                        <option value="BIF">Burundian franc (Fr)</option>
                                        <option value="BMD">Bermudian dollar ($)</option>
                                        <option value="BND">Brunei dollar ($)</option>
                                        <option value="BOB">Bolivian boliviano (Bs.)</option>
                                        <option value="BRL">Brazilian real (R$)</option>
                                        <option value="BSD">Bahamian dollar ($)</option>
                                        <option value="BTC">Bitcoin (฿)</option>
                                        <option value="BTN">Bhutanese ngultrum (Nu.)</option>
                                        <option value="BWP">Botswana pula (P)</option>
                                        <option value="BYR">Belarusian ruble (old) (Br)</option>
                                        <option value="BYN">Belarusian ruble (Br)</option>
                                        <option value="BZD">Belize dollar ($)</option>
                                        <option value="CAD">Canadian dollar ($)</option>
                                        <option value="CDF">Congolese franc (Fr)</option>
                                        <option value="CHF">Swiss franc (CHF)</option>
                                        <option value="CLP">Chilean peso ($)</option>
                                        <option value="CNY">Chinese yuan (¥)</option>
                                        <option value="COP">Colombian peso ($)</option>
                                        <option value="CRC">Costa Rican colón (₡)</option>
                                        <option value="CUC">Cuban convertible peso ($)</option>
                                        <option value="CUP">Cuban peso ($)</option>
                                        <option value="CVE">Cape Verdean escudo ($)</option>
                                        <option value="CZK">Czech koruna (Kč)</option>
                                        <option value="DJF">Djiboutian franc (Fr)</option>
                                        <option value="DKK">Danish krone (DKK)</option>
                                        <option value="DOP">Dominican peso (RD$)</option>
                                        <option value="DZD">Algerian dinar (د.ج)</option>
                                        <option value="EGP">Egyptian pound (EGP)</option>
                                        <option value="ERN">Eritrean nakfa (Nfk)</option>
                                        <option value="ETB">Ethiopian birr (Br)</option>
                                        <option value="EUR">Euro (€)</option>
                                        <option value="FJD">Fijian dollar ($)</option>
                                        <option value="FKP">Falkland Islands pound (£)</option>
                                        <option value="GBP">Pound sterling (£)</option>
                                        <option value="GEL">Georgian lari (₾)</option>
                                        <option value="GGP">Guernsey pound (£)</option>
                                        <option value="GHS">Ghana cedi (₵)</option>
                                        <option value="GIP">Gibraltar pound (£)</option>
                                        <option value="GMD">Gambian dalasi (D)</option>
                                        <option value="GNF">Guinean franc (Fr)</option>
                                        <option value="GTQ">Guatemalan quetzal (Q)</option>
                                        <option value="GYD">Guyanese dollar ($)</option>
                                        <option value="HKD">Hong Kong dollar ($)</option>
                                        <option value="HNL">Honduran lempira (L)</option>
                                        <option value="HRK">Croatian kuna (kn)</option>
                                        <option value="HTG">Haitian gourde (G)</option>
                                        <option value="HUF">Hungarian forint (Ft)</option>
                                        <option value="IDR">Indonesian rupiah (Rp)</option>
                                        <option value="ILS">Israeli new shekel (₪)</option>
                                        <option value="IMP">Manx pound (£)</option>
                                        <option value="INR">Indian rupee (₹)</option>
                                        <option value="IQD">Iraqi dinar (ع.د)</option>
                                        <option value="IRR">Iranian rial (﷼)</option>
                                        <option value="IRT">Iranian toman (تومان)</option>
                                        <option value="ISK">Icelandic króna (kr.)</option>
                                        <option value="JEP">Jersey pound (£)</option>
                                        <option value="JMD">Jamaican dollar ($)</option>
                                        <option value="JOD">Jordanian dinar (د.ا)</option>
                                        <option value="JPY">Japanese yen (¥)</option>
                                        <option value="KES">Kenyan shilling (KSh)</option>
                                        <option value="KGS">Kyrgyzstani som (сом)</option>
                                        <option value="KHR">Cambodian riel (៛)</option>
                                        <option value="KMF">Comorian franc (Fr)</option>
                                        <option value="KPW">North Korean won (₩)</option>
                                        <option value="KRW">South Korean won (₩)</option>
                                        <option value="KWD">Kuwaiti dinar (د.ك)</option>
                                        <option value="KYD">Cayman Islands dollar ($)</option>
                                        <option value="KZT">Kazakhstani tenge (₸)</option>
                                        <option value="LAK">Lao kip (₭)</option>
                                        <option value="LBP">Lebanese pound (ل.ل)</option>
                                        <option value="LKR">Sri Lankan rupee (රු)</option>
                                        <option value="LRD">Liberian dollar ($)</option>
                                        <option value="LSL">Lesotho loti (L)</option>
                                        <option value="LYD">Libyan dinar (ل.د)</option>
                                        <option value="MAD">Moroccan dirham (د.م.)</option>
                                        <option value="MDL">Moldovan leu (MDL)</option>
                                        <option value="MGA">Malagasy ariary (Ar)</option>
                                        <option value="MKD">Macedonian denar (ден)</option>
                                        <option value="MMK">Burmese kyat (Ks)</option>
                                        <option value="MNT">Mongolian tögrög (₮)</option>
                                        <option value="MOP">Macanese pataca (P)</option>
                                        <option value="MRU">Mauritanian ouguiya (UM)</option>
                                        <option value="MUR">Mauritian rupee (₨)</option>
                                        <option value="MVR">Maldivian rufiyaa (.ރ)</option>
                                        <option value="MWK">Malawian kwacha (MK)</option>
                                        <option value="MXN">Mexican peso ($)</option>
                                        <option value="MYR">Malaysian ringgit (RM)</option>
                                        <option value="MZN">Mozambican metical (MT)</option>
                                        <option value="NAD">Namibian dollar (N$)</option>
                                        <option value="NGN">Nigerian naira (₦)</option>
                                        <option value="NIO">Nicaraguan córdoba (C$)</option>
                                        <option value="NOK">Norwegian krone (kr)</option>
                                        <option value="NPR">Nepalese rupee (₨)</option>
                                        <option value="NZD">New Zealand dollar ($)</option>
                                        <option value="OMR">Omani rial (ر.ع.)</option>
                                        <option value="PAB">Panamanian balboa (B/.)</option>
                                        <option value="PEN">Sol (S/)</option>
                                        <option value="PGK">Papua New Guinean kina (K)</option>
                                        <option value="PHP">Philippine peso (₱)</option>
                                        <option value="PKR">Pakistani rupee (₨)</option>
                                        <option value="PLN">Polish złoty (zł)</option>
                                        <option value="PRB">Transnistrian ruble (р.)</option>
                                        <option value="PYG">Paraguayan guaraní (₲)</option>
                                        <option value="QAR">Qatari riyal (ر.ق)</option>
                                        <option value="RON">Romanian leu (lei)</option>
                                        <option value="RSD">Serbian dinar (рсд)</option>
                                        <option value="RUB">Russian ruble (₽)</option>
                                        <option value="RWF">Rwandan franc (Fr)</option>
                                        <option value="SAR">Saudi riyal (ر.س)</option>
                                        <option value="SBD">Solomon Islands dollar ($)</option>
                                        <option value="SCR">Seychellois rupee (₨)</option>
                                        <option value="SDG">Sudanese pound (ج.س.)</option>
                                        <option value="SEK">Swedish krona (kr)</option>
                                        <option value="SGD">Singapore dollar ($)</option>
                                        <option value="SHP">Saint Helena pound (£)</option>
                                        <option value="SLL">Sierra Leonean leone (Le)</option>
                                        <option value="SOS">Somali shilling (Sh)</option>
                                        <option value="SRD">Surinamese dollar ($)</option>
                                        <option value="SSP">South Sudanese pound (£)</option>
                                        <option value="STN">São Tomé and Príncipe dobra (Db)</option>
                                        <option value="SYP">Syrian pound (ل.س)</option>
                                        <option value="SZL">Swazi lilangeni (L)</option>
                                        <option value="THB">Thai baht (฿)</option>
                                        <option value="TJS">Tajikistani somoni (ЅМ)</option>
                                        <option value="TMT">Turkmenistan manat (m)</option>
                                        <option value="TND">Tunisian dinar (د.ت)</option>
                                        <option value="TOP">Tongan paʻanga (T$)</option>
                                        <option value="TRY">Turkish lira (₺)</option>
                                        <option value="TTD">Trinidad and Tobago dollar ($)</option>
                                        <option value="TWD">New Taiwan dollar (NT$)</option>
                                        <option value="TZS">Tanzanian shilling (Sh)</option>
                                        <option value="UAH">Ukrainian hryvnia (₴)</option>
                                        <option value="UGX">Ugandan shilling (UGX)</option>
                                        <option value="USD">United States (US) dollar ($)</option>
                                        <option value="UYU">Uruguayan peso ($)</option>
                                        <option value="UZS">Uzbekistani som (UZS)</option>
                                        <option value="VEF">Venezuelan bolívar (Bs F)</option>
                                        <option value="VES">Bolívar soberano (Bs.S)</option>
                                        <option value="VND">Vietnamese đồng (₫)</option>
                                        <option value="VUV">Vanuatu vatu (Vt)</option>
                                        <option value="WST">Samoan tālā (T)</option>
                                        <option value="XAF">Central African CFA franc (CFA)</option>
                                        <option value="XCD">East Caribbean dollar ($)</option>
                                        <option value="XOF">West African CFA franc (CFA)</option>
                                        <option value="XPF">CFP franc (Fr)</option>
                                        <option value="YER">Yemeni rial (﷼)</option>
                                        <option value="ZAR">South African rand (R)</option>
                                        <option value="ZMW">Zambian kwacha (ZK)</option>
               



          </select>
            <input id="monto" class="tel" type="text" name="monto" placeholder="Ingresar monto" required />
            <br>
            <input id="dolar_checkbox" type="hidden" name="dolar_checkbox" value="1" />
            <?php submit_button(); ?>
        </form>
</div>

  <?php

  
}


function cris_output_submenu() {
  esc_html_e( '<h1>Proximamente...</h1>', 'text_domain' );


}





