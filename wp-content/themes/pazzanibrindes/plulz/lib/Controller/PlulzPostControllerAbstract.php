<?php
/**
 *
 * Class responsible for returning the values do orçamento atual
 *
 *
 * startPostFront - all actions/filters related to a post_type in the front end
 * startPostBack - all actions/filters related to a post_type in the admin panel
 */

if (!class_exists('PlulzPostControllerAbstract'))
{
    abstract class PlulzPostControllerAbstract extends PlulzControllerAbstract
    {
        /**
         * Holds current post object
         * @var object
         */
        protected $_post;

        /**
         * Holds current post type name
         * @var string
         */
        protected $_postType;

        /**
         * CSS files to be applied on the metaboxes
         * @var
         */
        protected $_metaboxCSS;

        /**
         * Prevents the nonce to be send more than one time on any given post type admin page
         * @var bool
         */
        protected $_nonceAlreadySend = 0;

        public function __construct()
        {
            $this->_name    =   $this->_post->getName();

            $this->_postType=   $this->_post->getPostType();

            $this->setAction( 'init', 'register' );

            parent::__construct();
        }

        public function startAdmin()
        {
            if (self::getPostType() == $this->_postType)
            {
                $this->setAction( 'admin_enqueue_scripts', 'adminAssets' );
                $this->setAction( 'after_setup_theme', 'startPostBack' );
            }

        }

        public function startFrontEnd()
        {
            parent::startFrontEnd();

            if (self::getPostType() == $this->_postType)
                $this->setAction( 'wp', 'startPostFront' );
        }

        public function startPostBack()
        {
            // 2 params, $id and $post_data
            $this->setAction('save_post', 'handlePost');
        }

        public function init()
        {
            parent::init();

            if ( empty($this->_post) )
                $this->PlulzNotices->addError($this->_name, __('Empty post') );

            if ( empty($this->_postType) )
                $this->PlulzNotices->addError($this->_name, __('Empty _postType') );
        }

        /**
         * This method is to circumvent the need to know the post type with wordpress that does not make
         * life easier in this matter ( you never know what info you will have to find the post type
         *
         * @static
         * @return bool|string
         */
        public static function getPostType()
        {
            global $post;

            $postType  =   PlulzTools::getValue('post_type');
            $localPost =   PlulzTools::getValue('post');

            if ( $postType )
                return $postType;
            else if ( $localPost )
            {
                if(is_numeric($localPost))
                    return get_post_type( $localPost );
            }
            else if ( isset($post) )
                return $post->post_type;
            else
                return false;
        }

        /**
         * Output nonce fields if they are not already sended
         * @return bool
         */
        public function outputNonce()
        {
            if (!$this->_nonceAlreadySend)
            {
                wp_nonce_field( -1, $this->_nonce );
                $this->_nonceAlreadySend = 1;
                return true;
            }

            return false;
        }

        /**
         * Validates the current request, check if the permissions and the nonce are all correct
         * @param $post_id
         * @return bool
         */
        public function validateRequest( $post_id )
        {
            // Prevents this function from running twice (once for post, other for revision)
            if (wp_is_post_revision($post_id))
                return;

             // verify if this is an auto save routine.
            // If it is our form has not been submitted, so we dont want to do anything
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return;

            // verify this came from the our screen and with proper authorization,
            // because save_post can be triggered at other times
            if ( !isset($_POST[$this->_nonce]) )
                return false;

            if ( !wp_verify_nonce( $_POST[$this->_nonce] ) )
                return false;

            // Check permissions
            if ( 'page' == self::getPostType() )
            {
                if ( !current_user_can( 'edit_page', $post_id ) )
                {
                    $this->PlulzNotices->addError($this->_name, __('User can\'t edit this page', $this->_name) );
                    return false;
                }

            }
            else
            {
                if ( !current_user_can( 'edit_post', $post_id ) )
                {
                    $this->PlulzNotices->addError($this->_name, __('User can\'t edit this post', $this->_name) );
                    return false;
                }
            }

            return true;
        }

        /**
         * Default implementation for the method responsible for saving our custom metaboxes data
         *
         * Must be overriden in children class if want to validate the fields before saving on the db
         * or any other thing
         *
         * @param $id
         * @param $data
         * @return bool
         */
        public function handlePost( $id )
        {
            if( !$this->validateRequest( $id ) )
                return false;

            // OK, we're authenticated: we need to find and save the data
            $customData = $_POST[$this->_name];

            $this->_post->id = $id;
            return $this->_post->savePostCustomData($customData);
        }

        /**
         * Method that register the new post types in wordpress if the post is not from the default ones
         * Default post list: 'post', 'page', 'attachment', 'revisions', 'nav_menu_item'
         * @return void
         * @hook init
         */
        public function register()
        {
            $this->_post->register();

            // The contents for the custom columns of the posts types
            if ( self::getPostType() == $this->_postType )
            {
                $this->setAction( 'load-edit.php', 'startPostsEditPage' );

                $this->setAction( 'manage_posts_custom_column', array('_post', 'customPostTypeColumnsContent'), 10, 2 );
            }

        }

        /**
         * Load  all methods related to the edit.php page
         * @return void
         * @hook load-(page).php
         */
        public function startPostsEditPage()
        {
             // Append extra filter options for the custom post types
            $this->setAction( "restrict_manage_posts", array('_post', 'customPostTypeColumnsFilterRestriction') );

            // Checks the query being performed to search for the custom filters
            $this->setFilter( "parse_query", "customPostTypeMetadaFilter" );

            // Definir as custom columns a ser exibidas
            $this->setFilter( "manage_" . $this->_postType . "_posts_columns", array('_post', 'customPostTypeColumns') );

            // Define quais das columns adicionadas poderão ser  "sortidas"
            $this->setFilter( "manage_edit-" . $this->_postType . "_sortable_columns", array('_post', 'customPostTypeSortableColumns') );
        }

        /**
         * Gets the searched params, sees if it is a custom field and if it is serialized or not and deal with
         * all that
         * @param $query
         * @return
         *
         * @internal param $ parse_query
         * @hook parse_query
         */
        public function customPostTypeMetadaFilter( $query )
        {
            $serializedFields = $this->_post->getSerializedFields();

            // If no serialized fields were defined, just leave
            if ( empty($serializedFields) )
                return $query;

            global $pagenow;

            if ( !is_admin() || $pagenow != 'edit.php' )
                return $query;

            $postMetaValuefilter   =   PlulzTools::getValue('post_meta_value_filter_value', '');
            $postOrderBy           =   PlulzTools::getValue('orderby', '');

            // Executa a busca por posts baseado nos filtros passados
            if ( !empty($postMetaValuefilter) )
            {
                // Possible mysql manipulation before the query
                // (posts_where, posts_join, posts_groupby, posts_orderby, posts_distinct, posts_fields,
                // post_limits, posts_where_paged, posts_join_paged, and posts_request

                $this->setFilter( 'posts_join', array('_post', 'serializedSearchJoin') );
                $this->setFilter( 'posts_where',  array('_post', 'serializedSearchWhere') );
            }

            // Ordenar os resultados obtidos da busca
            if ( !empty($postOrderBy) )
            {
                // Lets manipulate the orderby only if it is a serialized field, otherwise degrades to wp default
                if (PlulzTools::inMultidimensionalArray($postOrderBy, $this->_post->_serializedFields))
                {
                    $this->setFilter( 'the_posts', array('_post', 'serializedQueryPostData') );
                }
                else
                    return $query;
            }
        }
    }
}
