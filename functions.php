<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), '7.74' );
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
    if (get_field('informacion_nutricional')['ocultar_tabla']) { return; }
    if (is_product()) {
    $formato = get_field('informacion_nutricional')['formato'];
    $concat = get_field('informacion_nutricional')['formato'] === 'porciones' ? 'Porción' : 'Unidad';
     ?>
     
        <table class="nutritional-info-table" border="1">
            <tr>
                <th colspan="2"><strong>Información Nutricional</strong></th>
            </tr>
            <tr>
                <td colspan="2"><?php echo get_field('informacion_nutricional')['packaging']; ?></td>
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
                <td colspan="2" style="font-size:12px;padding:5px 15px;font-style:italic;">
                    <span style="color:red;">*</span><?php echo get_field('informacion_nutricional')['observaciones']; ?>
                </td>
            </tr> <?php 
            } ?>

        </table>
       
     <?php
     }
}

function get_alergenos() {
    $alergenos = get_field( 'lista_alergenos' );?>
     <ul class='listado-alergenos'> <?php
    $path = get_stylesheet_directory_uri()."/images/alergenos-template-producto/";
    
    foreach ($alergenos as $alergeno => $value) { ?>
        <li class='alergeno <?php echo $value?>'><?php echo $value?></li> <?php
        //<img src='aquí-abres-php echo $path.$value.".svg"; aquí-cierras-php<!-- ' alt='alergeno- aquí-abres-php //echo $value aquí-cierras-php'/>
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

    //  $chosen_methods = WC()->session->get( 'chosen_shipping_methods' ); // Método de envío seleccionado
    //  if(isset($chosen_methods)) {
    //      $chosen_method = explode(':', reset($chosen_methods) );  
    //      if (substr( $chosen_method[0], 0, 12 ) === "local_pickup") {
    //         $address_fields['address_1']['required'] = false;
    //         $address_fields['address_1']['class'] = array( 'd-none');
    //         $address_fields['address_2']['required'] = false;
    //         $address_fields['address_2']['class'] = array( 'd-none');
    //         $address_fields['city']['required'] = false;
    //         $address_fields['city']['class'] = array( 'd-none');
    //         $address_fields['state']['required'] = false;
    //         $address_fields['state']['class'] = array( 'd-none');
    //         $address_fields['postcode']['required'] = false;
    //         if(!is_cart()) {
    //             $address_fields['postcode']['class'] = array( 'd-none hide-hook');
    //         }
    //         $address_fields['country']['required'] = false;
    //         $address_fields['country']['class'] = array( 'd-none');
    //     } else {
    //         $address_fields['address_1']['required'] = true;
    //         $address_fields['address_2']['required'] = false;
    //         $address_fields['city']['required'] = true;
    //         $address_fields['state']['required'] = true;
    //         $address_fields['postcode']['required'] = true;
    //     }
    // } else {
    //         $address_fields['address_1']['required'] = false;
    //         $address_fields['address_1']['class'] = array( 'd-none');
    //         $address_fields['address_2']['required'] = false;
    //         $address_fields['address_2']['class'] = array( 'd-none');
    //         $address_fields['city']['required'] = false;
    //         $address_fields['city']['class'] = array( 'd-none');
    //         $address_fields['state']['required'] = false;
    //         $address_fields['state']['class'] = array( 'd-none');
    //         $address_fields['postcode']['required'] = false;
    //         $address_fields['postcode']['class'] = array( 'd-none');
    // }

    $address_fields['address_1']['required'] = false;
    $address_fields['address_1']['class'] = array( 'd-none');
    $address_fields['address_2']['required'] = false;
    $address_fields['address_2']['class'] = array( 'd-none');
    $address_fields['city']['required'] = false;
    $address_fields['city']['class'] = array( 'd-none');
    $address_fields['state']['required'] = false;
    $address_fields['state']['class'] = array( 'd-none');
    $address_fields['postcode']['required'] = false;
    // if(!is_cart()) {
    //     $address_fields['postcode']['class'] = array( 'd-none hide-hook');
    // }
    $address_fields['country']['required'] = false;
    $address_fields['country']['class'] = array( 'd-none');

    return $address_fields;
 }
 add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

// function my_hide_shipping_when_free_is_available( $rates ) {
// 	$free = array();
// 	foreach ( $rates as $rate_id => $rate ) {
// 		if ( 'free_shipping' === $rate->method_id ) {
// 			$free[ $rate_id ] = $rate;
// 			break;
// 		}
// 	}

// 	if (! empty($free)) {
// 		foreach ( $rates as $rate_id => $rate ) {
// 			if ( 'local_pickup' === $rate->method_id ) {
// 				$free[ $rate_id ] = $rate;
// 				break;
// 			}	
// 		}
// 		return $free;	
// 	}
	
// 	return ! empty( $free ) ? $free : $rates;
// }
// add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );

function my_completed_order_email_instructions( $order, $sent_to_admin, $plain_text, $email ) {
    if('customer_on_hold_order' == $email->id && 'cheque' == $order->get_payment_method()){
         echo '<p><strong>Si no lo has hecho aún, te recordamos que debes enviar el importe TOTAL del pago por Bizum con el número de pedido #'.$order->get_id().' al +34 611 425 698. Comenzaremos a procesar tu pedido en cuanto confirmemos el pago.</strong></p>';
	}
}
add_action( 'woocommerce_email_order_details', 'my_completed_order_email_instructions', 10, 4 );

function add_notice_about_shipping($order) {   
    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' ); 
    if(isset($chosen_methods)) {
        $chosen_method = explode(':', reset($chosen_methods) );
    }
    if ($chosen_method[0] == "local_pickup") {
        echo '<p style="padding:10px;border-radius:5px;font-style:italic;color:#242424;background-color:#F9E52C"><span style="color:red;">* </span>Solo queremos recordarte que <span style="text-decoration:underline;">has seleccionado recoger tu pedido en local (metro Legazpi)</span>. Si deseas seleccionar el envío a domicilio, vuelve atrás y valida tu código postal para comprobar si está dentro del área de envío.</p>';
    } 
    
}
add_filter('woocommerce_before_checkout_billing_form', 'add_notice_about_shipping', 10, 4 );

function bt_add_checkout_checkbox() {  

    woocommerce_form_field( 'checkout_checkbox_privacy', array(
        'type'          => 'checkbox',
        'class'         => array('form-row mycheckbox'),
        'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
        'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
        'required'      => true,
        'label'         => 'Estoy de acuerdo con la <a style="color:blue;text-decoration: underline;" href="https://www.antojosfit.es/politica-de-privacidad" target="_blank" rel="noopener">Política de privacidad</a>'
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

	$tienda_url = get_permalink( wc_get_page_id( 'shop' ) );  // obtener la url de la página de tienda
	?>
	<a class="button wc-backward" href="<?php echo $tienda_url ?>">
		Seguir comprando
	</a>
	<?php
}
add_action( 'woocommerce_cart_actions', 'oaf_woocommerce_boton_seguir_comprando_carrito', 10, 0 );
add_action( 'woocommerce_thankyou', 'oaf_woocommerce_boton_seguir_comprando_carrito', 10, 0 );

function woo_change_order_received_text( $str, $order ) {
    $payment_method = $order->get_payment_method();
    $bizum_text = '<br>Envía Bizum de <strong style="color:#777">'.$order->get_total().'€</strong> al número <strong style="color:#777">+34 611 425 698</strong> añadiendo como concepto el número: <strong style="color:#777">'.$order->get_id().'</strong>.<br>Recibirás un email cuando confirmemos el pago.';
    return $payment_method == 'cheque' ? $str.$bizum_text : $str;
}
add_filter('woocommerce_thankyou_order_received_text', 'woo_change_order_received_text', 10, 2 );

function soivigol_insert_popup() {
    ?>
    <div class="soivigol-popup">
       <div class="soivigol-popup-inner">
            <div class="soivigol-popup-inner-title">
                <span>COMPROBAR ZONA DE ENVÍO</span>
                <span id="cerrar-pop-up" class="soivigol-close fas fa-times"></span>
            </div>
            <div class="soivigol-popup-inner-subtitle">
                <span class="delivery-icon"><img src="<?php echo get_stylesheet_directory_uri()?>/images/brand-icons/delivery-icon-fill-pink.svg" alt="delivery icon"></span>
                <span>Indica el código postal donde quieres recibir tu pedido</span>
            </div>
        <input placeholder="Código postal" id="calc_shipping_postcode" type="text">
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
                    document.getElementById("mensaje_zona_envio").style.background = "#25CE71";
                } 
                else 
                {
                    console.log("NO Válido");
                    document.getElementById("mensaje_zona_envio").innerHTML = "El envío a domicilio no está disponible para esta zona.<br>Puedes optar por la 'Recogida en local' (C/ Jaime el Conquistador 27).";
                    document.getElementById("mensaje_zona_envio").style.background = "#FF502D";
                }
            });
        </script>
        <?php } ?>
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

add_action( 'get_header', function( $name ) {
    add_filter( 'current_header', function() use ( $name ) {
        // always return the same type, unlike WP
        return (string) $name;
    });
});

// -----------------------------------------
// 1. Show custom input field above Add to Cart

add_action( 'woocommerce_before_add_to_cart_button', 'bbloomer_product_add_on', 9 );


function bbloomer_product_add_on() {
    if (is_product()) {
        global $product;
        $categories = get_the_terms($product->get_id(), 'product_cat');
        if  (count($categories) == 1 && $categories[0]->slug != 'merchandising') {
            $valueDedication = isset( $_POST['dedication_text_add_on'] ) ? sanitize_text_field( $_POST['dedication_text_add_on'] ) : '';
            echo '<p class="dedication dedication-text-add-on-font-size">Añade una dedicatoria si lo deseas: <span id="show-dedication-field"><i class="fas fa-chevron-down"></i></span></p>';
            echo '<div class="dedication dedication-area-text-hide" style="margin-bottom:20px;"><textarea maxlength="200"  placeholder="Escribe una felicitación por cumpleaños, agradecimientos, celebraciones... " class="dedication-text-add-on-font-size" name="dedication_text_add_on" value="' . $valueDedication . '"></textarea></div>';
        }
    }
}

// -----------------------------------------
// 3. Save custom input field value into cart item data
add_filter( 'woocommerce_add_cart_item_data', 'bbloomer_product_add_on_cart_item_data', 10, 2 );
 
function bbloomer_product_add_on_cart_item_data( $cart_item, $product_id ){
    if( isset( $_POST['dedication_text_add_on'] )) {
        $cart_item['dedication_text_add_on'] = sanitize_text_field( $_POST['dedication_text_add_on'] );
    }
    return $cart_item;
}

// -----------------------------------------
// 4. Display custom input field value @ Cart
add_filter( 'woocommerce_get_item_data', 'bbloomer_product_add_on_display_cart', 10, 2 );
 
function bbloomer_product_add_on_display_cart( $data, $cart_item ) {
    if ( isset( $cart_item['dedication_text_add_on'] ) && $cart_item['dedication_text_add_on'] !== ""){
        $data[] = array(
            'name' => 'Dedicatoria',
            'value' => sanitize_text_field( $cart_item['dedication_text_add_on'] )
        );
    }
    return $data;
}
 
// -----------------------------------------
// 5. Save custom input field value into order item meta
 
add_action( 'woocommerce_add_order_item_meta', 'bbloomer_product_add_on_order_item_meta', 10, 2 );
 
function bbloomer_product_add_on_order_item_meta( $item_id, $values ) {
    if ( ! empty( $values['dedication_text_add_on'] ) ) {
        wc_add_order_item_meta( $item_id, 'Dedicatoria del cliente', $values['dedication_text_add_on'], true );
    }
}
 
// -----------------------------------------
// 6. Display custom input field value into order table
add_filter( 'woocommerce_order_item_product', 'bbloomer_product_add_on_display_order', 10, 2 );
 
function bbloomer_product_add_on_display_order( $cart_item, $order_item ){
    if( isset( $order_item['dedication_text_add_on'] ) ){
        $cart_item['dedication_text_add_on'] = $order_item['dedication_text_add_on'];
    }
    return $cart_item;
}
 
// -----------------------------------------
// 7. Display custom input field value into order emails
 
add_filter( 'woocommerce_email_order_meta_fields', 'bbloomer_product_add_on_display_emails' );
 
function bbloomer_product_add_on_display_emails( $fields ) { 
    $fields['dedication_text_add_on'] = 'Dedicatoria del cliente';
    return $fields; 
}

add_filter( 'woocommerce_cart_crosssell_ids', 'filter_woocommerce_cart_crosssell_ids', 10, 2 );
function filter_woocommerce_cart_crosssell_ids( $cross_sells, $cart ) {

    $product_ids_from_cats_ids = get_posts( array(
        'post_type'   => 'product',
        'numberposts' => -1,
        'post_status' => 'publish',
        'fields'      => 'ids',
        'tax_query'   => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => 'merchandising',
                'operator' => 'IN',
            )
        ),
    ) );

    $cross_sells = array_unique( $product_ids_from_cats_ids, SORT_REGULAR );

    return $cross_sells;
}

/*
add_action( 'template_redirect', 'bbloomer_add_gift_if_id_in_cart' );
function bbloomer_add_gift_if_id_in_cart() {

   if ( is_admin() ) return;
   if ( WC()->cart->is_empty() ) return;

   $product_gifted_id = 877;
  
   // see if gift id in cart
   $product_gifted_cart_id = WC()->cart->generate_cart_id( $product_gifted_id );
   $product_gifted_in_cart = WC()->cart->find_product_in_cart( $product_gifted_cart_id );
 
   // if not in cart remove gift, else add gift
    if ( $product_gifted_in_cart && count(WC()->cart->cart_contents) == 1 ) {
        WC()->cart->remove_cart_item( $product_gifted_in_cart );
    } else {
        if ( ! $product_gifted_in_cart ) {
            WC()->cart->add_to_cart( $product_gifted_id ); 
        } else {
            WC()->cart->remove_cart_item( $product_gifted_in_cart );
            WC()->cart->add_to_cart( $product_gifted_id );
        }
    }
    
}*/