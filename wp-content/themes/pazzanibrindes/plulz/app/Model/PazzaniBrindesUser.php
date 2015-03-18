<?php
/**
 * This is a abstract class that when the plugin need to use all the admin integration functionality it must extends it
 * and declares all the variables / methods inside it
 *
 * The advantage is that everything else is easier since there are many pre built functions that help manage the Wordpress Admin
 * Panel
 *
 * CLASS OVERVIEW
 *
 * This class should be extended whenever we want to create a Theme or Plugin since it contains many helpfull
 * methods and variables in order to make an easy integration with the wordpress CMS
 *
 */
// Make sure there is no bizarre coincidence of someone creating a class with the exactly same name of this plugin
if ( !class_exists("PazzaniBrindesUser") )
{
    class PazzaniBrindesUser extends PlulzUserAbstract
    {
        /**
         * Origens do cliente
         * @var array
         */
        protected $_origem;

        /**
         * Hold custom contact user fields
         * @var array
         */
        protected $_contactFields;

        public function __construct()
        {
            // Customer role
            add_role('cliente', __( 'Cliente' ), array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false
            ));

            $this->_name  = 'pazzanibrindes_usuario';

            $this->_contactFields = array(
                'telefone' => __('Telefone', $this->_name)
            );

            $this->_origem = array(
                'Site' => 'Site',  'Contato' => 'Contato', 'Telefone' => 'Telefone'
            );

            parent::__construct();
        }

        /**
         * Devolve os diferentes tipos de origens do cliente
         * @return array
         */
        public function getOrigens()
        {
            return $this->_origem;
        }

        /**
         * Creates a new user in the db if it already not exists
         * @param $data
         * @return int $user_id
         * @todo bug talvez no userExists, verificar com calma, deveria retornar true ao criar user
         */
        public function createCliente( $data )
        {
            if( !$this->validateUserData($data) )
                return false;

            $userExists = $this->userExists( $data['email'] );

            // User dont exist, lets register it, else, do nothing
            if ( !$userExists )
            {
                $nomeCompleto   =   ucwords(strtolower($data['nome']));
                $telefone       =   $data['telefone'];

                $userID = wp_create_user( $data['email'], '1q2w3e', $data['email'] );

                $nome = explode( " ", $nomeCompleto );

                $first_name = ucfirst(strtolower($nome[0]));

                unset($nome[0]);

                if (isset($nome[1]))
                    $last_name = ucfirst(strtolower(implode(" ", $nome)));
                else
                    $last_name = '';

                // Lets update the nicename

                $userdata = array(
                    'ID'            =>  $userID,
                    'user_nicename' =>  $nomeCompleto,
                    'display_name'  =>  $first_name
                );

                wp_update_user($userdata);

                // Now lets update the user meta information
                update_user_meta( $userID, 'first_name', $first_name );
                update_user_meta( $userID, 'last_name', $last_name );
                update_user_meta( $userID, 'telefone', $telefone );
                update_user_meta( $userID, 'show_admin_bar_front', 'false' );

                $user = new WP_User( $userID );

                // Add default user role to cliente, not subscriber
                $user->set_role('cliente');
            }

            return $userExists;
        }
    }
}