<?php

if (!class_exists('PazzaniBrindesFacebookController'))
{

    class PazzaniBrindesFacebookController extends PlulzControllerAbstract
    {
        public $PlulzFacebook;

        public function __construct()
        {
            $this->PlulzFacebook=   new PazzaniBrindesFacebook();

            $this->_name        =   $this->PlulzFacebook->getName();

            $this->_nonce       =   'pazzanibrindes_facebook_nonce';

            // Shortcodes
            $this->setShortCode( 'plulz_social_like', array('PlulzFacebook', 'socialLike' ) );

            parent::__construct();
        }

        public function startFrontEnd()
        {
            parent::startFrontEnd();

            $this->setAction( 'plulz_top', array('PlulzFacebook', 'addFbJS') );

            // og tags
            $this->setAction( 'wp_head',	array('PlulzFacebook', 'addOpenGraph') ); // og tag

            // fbLanguages
            $this->setAction( 'language_attributes', array('PlulzFacebook', 'languages') );
        }
    }
}

?>