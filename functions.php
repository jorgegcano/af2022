<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
// Disable City
add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_false' );
add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('brandon-grotesque-font-css', get_stylesheet_directory_uri() . '/fonts/BrandonGrotesque/stylesheet.css');
});	
add_action( 'woocommerce_before_add_to_cart_form', 'woocommerce_template_single_price', 11 );

function add_nutritional_info()
{
    $formato = get_field('informacion_nutricional')['formato'];
    $concat = get_field('informacion_nutricional')['formato'] === 'porciones' ? 'Porción' : 'Unidad';
     ?>
     
        <table class="nutritional-info-table" border="1">
            <tr>
                <th colspan="2"><strong>Información Nutricional</strong></th>
            </tr>
            <tr>
                <td colspan="2"><strong><?php echo get_field('informacion_nutricional')['packaging']; ?></strong></td>
            </tr>
            <tr>
                <td><?php echo $formato; ?></td>
                <td style="text-align:center;">
                    <?php 
                    echo $formato === 'porciones' ?
                    get_field('informacion_nutricional')['porciones']
                        :
                    get_field('informacion_nutricional')['unidades']; 
                    ?>
                </td>
            </tr>
            <tr>
                <th colspan="2"><strong>Cantidad por <?php echo $concat; ?></strong></th>
            </tr>
            <tr>
                <td>Calorías</td>
                <td style="text-align:center;"><?php echo get_field('informacion_nutricional')['calorias']; ?></td>
            </tr>
            <tr>
                <td>Proteínas (g)</td>
                <td style="text-align:center;"><?php echo get_field('informacion_nutricional')['proteinas']; ?></td>
            </tr>
            <tr>
                <td>Grasas (g)</td>
                <td style="text-align:center;"><?php echo get_field('informacion_nutricional')['grasas']; ?></td>
            </tr>
            <tr>
                <td>Carbohidratos (g)</td>
                <td style="text-align:center;"><?php echo get_field('informacion_nutricional')['carbohidratos']; ?></td>
            </tr>
            <tr>
                <th colspan="2"><strong>Alérgenos</strong></th>
            </tr>
            <tr>
                <td colspan="2">
                    <?php get_alergenos() ?>
                </td>
            </tr>
            <?php if (!empty(get_field('informacion_nutricional')['observaciones'])) { ?>
                <tr>
                <td colspan="2" style="color:black;font-size:12px;padding:0 15px;font-style:italic;">
                    *<?php echo get_field('informacion_nutricional')['observaciones']; ?>
                </td>
            </tr> <?php 
            } ?>

        </table>
       
     <?php
}

function get_alergenos() {
    $alergenos = get_field( 'lista_alergenos' );?>
     <ul class='listado-alergenos'> <?php
    $path = get_stylesheet_directory_uri()."/images/alergenos-template-producto/";
    
    foreach ($alergenos as $alergeno => $value) { ?>
        <li class='alergeno <?php echo $value?>'><img src='<?php echo $path.$value.".png"; ?>' alt='alergeno-<?php echo $value?>'/></li> <?php
    } ?>
    </ul> <?php

}
add_action( 'woocommerce_before_add_to_cart_form', 'add_nutritional_info', 10 );

function wc_varb_price_range( $wcv_price, $product ) {

    $prefix = sprintf('%s: ', __('Desde', 'wcvp_range'));
    
    $wcv_reg_min_price = $product->get_variation_regular_price( 'min', true );
    
    $wcv_min_sale_price    = $product->get_variation_sale_price( 'min', true );
    
    $wcv_max_price = $product->get_variation_price( 'max', true );
    
    $wcv_min_price = $product->get_variation_price( 'min', true );
    
    $wcv_price = ( $wcv_min_sale_price == $wcv_reg_min_price ) ?
    
    wc_price( $wcv_reg_min_price ) :
    
    '<del>' . wc_price( $wcv_reg_min_price ) . '</del>' . '<ins>' . wc_price( $wcv_min_sale_price ) . '</ins>';
    
    return ( $wcv_min_price == $wcv_max_price ) ?
    
    $wcv_price :
    
    sprintf('%s%s', $prefix, $wcv_price);
    
}

add_filter( 'woocommerce_variable_sale_price_html', 'wc_varb_price_range', 10, 2 );
    
add_filter( 'woocommerce_variable_price_html', 'wc_varb_price_range', 10, 2 );

 /**
 * Remove product data tabs
 */
function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['additional_information'] );

    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function ejr_definir_provincias ($provincias) {
    $provincias ['ES'] = array(
       'M' => 'Madrid'
       );
    return $provincias;
 }
add_filter ('woocommerce_states', 'ejr_definir_provincias');

// Our hooked in function - $address_fields is passed via the filter!
function custom_override_default_address_fields( $address_fields ) {


    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' ); // Método de envío seleccionado
    if(isset($chosen_methods)) {
        $chosen_method = explode(':', reset($chosen_methods) );
    }
    
    if (substr( $chosen_method[0], 0, 12 ) === "local_pickup") {
        $address_fields['address_1']['required'] = false;
        $address_fields['address_2']['required'] = false;
        $address_fields['city']['required'] = false;
        $address_fields['state']['required'] = false;
        $address_fields['postcode']['required'] = false;
        return $address_fields;
    } else {
        $address_fields['address_1']['required'] = true;
        $address_fields['address_2']['required'] = false;
        $address_fields['city']['required'] = true;
        $address_fields['state']['required'] = true;
        $address_fields['postcode']['required'] = true;
   
        return $address_fields;
    }
}
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

function my_hide_shipping_when_free_is_available( $rates ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}

	if (! empty($free)) {
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'local_pickup' === $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}	
		}
		return $free;	
	}
	
	return ! empty( $free ) ? $free : $rates;
}
add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );

function my_completed_order_email_instructions( $order, $sent_to_admin, $plain_text, $email ) {
    if('customer_processing_order' == $email->id){
		foreach( $order->get_items('shipping') as $shipping_item ){
        	$shipping_rate_id = $shipping_item->get_method_id();
        	$method_array = explode(':', $shipping_rate_id );
        	$shipping_method_id = reset($method_array);
        	// Display a custom text for local pickup shipping method only
        	if( 'local_pickup' == $shipping_method_id ){
            	echo '<p><strong>¡IMPORTANTE!</strong></p><p><strong>En el transcurso del día nos pondremos en contacto contigo para coordinar la recogida de tus antojos FIT en 					Legazpi</strong></p>';
            	break;
        	}
    	}
	}
}
add_action( 'woocommerce_email_order_details', 'my_completed_order_email_instructions', 10, 4 );

function bt_add_checkout_checkbox() {  

    woocommerce_form_field( 'checkout_checkbox_privacy', array(
        'type'          => 'checkbox',
        'class'         => array('form-row mycheckbox'),
        'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
        'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
        'required'      => true,
        'label'         => 'Estoy de acuerdo con la <a href="https://www.antojosfit.es/politica-de-privacidad" target="_blank" rel="noopener">Política de privacidad</a>'
     ));  
}
add_action( 'woocommerce_review_order_before_submit', 'bt_add_checkout_checkbox', 10 );

function bt_add_checkout_checkbox_warning() {

    if ( ! (int) isset( $_POST['checkout_checkbox_privacy'] ) ) {
        wc_add_notice( __( 'Debes aceptar la política de privacidad' ), 'error' );
    }
}
add_action( 'woocommerce_checkout_process', 'bt_add_checkout_checkbox_warning' );

function oaf_woocommerce_boton_seguir_comprando_carrito(  ) {

	$tienda_url = get_permalink( woocommerce_get_page_id( 'shop' ) );  // obtener la url de la página de tienda
	?>
	<a class="button wc-backward" href="<?php echo $tienda_url ?>">
		Seguir comprando
	</a>
	<?php
}
add_action( 'woocommerce_cart_actions', 'oaf_woocommerce_boton_seguir_comprando_carrito', 10, 0 );
add_action( 'woocommerce_order_details_after_customer_details', 'oaf_woocommerce_boton_seguir_comprando_carrito', 10, 0 );

function woo_change_order_received_text( $str, $order ) {
    $order = new WC_Order( $order->data["id"] );
    $payment_method = $order->get_payment_method();
    $bizum_text = '<br>Envía Bizum de <strong style="color:#777">'.$order->data["total"].'€</strong> al número <strong style="color:#777">611 611 611</strong> añadiendo como concepto el número: <strong style="color:#777">'.$order->data["id"].'</strong>.<br>Recibirás un email cuando confirmemos el pago.';
    return $payment_method == 'cheque' ? $str.$bizum_text : $str;
}
add_filter('woocommerce_thankyou_order_received_text', 'woo_change_order_received_text', 10, 2 );

function soivigol_insert_popup() {
    ?>
    <div class="soivigol-popup">
       <div class="soivigol-popup-inner">
        <span class="fas fa-truck"></span>
        <p>Inserta tu código postal</p>
        <input id="calc_shipping_postcode" type="text">
        <button disabled id="check_shipping_postcode">Comprobar</button>
        <p id="mensaje_zona_envio"></p>
        <?php if(is_front_page() || is_shop()) { ?>
        <script type="text/javascript">
            let validCodesList = <?php echo json_encode(get_postal_codes()); ?>;
            const cpInput = document.getElementById('calc_shipping_postcode');
            const cpButton = document.getElementById('check_shipping_postcode');
            console.log(validCodesList)
            cpInput.addEventListener('keyup', (e) => {cpInput.value.length === 5 ? cpButton.removeAttribute('disabled') : cpButton.setAttribute('disabled','')})
            cpButton.addEventListener('click', (e) => {
                if ( validCodesList.includes(Number(cpInput.value))) {
                    console.log("Válido")
                    document.getElementById("mensaje_zona_envio").innerHTML = "Envío a domicilio disponible para esta zona.";
                } 
                else 
                {
                    console.log("NO Válido");
                    document.getElementById("mensaje_zona_envio").innerHTML = "Envío a domicilio no disponible para esta zona.";
                }
            });
        </script>
        <?php } ?>
       <span id="cerrar-pop-up" class="soivigol-close">&times;</span>
       </div>
    </div>
    <?php
}
add_action( 'wp_footer', 'soivigol_insert_popup' );

function get_postal_codes(){
    $delivery_zones = WC_Shipping_Zones::get_zones();

    foreach ((array) $delivery_zones as $key => $the_zone ) {
        foreach($the_zone['zone_locations'] as $location) {
            $result[] = intval($location->code);
        }
    }
    
    return $result;
}
