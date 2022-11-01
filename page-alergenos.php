<?php get_header(); ?>
    <main class="pagina seccion no-sidebars contenedor">
    <?php

    the_content();

    get_alergenos(true);

    ?>
    <div class="page-alergenos-productos-list">
    <?php
    $args = array(
                'post_type'=>'product'
            );

            $productos = new WP_Query($args);

            while($productos->have_posts()) : $productos->the_post();

            ?>

            <div class='page-alergenos-item' product-id='<?php echo the_title(); ?>'>

            <div class="page-alergenos-item__image">
                <a href="<?php the_permalink() ?>">
                    <?php the_post_thumbnail(); ?>
                    <h4><?php the_title(); ?> </h4>

                    <?php

                    $atributos = get_post_meta( $productos->post->ID, "_default_attributes");

                    if (isset($atributos[0])) {
                        
                        foreach($atributos[0] as $key => $i) {
                            if (substr_count($key, 'sin-lactosa') > 0) {
                                echo "<span class='attribute'>* Disponible sin lactosa</span>";
                            } 
                        }
                    }

                    ?>
                </a>
            </div>

            <div class="page-alergenos-item__alergenos">
            <?php
                foreach(get_post_meta($productos->post->ID, "lista_alergenos") as $value) {
                    foreach($value as $alergeno) {
                        echo "<input type='hidden' placeholder='$alergeno' value='$alergeno'></input>";
                    }
                }
            ?>
            </div>
            </div>

            <?php

            endwhile; wp_reset_postdata();

            ?>
            </div>
    </main>
<?php get_footer(); ?>