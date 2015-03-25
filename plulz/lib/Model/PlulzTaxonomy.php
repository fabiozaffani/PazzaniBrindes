<?php

if (!class_exists('PlulzTaxonomy'))
{
    class PlulzTaxonomy extends PlulzObjectAbstract
    {
        protected $_name;

        protected $_postType;

        protected $_customTaxonomies;

        public function __construct($name, $postType, $taxonomies)
        {
            $this->_name = $name;
            $this->_postType = $postType;
            $this->_customTaxonomies = $taxonomies;

            $this->createCustomTaxonomies();

            parent::__construct();
        }

        public function createCustomTaxonomies()
        {
            foreach( $this->_customTaxonomies as $taxonomy )
            {
                register_taxonomy(  $taxonomy['name'],
                                    $this->_postType,
                                    $taxonomy['args']
                );
            }
        }

        public function createTaxonomyCustomField( $tag )
        {
           // Check for existing taxonomy meta for the term you're editing
            $termID = $tag->term_id; // Get the ID of the term you're editing

            $option = $this->_name . $termID;

            $metaValues = get_option( $option );

            $output = '';

            foreach ( $this->_customTaxonomies as $customTaxonomy )
            {
                if ( isset($customTaxonomy['extraFields']) && !empty( $customTaxonomy['extraFields']) )
                {
                    foreach ( $customTaxonomy['extraFields'] as $field )
                    {
                        $label          =   $field['label'];
                        $name           =   $field['name'];
                        $description    =   $field['description'];
                        $value          =   $metaValues[$name];

                        $output .= "
                            <tr class='form-field'>
                                <th scope='row' valign='top'>
                                    <label for='{$name}'>{$label}</label>
                                </th>
                                <td>
                                    <input type='text' name='{$name}' id='{$name}' value='{$value}'><br />
                                    <span class='description'>{$description}</span>
                                </td>
                            </tr>";

                    }
                }
            }
            echo $output;
        }

        /**
         * Method that saves the custom fields on the custom taxonomys type
         * @param $termID
         * @return void
         */
        public function saveTaxonomyCustomField( $termID )
        {
            if ( isset( $_POST ) )
            {
                $option = $this->_name . $termID;

                $term_meta = get_option( $option );
                foreach ( $this->_customTaxonomies as $customTaxonomy )
                {
                    if ( isset($customTaxonomy['extraFields']) && !empty( $customTaxonomy['extraFields']) )
                    {
                        foreach ( $customTaxonomy['extraFields'] as $field )
                        {
                            $name = $field['name'];

                            if ( isset($_POST[$name]) )
                                $term_meta[$name]   =   $_POST[$name];
                        }
                    }
                }

                //save the option array
                update_option( $option, $term_meta );
            }
        }

        /**
         * Pega todos os termos taxonomicos de um determinado post
         * @param $id
         * @param $taxonomy
         * @return mixed
         */
        public function getTaxonomyTermsCategoria( $id, $taxonomy )
        {
            return get_the_terms( $id, $taxonomy );
        }

        /**
         * Get taxonomy custom field value
         * @param $field_id
         * @return string
         */
        public function getCustomTaxanomyExtraFieldValue( $field_id )
        {
            $name = $this->_name . $field_id;

            return get_option( $name );
        }

    }
}