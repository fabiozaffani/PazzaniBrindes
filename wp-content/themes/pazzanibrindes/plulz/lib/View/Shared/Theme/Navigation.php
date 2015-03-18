<?php
        if ( $this->MaxPages > 1 ) :
?>
            <ul id="navigation">

                <li class="previous">
                    <?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Anterior', $this->_name ) ); ?>
                </li>
<?php
                   foreach($this->Pages as $page) :

                       $link = $page['link'];
                       $name = $page['name'];
                       $current = $page['current'];

                       if ($current)
                           $class = ' class="current"';
                       else
                           $class = '';
?>
                        <li><a href="<?php echo $link; ?>"<?php echo $class ?> target="_self"><?php echo $name; ?></a></li>
<?php
                    endforeach;
?>
                <li class="next">
                    <?php previous_posts_link( __( 'Próximo <span class="meta-nav">&rarr;</span>', $this->_name ) ); ?>
                </li>

            </ul><!-- #nav-above -->
<?php
        endif;
?>