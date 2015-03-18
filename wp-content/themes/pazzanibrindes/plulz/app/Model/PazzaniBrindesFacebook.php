<?php

if (!class_exists('PazzaniBrindesFacebook'))
{
    class PazzaniBrindesFacebook extends PlulzFacebookAbstract
    {
        public function __construct()
        {
            $this->_name = 'pazzanibrindes_facebook';

            parent::__construct();
        }
    }
}
?>