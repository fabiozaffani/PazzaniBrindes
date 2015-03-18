<?php

require_once "Mail.php";

if (!class_exists('PazzaniBrindesContato'))
{
    class PazzaniBrindesContato extends PlulzObjectAbstract
    {
        public $_smtpConfig = array(
            'host'  =>  'ssl://smtp.gmail.com',
            'port'  =>  '465',
            'username'  =>  'contato@pazzanibrindes.com.br',
            'password'  =>  '$nfywlm01',
            'auth'      =>  true
        );

        public $_email = array(
            'titulo'        =>  "Pazzani Brindes",
            'admin_email'   =>  "jaquelinev@pazzanibrindes.com.br"
        );


        public function __construct()
        {
            $this->_name = 'pazzanibrindes_contato';

            parent::__construct();
        }

        /**
         * Valida se o email esta preenchido e se e um email valido
         *
         * @param $email
         * @return bool|string
         */
        public function validateEmail($email)
        {

            if($email = $this->validateField($email))
            {

                if(is_email($email))
                {
                    return $email;
                }

                return false;
            }

            return false;
        }

        /**
         * Valida o nome do usuario
         *
         * @param $nome
         * @return bool
         */
        public function validateNome($nome)
        {
            if($nome = $this->validateField($nome))
            {
                return $nome;
            }

            return false;
        }

        /**
         * Valida o telefone do usuario
         * @param $telefone
         * @return bool|string
         */
        public function validateTelefone($telefone)
        {
            if($telefone = $this->validateField($telefone))
            {
                return $telefone;
            }

            return false;
        }

        /**
         * Valida o campo desejado
         * @param $field
         * @return bool|string
         */
        public function validateField($field)
        {
            if(trim($field) === '')
            {
                return false;
            }
            else
            {
                return stripslashes(trim($field));
            }

        }

        /**
         * Envia e-mail de contato da pagina interna de produto, difere que neste e-mail vai o código do produto
         *
         * @param $template
         * @param $nome
         * @param $email
         * @param $telefone
         * @param $codigo
         * @param $message
         * @return bool
         */
        public function sendProdutoContatoEmail($template, $nome, $email, $telefone, $codigo, $message)
        {
            $img_url        =   $template['img'];

            $subject        =   'Contato Pazzani Brindes';

            $search_array   = array('[#$nome#]','[#$email#]', '[#$telefone#]', '[#$codigo#]','[#$conteudo#]', '[#$URL]');

            $replace_array  = array( $nome, $email, $telefone, $codigo, $message, $img_url );

            $email          = str_replace( $search_array, $replace_array, $template['message'] );

            return $this->sendEmail($this->_email['admin_email'], $subject, $email);
        }


        /**
         * Envia um e-mail utilizando o template padrão de contato do site
         *
         * @param $template
         * @param $nome
         * @param $email
         * @param $telefone
         * @param $message
         * @return bool
         */
        public function sendContatoEmail($template, $nome, $email, $telefone, $message)
        {
            $img_url        =   $template['img'];

            $subject        =   'Contato Pazzani Brindes';

            $search_array   = array('[#$nome#]','[#$email#]', '[#$telefone#]', '[#$conteudo#]', '[#$URL]');

            $replace_array  = array( $nome, $email, $telefone, $message, $img_url );

            $email          = str_replace( $search_array, $replace_array, $template['message'] );

            return $this->sendEmail($this->_email['admin_email'], $subject, $email);
        }


        /**
         * Função que envia e-mail utilizando o pear, retorna um booleano dependendo se o e-mail foi enviado
         * com sucesso ou não
         *
         * @param $to
         * @param $subject
         * @param $body
         * @param string $from
         * @return bool
         */
        public function sendEmail($to, $subject, $body, $replyTo = "Pazzani Brindes <contato@pazzanibrindes.com.br>")
        {
            $headers = array (
                'MIME-Version'  =>  '1.0',
                'Content-Type'  =>  "text/html; charset=ISO-8859-1",
                'From'          =>  "Pazzani Brindes <contato@pazzanibrindes.com.br>",
                'Reply-To'      =>  $replyTo,
                'To'            =>  $to,
                'Subject'       =>  $subject);

            $smtp = Mail::factory('smtp', $this->_smtpConfig);

            $mail = $smtp->send($to, $headers, $body);

            if (PEAR::isError($mail))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}
?>
