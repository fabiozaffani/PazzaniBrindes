<div id="sidebar" class="widget-area round">

    <!-- Show Categories -->
    <div class="widget">
        <h3 class="widget-title">Categorias</h3>
        <ul id="categorias">
<?php       $args = array(
                'orderby'            => 'name',
                'order'              => 'ASC',
                'show_last_update'   => 0,
                'style'              => 'list',
                'show_count'         => 0,
                'hide_empty'         => 0,
                'use_desc_for_title' => 1,
                'child_of'           => 0,
                'hierarchical'       => true,
                'title_li'           => '',
                'show_option_none'   => __('Nenhuma Categoria'),
                'number'             => NULL,
                'echo'               => 1,
                'depth'              => 0,
                'current_category'   => 1,
                'pad_counts'         => 0,
                'taxonomy'           => 'tipo'
            );

            wp_list_categories($args); ?>

        </ul>
    </div>

    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' );   ?>
	<?php endif; ?>

    <?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-2' );   ?>
	<?php endif; ?>

</div>