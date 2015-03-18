<?php

if (!class_exists('PazzaniBrindesAdmin'))
{
    class PazzaniBrindesAdmin extends PlulzAdmin
    {
        public function __construct()
        {
            parent::__construct('pazzanibrindes_admin');

        }
        public function getFooterText()
        {
            $option = get_option($this->_name);

            return $option['footerText'];
        }

        public function getAtendimento()
        {
            $option = get_option($this->_name);

            return $option['atendimento'];
        }

        public function getContato()
        {
            $option = get_option($this->_name);

            return $option['contato'];
        }

        public function getMetaDescription()
        {
            $option = get_option($this->_name);
            return $option['metaDescription'];
        }
    }
}

?>