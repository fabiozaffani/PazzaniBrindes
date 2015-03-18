<div id="container">

<?php
    if (have_posts()) :
?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <p id="breadcrumb">
                <a href="<?php echo $this->HomeLink;?>">Inicio</a> > <?= $this->ListaTipo; ?> > <?php the_title(); ?>
            </p>

            <h1 class="product_title page-title"><?php the_title(); ?></h1>

			    <div id="imagem-post">
                    <a href="<?php echo $this->ProdutoThumbnailLink; ?>" rel="lightbox-produto">
                        <?php the_post_thumbnail('post-thumb'); ?>
                    </a>
                </div>

                <div id="content">

                    <?php the_content(); ?>

                </div>

            </div>

		</div>
<?php
    endif;
?>

</div>