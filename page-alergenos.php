    <?php 
        get_header(); 
        the_content(); 
    ?>
        <div style="margin: 30px 0 40px;" class="site-content shop-content-area col-lg-12 col-12 col-md-12 description-area-before content-with-products">
            <div class="d-flex flex-wrap">
            <?php $args = array(
                'post_type'=>'product',
                'posts_per_page' => -1);

            $productos = new WP_Query($args);

            while($productos->have_posts()) : $productos->the_post();
                //eliminamos los donuts individuales de la lista de alérgenos.
                $categories = get_the_terms($productos->post->ID, 'product_cat');
                $not_suitable_categories = count($categories) == 1 && ($categories[0]->slug == 'donuts' || $categories[0]->slug == 'merchandising');
                $not_in_stock_products = $productos->post->post_status == 'private';
                if  ($not_suitable_categories || $not_in_stock_products) {
                    continue;
                }
                //
                ?>

                <div class='product-allergens-details' product-id='<?php echo the_ID(); ?>'>

                    <div class="image-container"> <?php the_post_thumbnail('thumbnail'); ?> </div>
                    <div class="data-container">
                        <h4><?php the_title(); ?> </h4>
                        <div>
                            <ul>
                            <?php
                            foreach(get_post_meta($productos->post->ID, "lista_alergenos") as $value) {
                                foreach($value as $alergeno) {
                                    echo "<li>".$alergeno."</li>";
                                }
                            }
                            ?>
                            </ul>
                        </div>
                        <?php
                        $atributos = $product->get_attributes(); ?>
                        <div>
                            <?php
                        if ($atributos !== null && isset($atributos['sin-lactosa'])) {
                            echo "<span class='attribute'><span style='color:red'>*</span> Disponible sin lactosa</span>";
                        } ?>
                        </div>
                    </div>
                </div>

                <?php endwhile; wp_reset_postdata(); ?>
                    </div>
        </div>
    <?php 
        get_footer(); 
    ?>