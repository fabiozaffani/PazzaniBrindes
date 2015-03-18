</div> <!-- Fim do div#center-inner -->
</div> <!-- fim do div#center -->
</div> <!-- fim do div#wrapper -->
<div id="footer">

<div id="footer-inner" class="transparencia">

<div id="footer-inner-top" class="clearfix">

    <div class="widget-area first">
        <?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>

                <?php dynamic_sidebar( 'sidebar-3' ); ?>

        <?php endif; ?>
    </div><!-- #first .widget-area -->


    <div class="widget-area">
        <?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>

                <?php dynamic_sidebar( 'sidebar-4' ); ?>

        <?php endif; ?>
    </div><!-- #second .widget-area -->

    <div class="widget-area last">
        <?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>

            <?php dynamic_sidebar( 'sidebar-5' ); ?>

        <?php endif; ?>
    </div><!-- #third .widget-area -->


</div>

<div id="footer-inner-lower">

    <p class="footer-bottom"><?php echo $this->FooterText;?></p>

</div>

</div>

</div>

<!-- end footer -->
<?php wp_footer(); ?>

</body>
</html>