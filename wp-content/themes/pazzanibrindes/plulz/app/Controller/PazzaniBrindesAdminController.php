<?php
/*
 * The class with the admin implementation for configurations and stuff when needed
 *
 *
 * CLASS OVERVIEW
 *
 *
 * It also is responsible for managing the default implementations that comes with wordpressl ike the posts and pages
 * the excerpts, admin configuration and custom page creation.
 *
 */

// ** Avoid db bloating with milions of unnecessary revisions ** //
if (!defined('WP_POST_REVISIONS'))
    define('WP_POST_REVISIONS', 5);

if (!class_exists('PazzaniBrindesAdminController'))
{
    class PazzaniBrindesAdminController extends PlulzAdminControllerAbstract
    {
        protected $_facebook;

        protected $_carrinho;

        protected $_metas;

        public function __construct()
        {
            $this->_facebook=   new PazzaniBrindesFacebook();

            $this->_carrinho=   new PazzaniBrindesCarrinho();

            $this->_metas   =   new PazzaniBrindesMetas();

            $this->_admin   =   new PazzaniBrindesAdmin();

            $this->_nonce   =   'pazzanibrindes_admin_nonce';

            parent::__construct();
        }

        public function init()
        {
            $this->_menuPages        =  array(
                0   =>  array(
                    'page_title'    =>  'Pazzani Brindes',
                    'menu_title'    =>  'Pazzani Brindes',
                    'capability'    =>  'administrator',
                    'menu_slug'     =>  $this->_admin->getName(),
                    'icon_url'      =>  $this->_assets . 'img/tiny-logo-plulz.png',
                    'position'      =>  '',
                    'callback'      =>  array( &$this, 'pageMain' ),
                    'submenus'      =>  array(
                        0               =>  array(
                            'page_title'    =>  'Facebook',
                            'menu_title'    =>  'Facebook',
                            'capability'    =>  'administrator',
                            'menu_slug'     =>  $this->_facebook->getName(),
                            'callback'      =>  array( &$this, 'pageFacebook' )
                        ),
                        1               =>  array(
                            'page_title'    =>  'Orçamento',
                            'menu_title'    =>  'Orçamento',
                            'capability'    =>  'administrator',
                            'menu_slug'     =>  $this->_carrinho->getName(),
                            'callback'      =>  array( &$this, 'pageCarrinho')
                        ),
                        2               =>  array(
                            'page_title'    =>  'Metas',
                            'menu_title'    =>  'Metas',
                            'capability'    =>  'administrator',
                            'menu_slug'     =>  $this->_metas->getName(),
                            'callback'      =>  array( &$this, 'pageMetas')
                        )
                    )
                )
            );

            // The extra buttons to be added on the tinyMCE edittor
            $this->_mceButtons   =   array(
                0   =>  array(
                   'id'        => 'like',
                   'separator' =>  '|'
                )
            );

            parent::init();
        }

        public function pageMain()
        {
            $name = PlulzTools::getValue('page', $this->_name);

            $this->set('data', get_option($name) );
            $this->set('group' , $name . $this->groupSuffix);
            $this->set('domain', PlulzAdmin::$DOMAIN['www']);
            $this->includeTemplate('Main', 'Admin');
        }

        public function pageCarrinho()
        {
            $name = PlulzTools::getValue('page', $this->_name);

            $this->set('data', get_option($name) );
            $this->set('group' , $name . $this->groupSuffix);
            $this->set('domain', PlulzAdmin::$DOMAIN['www']);
            $this->includeTemplate('Carrinho', 'Admin');
        }

        public function pageFacebook()
        {
            $name = PlulzTools::getValue('page', $this->_name);

            $this->set('data', get_option($name) );
            $this->set('group' , $name . $this->groupSuffix);
            $this->set('domain', PlulzAdmin::$DOMAIN['www']);
            $this->includeTemplate('Facebook', 'Admin');
        }

        public function pageMetas()
        {
            $name = PlulzTools::getValue('page', $this->_name);

            $this->set('data',  get_option($name) );
            $this->set('group', $name . $this->groupSuffix);
            $this->set('domain',PlulzAdmin::$DOMAIN['www']);
            $this->includeTemplate('Metas', 'Admin');
        }
    }
}
