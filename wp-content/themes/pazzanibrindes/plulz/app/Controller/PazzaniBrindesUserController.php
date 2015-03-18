<?php

if (!class_exists('PazzaniBrindesUserController'))
{

    class PazzaniBrindesUserController extends PlulzControllerAbstract
    {
        public $PlulzUser;

        public function __construct()
        {
            $this->PlulzUser=   new PazzaniBrindesUser();

            $this->_name    =   $this->PlulzUser->getName();

            $this->_nonce   =   'pazzanibrindes_usuario_nonce';

            parent::__construct();

        }

        public function startAdmin()
        {
            parent::startAdmin();

            $this->setFilter( 'user_contactmethods', array('PlulzUser', 'addUserContactFields') );
        }

        public function corrigirUserNicename()
        {
            return;
            $users = get_users();
            foreach($users as $user)
            {
                $nicename = $user->display_name;

                $novoNicename = explode("-", $nicename);

                $userdata = array(
                    'ID'            =>  $user->ID,
                    'display_name'  =>  $novoNicename[0]
                );

                wp_update_user($userdata);
            }
        }
        
        /**
         * Metodo para transferencia da bases de dados
         * @return mixed
         */
      /*  public function transferenciaCorrigirUsuarios()
        {
            return;
            set_time_limit( 4000 );

            // ACERTAR OS TELEFONES

            $clientesMetaCSV = fopen($this->_appControllerDir . 'backup/users_meta.csv', "r");

            $header = fgetcsv($clientesMetaCSV, 1000, ";");
            $clientesMeta = array();
            while ($row = fgetcsv($clientesMetaCSV, 1000, ";") )
            {
                $arr = array();

                foreach ($header as $i => $col)
                {
                    $arr[$col] = $row[$i];
                }

                $clientesMeta[] = $arr;
            }

            $clientesTelefones = array();
            foreach($clientesMeta as $meta)
            {
                if ($meta['meta_key'] == 'user_address_info' )
                {
                    $telefone = unserialize(unserialize($meta['meta_value']));

                    if (empty($telefone['user_telefone']))
                        $telefone['user_telefone'] = '(00)0000-0000';

                    $clientesTelefones[$meta['user_id']] = $telefone['user_telefone'];
                }
            }

            $users = get_users();

            foreach($users as $user)
            {
                $userID = $user->ID;
                $nomeCompleto = $user->user_nicename;

                $nome = explode( " ", $nomeCompleto );

                $first_name = ucfirst(strtolower($nome[0]));

                unset($nome[0]);

                if (isset($nome[1]))
                    $last_name = ucfirst(strtolower(implode(" ", $nome)));
                else
                    $last_name = '';

                $userdata = array(
                    'ID'            =>  $userID,
                    'user_nicename' =>  $nomeCompleto,
                    'display_name'  =>  $first_name
                );

                wp_update_user($userdata);

                update_user_meta( $userID, 'first_name', $first_name );
                update_user_meta( $userID, 'last_name', $last_name );
                update_user_meta( $userID, 'nickname', $first_name );
                update_user_meta( $userID, 'show_admin_bar_front', 'false' );
                update_user_meta( $userID, 'description', '' );
                update_user_meta( $userID, 'rich_editing', 'true' );
                update_user_meta( $userID, 'comment_shortcuts', 'false' );
                update_user_meta( $userID, 'admin_color', 'fresh' );
                update_user_meta( $userID, 'use_ssl', 0 );
                update_user_meta( $userID, 'show_admin_bar_admin', 'false' );
                update_user_meta( $userID, 'wp_user_level', 0 );
                update_user_meta( $userID, 'telefone', $clientesTelefones[$userID] );

                $user = new WP_user( $userID );
                $user->add_role('cliente');
            }

            die('users done');
        }*/
    }
}

?>