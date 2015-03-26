<div id="container">
<?php
        if( have_posts() ) :
            $i = 0;
            while( have_posts() ) :

                the_post();
?>
                <div class="round post-list">

                    <div class="post-list-img">

                        <a href="<?php the_permalink();?>" title="<?php the_title();?>">
                            <?php the_post_thumbnail('list-thumb'); ?>
                        </a>

                    </div>

                    <div class="post-list-entry">

                        <h3><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title(); ?></a></h3>
                        <?php the_excerpt(); ?>
                        <a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
                            <?php _e('Veja Mais', $this->_name ); ?>
                        </a>
                    </div>

                </div>
<?php
                $i++;
            endwhile;
        endif;


?>

</div>
