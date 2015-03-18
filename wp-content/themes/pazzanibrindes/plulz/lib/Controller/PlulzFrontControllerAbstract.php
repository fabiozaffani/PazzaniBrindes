<?php

if(!class_exists('PlulzFrontController'))
{
    abstract class PlulzFrontControllerAbstract extends PlulzControllerAbstract
    {

        protected $searchString = 'lista/';

        protected $_postData;

        protected $_action;

        abstract function preLoad();

        public function __construct()
        {
            // Reescrever a busca
            $this->setFilter('search_rewrite_rules', 'changeSearchString');

            parent::__construct();
        }


        /**
         * Set the actions meant to run on the front end only
         * @return void
         */
        public function startFrontEnd()
        {
            parent::startFrontEnd();

            $this->setAction('template_redirect', 'searchRewrite');

            $this->setAction( 'template_redirect', 'preLoad');

            // Add the extra js
            $this->setAction( 'wp_print_styles', 'loadAssets' );

            $this->setAction( 'template_redirect', 'renderTemplate');

            // Front end notices
            $this->setAction( 'front_notices', 'frontMessage' );
        }

        /**
         * Muda o texto do search para um mais amigavel no SEO
         *
         * @param $search_rewrite
         * @return array
         */
        public function changeSearchString( $search_rewrite )
        {
            if( !is_array( $search_rewrite ) )
                return $search_rewrite;

            $new_array = array();

            foreach( $search_rewrite as $pattern => $_s_query_string )
                $new_array[ str_replace( 'search/', $this->searchString, $pattern ) ] = $_s_query_string;

            $search_rewrite = $new_array;

            unset( $new_array );

            return $search_rewrite;
        }


        /**
         * Tirando aquele padrao de URL feioso e deixando um decente
         */
        public function searchRewrite()
        {
            if ( is_search() && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/' ) === false && strpos( $_SERVER['REQUEST_URI'], $this->searchString ) === false ) {

                $link = remove_accents(str_replace( array( ' ', '%20' ),  array( '-', '-' ), get_query_var( 's' ) ));
                wp_redirect( home_url( $this->searchString . $link) );
                exit();
            }
        }

        /**
         * Carrega assets
         */
        public function loadAssets()
        {
            wp_deregister_script( 'jquery' );   // lets get jquery from google CDN

            wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
        }

        /**
         * Called on template, ideally should be created a do_action('front_notices')
         * to capture the front end notices
         *
         * @return void
         */
        public function frontMessage()
        {
            echo $this->PlulzNotices->showFrontNotices();
        }

        /**
         * Get all the data and mount the navigation bar according to the current page
         * Used as a shortcut to embed the page navigation
         * @return void
         */
        public function navigation()
        {

            global $paged, $wp_query;

            $pages = array();

            if ($wp_query->max_num_pages > 1)
            {
                for($i = 1; $i <= $wp_query->max_num_pages; $i++)
                {
                    if ($paged == $i)
                        $current = true;
                    else
                        $current = false;

                    $pages[] = array(
                        'link' => get_pagenum_link($i),
                        'name' => $i,
                        'current'=> $current
                    );
                }
            }

            $this->set('MaxPages', $wp_query->max_num_pages );
            $this->set('Pages', $pages);
            $this->includeThemeShared('Navigation');

        }

        /**
         * Chooses which template to run
         */
        public function renderTemplate()
        {
            if (is_search())
            {
                $this->search();
            }
            else if (is_home())
                $this->home();
            else if (is_404())
                $this->notFound();
            else if (is_page())
                $this->page();
            else if (is_single())
                $this->single();

            exit;
        }

        /**
         * Métodos de cada Página / Template do tema e suas respectivas variáveis
         */

        public function home()
        {
            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $this->includeTemplate('Home');

            $this->includeThemeShared('Footer');
        }

        public function single()
        {
            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $this->includeTemplate('Single');

            $this->includeThemeShared('Footer');
        }

        public function page()
        {

            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $this->includeTemplate('Page');

            $this->includeThemeShared('Footer');
        }

        public function notFound()
        {
            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $this->includeTemplate('404');

            $this->includeThemeShared('Footer');
        }

        public function search()
        {
            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $this->includeTemplate('Search');

            $this->includeThemeShared('Footer');
        }
    }
}
?>