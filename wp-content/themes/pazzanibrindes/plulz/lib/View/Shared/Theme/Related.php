<h2 id="relatedTitle"><?php _e('Produtos Relacionados', $this->_name); ?></h2>
<ul id="related">
<?php

    while($this->RelatedPosts->have_posts()) :

        $this->RelatedPosts->the_post();
?>
        <li>
            <a href="<?php the_permalink();?>" target="_self">
<?php

                echo get_the_post_thumbnail(get_the_ID(), 'list-thumb');
?>
                <h4><?php the_title(); ?></h4>
            </a>
        </li>
<?php
    endwhile;
?>
</ul>