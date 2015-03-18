<div id="container">

    <h1 class="post-list-title round"><?php echo single_cat_title( '', false ); ?></h1>

<?php
            if( have_posts() ) :

                $i = 0;
                while( have_posts() ) :

                    the_post();

                    ( $i % 3 == 1) ? $zebra = 'middle' : $zebra = 'side';
?>
                    <div class="round post-list <?php echo $zebra;?>">

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

<?php               $i++;

                endwhile;

            else:
?>
				<div id="post" class="transparencia clearfix">

                    <header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nada Encontrado', $this->_name ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Desculpe, mas a sua busca não retornou nenhum resultado. Tente novamente com outras palavras-chave.', $this->_name ); ?></p>
                        <?php $this->includeShared('SearchForm.php'); ?>
					</div><!-- .entry-content -->

				</div><!-- #post -->
<?php       endif;

            $this->navigation();
?>

</div>