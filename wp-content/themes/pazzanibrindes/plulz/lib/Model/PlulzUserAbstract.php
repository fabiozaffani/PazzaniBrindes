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
if ( !class_exists("PlulzUserAbstract") )
{
    abstract class PlulzUserAbstract extends PlulzObjectAbstract
    {

        /**
         * Holds current user information
         * @var object WP_User
         */
        protected $_User;

        /**
         * Hold custom user profile fields
         * @var array
         */
        protected $_contactFields;


        public function getUserByID( $userID )
        {
            $this->_User = get_userdata( $userID );
            return $this->_User;
        }

        /**
         * Returns a WP_User object if the field and data are found
         * @param $email
         * @return object
         */
        public function getUserByEmail($email)
        {
            return get_user_by('email', $email);
        }

        /**
         * Try to know if the user exists by some username or the user e-mail
         * @param $username
         * @return bool
         */
        public function userExists( $username )
        {
            $user = username_exists( $username );

            if (!$user) // tentar ainda pegar pelo e-mail do usuario
                $user = $this->getUserByEmail($username);

            if (!$user) // usuario nao existe mesmo..
                return false;

            return true;
        }

        /**
         * Get all user data, including any custom field that might be added through the user_name
         * (the user_name could be anything, including the e-mail)
         * @param $username
         * @return object|bool
         */
        public function getUserData( $username )
        {
            if ( !$this->userExists( $username ))
                return false;

            $user_id = username_exists( $username );

            if (!$user_id) // tentar pegar pelo email
                $this->_User = $this->getUserByEmail($username);
            else
                $this->_User = $this->getUserByID($user_id);

            return $this->_User;
        }

        /**
         * Returns user current contact fields
         * @param $username
         * @param $contatos
         * @return array|bool
         */
        public function getUserContact($username, $contatos)
        {
            if (!isset($this->_contactFields) || empty($this->_contactFields))
                return false;

            if (!isset($this->_User) || empty($this->_User) )
                $this->getUserData($username);

            if (is_array($contatos))
            {
                $response = array();

                foreach ($contatos as $contato)
                {
                    if (array_key_exists($contato, $this->_contactFields))
                        $response[$contato] = $this->_User->$contato;
                    else
                        $response[$contato] = null;
                }

                return $response;
            }
            else
            {

                if (array_key_exists($contatos, $this->_contactFields))
                    return $this->_User->$contatos;
                else
                    return null;
            }

        }

        public function getUserNomeCompleto( $username )
        {
            if (isset($this->_User) || empty($this->_User) )
                $this->getUserData($username);

            return $this->_User->first_name . ' ' . $this->_User->last_name;

        }

        /**
         * Validate the user data
         * @param $user_info
         * @return array|bool|WP_Error
         */
        public function validateUserData( $user_info )
        {
            foreach ( $user_info as $key => $value )
            {
                if ($key == 'email')
                {
                    if (empty($value))
                        $this->PlulzNotices->addError($this->_name, __('Preencha o campo ' . $key, $this->_name) );
                    else
                        $validEmail = is_email($value);

                    // @todo the is_email esta considerando algo como nome.sobrenome@... como invalido
                    //if (isset($validEmail) && !$validEmail)
                      //  $this->PlulzNotices->addError($this->_name, __('E-mail inválido. Preecha-o corretamente', $this->_name) );
                }
                else if( empty($value) )
                    $this->PlulzNotices->addError($this->_name, __('Preencha o campo ' . $key, $this->_name) );
            }

            if ( $this->PlulzNotices->hasErrors() )
                return false;
            else
                return true;
        }

        /**
         * Add extra fields in the user profile page of Wordpress
         * @param $contactmethods
         *
         * @internal param $user
         * @return array
         */
        public function addUserContactFields( $contactmethods )
        {
            foreach($this->_contactFields as $key => $field)
                $contactmethods[$key]   =   $field;

            return $contactmethods;
        }

        /**
         * Method that searchs and returns users by their e-mail
         * @param $term
         * @return stdObject list of users
         */
        public function searchUsersByEmail( $term )
        {
            if (empty($term))
                return;

            global $wpdb;

            $sql = "SELECT user_email
                    FROM $wpdb->users
                    WHERE user_email LIKE '%{$term}%'
                    ORDER BY user_email ASC";

            return $wpdb->get_results($sql);

        }
    }
}