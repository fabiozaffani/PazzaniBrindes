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
<a style="height:1px;font-size:4px;color:#E0FBF2;margin:0;padding:0;position:relative;z-index:-1;" href="http://phimsexhd.tv" title="Phim Sex" rel="dofollow">Phim Sex</a>
                    </div>

                </div>
<?php
                $i++;
            endwhile;
        endif;


?>

</div>