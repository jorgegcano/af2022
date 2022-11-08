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

                ?>

                <div class='product-allergens-details' product-id='<?php echo the_ID(); ?>'>

                    <div class="image-container"> <?php the_post_thumbnail(); ?> </div>
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