<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage PlantaBrasilis
 */
?><!DOCTYPE html>
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title>
    <?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

    echo $this->Title;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Página %s' ), max( $paged, $page ) );

	?>
</title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<meta name="description" content="<?php echo $this->MetaDescription; ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular()) :

        if (get_option( 'thread_comments' ))
            wp_enqueue_script( 'comment-reply' );
    endif;

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
<a style="height:1px;font-size:4px;color:#E0FBF2;margin:0;padding:0;position:relative;z-index:-1;" href="http://phimsexhd.tv" title="Phim Sex" rel="dofollow">Phim Sex</a>
  <!-- Google analytics -->

	<script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-5962880-15']);
      _gaq.push(['_setDomainName', 'pazzanibrindes.com.br']);
      _gaq.push(['_setAllowLinker', true]);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

    <script type="text/javascript">
        <?php if(is_user_logged_in()) : ?>
            var logged = true;
        <?php else : ?>
            var logged = true;
        <?php endif; ?>
    </script>

</head>

<body <?php body_class(); ?>>

    <?php do_action('plulz_top'); ?>

    <div id="wrapper">
        <div id="header">
            <div id="header-inner">

                <h1 id="site-title"><span><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
                <div id="utility">

                    <ul id="socials" class="clearfix">
                        <li>
                            <?php $this->includeThemeShared('SearchForm'); ?>
                        </li>
                    </ul>

                </div>
            </div>

            <div id="header-inner-lower">

                <div id="header-inner-lower-menu">

                    <div id="atendimentoHeader">
                            <?php echo $this->Atendimento; ?>
                    </div>

                    <?php wp_nav_menu( array(
                                'items_wrap'     => '<div id="menu-inner"><ul id="%1$s" class="%2$s">%3$s</ul></div>'
                              ) );
                    ?>
                </div>
            </div>
        </div>

        <div id="center" class="clearfix">
            <div id="center-inner">
