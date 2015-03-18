<?php

if(!class_exists('PlulzPost'))
{
    class PlulzPost extends PlulzObjectAbstract
    {
        /**
         * Holds the informationa about wordpress default post data
         * @var array
         */
        protected $_defaultPostTypes = array(
            'post', 'page', 'attachment', 'revisions', 'nav_menu_item'
        );

        /**
         * Holds the default wordpress post status
         * @var array
         */
        protected $_defaultPostStatus = array(
            'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'
        );

        /**
         * Hols which data are in serialized fields
         * @var array
         */
        protected $_serializedFields;

        /**
         * Holds all the metada from the current post type
         * @var
         */
        protected $_metadata;

        /**
         * Holds the information about the custom taxonomys to be created and used with the current post type
         * @var array
         */
        protected $_customTaxonomies;

        /**
         * Configurations of the new post to be added
         * @var array
         */
        protected $_postData;

        protected $_postType;

        public $id;

        public $PlulzTaxnomy;

        public function __construct( $post_id = '' )
        {
            $this->id = $post_id;

            parent::__construct();
        }

        public function init()
        {
            if ( empty($this->_postData) )
                $this->_PlulzNotices->addError($this->_name, __('Empty var _postData') );

            parent::init();
        }

        /**
         * Register custom post type
         * @return void
         */
        public function register()
        {
            // Check if the current post is already registered or not
            if ( !in_array($this->_postType, $this->_defaultPostTypes) )
                register_post_type( $this->_postType, $this->_postData['config'] );
        }

        /**
         * Save all the custom metadata from the current post
         * @param $data
         * @return bool
         */
        public function savePostCustomData($data)
        {
            return update_post_meta($this->id, $this->_name, $data);
        }

        public function getMetaExcerpt()
        {
            $post = get_post($this->id);

            // try to use the excerpt as description meta
            $meta = strip_tags($post->post_excerpt);

            // if there is no excerpt, lets automatically create one based on the content
            if (empty($meta))
            {
                $meta = strip_tags($post->post_content);
                $meta = substr($meta, 0, 130);
            }

            return $meta;
        }
        /**
         * Return the post back to the draft state, this method is intended to be used when
         * there is validation errors in the post data
         *
         * have an optinal parameter to be used when the caller is append
         * to pre_post_update or save_post wordpress hooks
         * @param $caller
         */
        public function savePostAsDraft($caller)
        {
            if (isset($caller))
                remove_action('save_post', $caller);

            // change the post status back to draft if there is errors
            $post = get_post($this->id, 'ARRAY_A');
            $post['post_status'] = 'draft';
            wp_update_post($post);

            if (isset($caller))
                add_action('save_post', $caller);
        }

        /**
         * Get all post metadata information, if the metadata is not set yet
         * @param bool $type
         *
         * @internal param $prod_id
         * @return void
         */
        public function fetchPostMetadata( $type = false )
        {
            $metadata = get_post_meta( $this->id, $this->_name, $type);
            $this->_metadata   =   isset($metadata[0]) ? $metadata[0] : array();
        }

        /**
         * Returns the post metadata information
         * @param string $name
         * @return mixed
         */
        public function getMetadata($name = '')
        {
            if (empty($name))
                return $this->_metadata;
            else
                return $this->_metadata[$name];
        }

        public function getPostType()
        {
            return $this->_postType;
        }

        /**
         * Create/override the columns to be listed in the edit.php page of the posts
         * @param $cols
         * @return array
         */
        public function customPostTypeColumns( $cols )
        {
            $colunas = $this->_postData['editPage']['customColumns'];

            if (empty($colunas))
                return $cols;

            foreach($colunas as $coluna => $valor)
                $cols[$coluna] = $valor;

            return $cols;
        }

        /**
         * Filters the columns that allows sorting
         * @param $cols
         * @return array
         */
        public function customPostTypeSortableColumns( $cols )
        {
            $colunas = $this->_postData['editPage']['sortableColumns'];

            if (empty($colunas))
                return $cols;

            foreach ($colunas as $coluna => $valor)
                $cols[$coluna]  = $valor;

            return $cols;
        }

        /*********************** SEARCH REALTED STUFFS *************************************/

        /**
         * Gets the results from the query and insert all the metadata related to that post and sort it
         * according to the data choose
         * @param $query
         * @return array|stdObject
         * @hook the_posts
         */
        public function serializedQueryPostData( $query )
        {
            foreach($query as $item)
            {
                $this->id = $item->ID;
                $this->fetchPostMetadata();

                foreach ($this->_serializedFields as $mainFieldKey => $mainFieldValue)
                {
                    if (is_array($mainFieldValue))
                    {
                        foreach($mainFieldValue as $innerField)
                        {
                            $item->$innerField = isset($this->_metadata[$mainFieldKey][$innerField]) ? $this->_metadata[$mainFieldKey][$innerField] : "-";
                        }
                    }
                    else
                    {
                        $item->$mainFieldValue = isset($this->_metadata[$mainFieldValue]) ? $this->_metadata[$mainFieldValue] : "-";
                    }
                }
            }

            usort($query, array(&$this, 'compareObject'));

            return $query;
        }


        /**
         * Outputs a input text field that the user can input data to be run over serialized fields
         * in the database for searching
         * @return void
         * @hook restrict_manage_posts
         */
        public function customPostTypeColumnsFilterRestriction()
        {
            $post_meta_value_filter_value   =   PlulzTools::getValue('post_meta_value_filter_value', '');

            $output = "<label for='metadata_filter'>Informações</label>
                        <input id='metadata_filter' type='text' name='post_meta_value_filter_value' value='{$post_meta_value_filter_value}' />";

            echo $output;
        }

        /**
         * Changes wordpress default SQL statement so it will also include the wp_postmeta table
         * @param $query
         * @return string
         * @hook posts_join
         */
        public function serializedSearchJoin( $query )
        {
            global $wpdb;

            $query .= "LEFT JOIN {$wpdb->postmeta}
                       ON {$wpdb->postmeta}.post_id = wp_posts.ID";

            return $query;
        }

        /**
         * Changes wordpress default SQL statement to search in serialized fields in the wp_postmeta table
         * @param $query
         * @return string
         * @hook posts_where
         */
        public function serializedSearchWhere( $query )
        {
            global $wpdb;

            $post_meta_value_filter_value   =   PlulzTools::getValue('post_meta_value_filter_value', '');

            $query .= " AND {$wpdb->postmeta}.meta_key = '{$this->_name}'
                        AND {$wpdb->postmeta}.meta_value LIKE '%{$post_meta_value_filter_value}%'";

            return $query;
        }

        /**
         * Try to get the post status from the a post, if no id is provided will try to get from
         * the current $_POST global variable
         * @return mixed (string or false)
         */
        public function getCurrentPostOriginalStatus()
        {
            if (!empty($this->id))
                return get_post_status( $this->id );

            return PlulzTools::getValue('original_post_status', false);

        }

        /**
         * Return the new post post status, or false if not availabe
         * @return mixed (string or false)
         */
        public function getCurrentPostNewStatus()
        {
            return PlulzTools::getValue('post_status', false);
        }


        /**
         * This function gets all elements defined in the serializedFields and will try to output
         * their content in their respective columns on the edit.php page
         * @param $column
         * @param $post_id
         * @return void
         * @hook manage_posts_custom_columns
         */
        public function customPostTypeColumnsContent( $column, $post_id )
        {
            /*
             *  Campos também podem ser obtidos através do exemplo comentado abaixo
             *
             *  acredito que o código comentado consuma bem menos recursos que oa tual montado
             *  pois evita o loop no metadata duas vezes e uma chamada no db a menos
             *
             *  entretanto ele só seria chamado para os loops com algum search filter
             *
             *  global $wp_query;
             *  $posts = $wp_query->get_posts();
             *
             */
            $this->id = $post_id;
            $this->fetchPostMetadata();

            foreach ($this->_serializedFields as $mainFieldKey => $mainFieldValue)
            {
                if (is_array($mainFieldValue))
                {
                    foreach($mainFieldValue as $innerField)
                    {
                        $output = isset($this->_metadata[$mainFieldKey][$innerField]) ? $this->_metadata[$mainFieldKey][$innerField] :  "-";
                        if ($innerField == $column)
                            echo $output;
                    }
                }
                else
                {
                    $output = isset($this->_metadata[$mainFieldValue]) ? $this->_metadata[$mainFieldValue] : "-";
                    if ($mainFieldValue == $column)
                        echo $output;
                }
            }
        }

        /**
         * Default custom sorting for the WP_Query object, allows us to sort the WP_Query by the returned
         * fields, be it from the post or the postmeta db table
         *
         * @static
         * @param $a
         * @param $b
         * @return int
         */
        public function compareObject($a, $b)
        {
            $orderby    = PlulzTools::getValue('orderby', '');
            $order      = PlulzTools::getValue('order', 'asc'); // defaults to asc

            $field_a = $a->$orderby;
            $field_b = $b->$orderby;

            if (is_numeric($field_a) && is_numeric($field_b))
            {
                if ( $field_a < $field_b )
                    return $order == 'asc' ? -1 : 1;

                if ( $field_a > $field_b )
                    return $order == 'asc' ? 1 : -1;

                return 0; // equality
            }
            else
            {
                // Return < 0 SE str1 < str2; Return > 0 SE str1 > str2, Return = 0 str1 = str2.
                $result = strcmp($field_a, $field_b);

                if ($order == 'asc' && $result != 0)
                    return $result;
                else if ($order == 'desc' && $result !=0)
                    return $result * -1;
            }
        }

        public function getSerializedFields()
        {
            return $this->_serializedFields;
        }
    }
}

?>