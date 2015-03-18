<?php

require_once "Mail.php";

if (!class_exists('PazzaniBrindesOrcamento'))
{
    class PazzaniBrindesOrcamento extends PlulzPost
    {
        /**
         * Holds current logged in user information
         * @var array
         */
        protected $_currentUser;

        public $PlulzProduto;

        public $PlulzUser;

        public $PlulzContato;

        public $acceptedStatus = array(
            'auto-draft', 'draft', 'future', 'pending', 'draft-user'
        );

        public function __construct()
        {
            $this->PlulzProduto     =   new PazzaniBrindesProduto();

            $this->PlulzUser        =   new PazzaniBrindesUser();

            $this->PlulzCarrinho    =   new PazzaniBrindesCarrinho();

            $this->PlulzContato     =   new PazzaniBrindesContato();

            $this->_name            =   'pazzanibrindes_orcamento';

            $this->_postType        =   'orcamento';

            $this->_postData =   array(
                'config'    =>      array(
                    'labels' => array(
                        'name'              => __( 'Orçamentos', $this->_name ),
                        'singular_name'     => __( 'Orçamento', $this->_name ),
                        'add_new'           => __( 'Novo Orçamento', $this->_name ),
                        'add_new_item'      => __( 'Adicionar Orçamento', $this->_name ),
                        'edit'              => __( 'Editar', $this->_name ),
                        'search_items'      => __( 'Procurar Orçamento', $this->_name ),
                        'description'       => __( 'Cadastre todos os orçamentos do site', $this->_name ),
                        'not_found'         => __( 'Nenhum orçamento encontrado', $this->_name ),
                        'not_found_in_trash'=> __( 'Nenhum orçamento na lixeira', $this->_name )
                    ),
                    'public'                => true,
                    'publicly_queryable'    => false,   // makes the post unavailable at the front end
                    'exclude_from_search'   => false,
                    'show_ui'               => true,
                    'menu_position'         => 4,
                    '_builtin'              => false,
                    '_edit_link'            => 'post.php?post=%d',
                    'capability_type'       => 'post',
                    'has_archive'           => true,
                    'hierarchical'          => false,
                    'rewrite'               => array(
                        'slug' => '/orcamentos',  'with_front' => false
                    ),
                    'query_var'             => 'orcamento',
                    'supports'              => array( 'author', 'editor', 'revisions' )
                ),
                'editPage'  =>  array(
                    'customColumns'         =>  array(
                        'title'     => __( 'Orçamento', $this->_name ),
                        'origem'    => __( 'Origem', $this->_name ),
                        'nome'      => __( 'Cliente', $this->_name ),
                        'email'     => __( 'E-mail', $this->_name ),
                        'telefone'  => __( 'Telefone', $this->_name ),
                        'author'    => __( 'Vendedor', $this->_name ),
                        'total'     => __( 'Total', $this->_name),
                        'comments'  => '<img alt="Comentários" src="http://www.pazzanibrindes.com.br/wp-admin/images/comment-grey-bubble.png">',
                    ),
                    'sortableColumns'   =>  array(
                        'origem'     => 'origem',
                        'nome'       => 'nome',
                        'email'      => 'email',
                        'telefone'   => 'telefone',
                        'total'      => 'total',
                        'comments'   => 'comment_count'
                    )
                )
            );

            $this->_serializedFields = array(
                'cliente' => array(
                    'email', 'nome', 'telefone', 'origem'
                )
            );

            global $current_user;
            get_currentuserinfo();
            $this->_currentUser = $current_user;

        }


        /**
         * Method that returns a self created title for the Orcamentos post type
         * @param $title
         * @return string
         */
        public function orcamentoPostTitle( $title )
        {
            global $post;

            $title = $post->ID;

            return $title;
        }

        /**
         * Adds comments to the related post
         * @param $content
         * @return int
         * @todo depois este cara vai para a classe PlulzComment
         */
        public function addComment( $content )
        {
            // Return if the there is no content
            if (empty($content))
                return 0;

            # Lets strip html data before saving on the database
            $cleanContent = esc_html( $content );

            $commentdata = array(
                'comment_post_ID' => $this->id,
                'comment_author' => $this->_currentUser->display_name,
                'comment_author_email' => $this->_currentUser->user_email,
                'comment_author_url' => get_bloginfo('url'),
                'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                'comment_date' => date('Y-m-d G:i:s'),
                'comment_date_gmt' => date('Y-m-d G:i:s'),
                'comment_content' => $cleanContent,
                'comment_karma' => '',
                'comment_approved' => 1,
                'comment_agent' =>  $_SERVER['HTTP_USER_AGENT'],
                'comment_type' => 'comment',
                'comment_parent' => '',
            );

            return wp_insert_comment( $commentdata );
        }

        /**
         * @return mixed
         * @todo depois este cara vai para a classe PlulzComment
         */
        public function getApprovedComments()
        {
            $args = array(
                'post_id'   =>  $this->id,
                'status'    =>  'approve',
                'orderby'   =>  'ASC'
            );

            $comentarios = get_comments( $args );

            foreach( $comentarios as $comment)
            {
                $comment->comment_date = human_time_diff( $comment->comment_date, current_time('timestamp') ) . ' atrás';
            }

            return $comentarios;
        }

        /**
         * Send the email to the user assim que ele fecha o orçamento, avisando-o do sucesso e alerta
         * o admin / vendedor de que há um novo orçamento
         *
         * @param $data
         * @return void
         */
        public function sendConfirmationEmail($data)
        {
            $img_url            =   $data['template']['img'];
            $cliente            =   $data['cliente'];
            $produtos           =   $data['produto'];
            $templateCliente    =   $data['template']['cliente'];

            $to_cliente =   $cliente['email'];

            // Acertar o nome do cliente
            $nome       =   explode( " ", $cliente['nome'] );
            $to_name    =   ucfirst(strtolower($nome[0]));

            $telefone   =   $cliente['telefone'];

            $store      =   "Pazzani Brindes";

            // Montando a tabela com os produtos do orçamento
            $pedido = '';
            foreach($produtos as $produto)
            {
                $this->PlulzProduto->id = $produto['id'];
                $codigo     =   $this->PlulzProduto->getCode();
                $descricao  =   $this->PlulzProduto->getDescricao();
                $quantidade =   $produto['quantidade'];

                $pedido .= "<tr>
                                <td width='20%' align='center' style='font-weight:bold;text-align:center;'>{$codigo}</td>
                                <td width='60%' align='center' style='width:60%;text-align:center;'>{$descricao}</td>
                                <td width='20%' align='center' style='width:60%;text-align:center;'>{$quantidade}</td>
                            </tr>";
            }

            // ** Enviado o e-mail para o Cliente ** //

			$search_array   =   array( '[#$to_name#]', '[#$order_info#]', '[#$store_name#]', '[#$URL]', '[#$to_email#]', '[#$to_telefone#]' );

            $replace_array  =   array( $to_name, $pedido, $store, $img_url , $to_cliente, $telefone);

            $client_message =   str_replace( $search_array, $replace_array, $templateCliente );

            $this->PlulzContato->sendEmail($to_cliente, 'Solicitação de Orçamento Recebida com Sucesso', $client_message);

        }

        /**
         * Envia o orçamento para o cliente com todos os dados e valores preenchidos dele
         *
         * @param $template
         * @internal param $post_id
         */
        public function sendOrcamentoEmail($template)
        {
            $img_url    =   $template['img'];

            $post = get_post($this->id);

            $this->fetchPostMetadata();

            $htmlProduto = '';

            $cliente  = $this->_metadata['cliente'];
            $produtos = $this->_metadata['produto'];

            foreach ($produtos as $produto)
            {
                $this->PlulzProduto->id = $produto['id'];
                $tiposGravacao  =   $this->PlulzProduto->getProdutoTiposGravacao();

                $codigo         =   $this->PlulzProduto->getCode();
                $descricao      =   $this->PlulzProduto->getDescricao();
                $quantidade     =   $produto['quantidade'];
                $venda          =   $this->PlulzProduto->convertNumberToMoney($produto['venda_unitario']);
                $total          =   $this->PlulzProduto->convertNumberToMoney($produto['total']);
                $prazo          =   $produto['prazo'];
                $gravacao       =   $tiposGravacao[$produto['gravacoes']];

                $htmlProduto .= "<tr>
                                    <td width='6%' align='center' style='width:6%;font-weight:bold;font-size:11px;'>{$codigo}</td>
                                    <td width='27%' align='center' style='width:27%;text-align:center;font-size:11px;'>{$descricao}</td>
                                    <td width='14%' align='center' style='width:14%;text-align:center;font-size:11px;'>{$gravacao}</td>
                                    <td width='6%' align='center' style='width:6%;text-align:center;font-size:11px;'>{$quantidade}</td>
                                    <td width='14%' align='center' style='width:14%;text-align:center;font-size:11px;'>{$venda}</td>
                                    <td width='14%' align='center' style='width:14%;text-align:center;font-size:11px;'>{$total}</td>
                                    <td width='19%' align='center' style='width:19%;text-align:center;font-size:11px;'>{$prazo}</td>
                                </tr>";
            }

            $search_array   = array('[#$produtos#]','[#$conteudo#]', '[#$URL]');

            $replace_array  = array( $htmlProduto, nl2br($post->post_content), $img_url );

            $email  = str_replace( $search_array, $replace_array, $template['message'] );

            $vendedor = "{$this->_currentUser->display_name} <{$this->_currentUser->user_email}>";

            $this->PlulzContato->sendEmail($cliente['email'], 'Orçamento Pazzani Brindes', $email, $vendedor);

        }

        /**
         * Apply a default text to orcamento post type content
         * @param $post_content
         * @return string
         */
        public function defaultContent( $post_content )
        {
            $post_content = "Olá <span id='nome-do-cliente'>%%NOMECLIENTE%%</span>, tudo bem?

            Segue abaixo o orçamento conforme sua solicitação.

            <strong>No valor já está incluso a personalização do logo</strong>.

            Lembrando que o custo do frete para envio do pedido será pago pelo cliente e o mesmo varia de acordo com o peso e o local de entrega.

            Para dúvidas ou negociações entrar em contato pelo telefone %%TELEFONEVENDEDOR%% ou pelo meu e-mail %%EMAILVENDEDOR%%.

            Atenciosamente, %%NOMEVENDEDOR%%

            Pazzani Brindes Personalizados";

            return $post_content;
        }

        /**
         * Apply modification to the pre created content if those information are available
         * @param $content
         * @return mixed
         */
        public function preContent( $content )
        {
            global $post;

            $this->id = $post->ID;
            $this->fetchPostMetadata();

            $cliente = $this->PlulzUser->getUserData($this->_metadata['cliente']['email']);

            if (isset($cliente->first_name) && !empty($cliente->first_name))
                $nomeCliente = $cliente->first_name;
            else
                $nomeCliente = '%%NOMECLIENTE%%';

            if (isset($this->_currentUser->telefone) && !empty($this->_currentUser->telefone))
                $telefone = $this->_currentUser->telefone;
            else
                $telefone = '%%TELEFONEVENDEDOR%%';

            if (isset($this->_currentUser->display_name) && !empty($this->_currentUser->display_name))
                $display_name = $this->_currentUser->display_name;
            else
                $display_name = '%%NOMEVENDEDOR%%';

            if (isset($this->_currentUser->user_email) && !empty($this->_currentUser->user_email))
                $email = $this->_currentUser->user_email;
            else
                $email = '%%EMAILVENDEDOR%%';

            $search_array   = array('%%NOMECLIENTE%%', '%%TELEFONEVENDEDOR%%', '%%NOMEVENDEDOR%%', '%%EMAILVENDEDOR%%');

            $replace_array  = array( $nomeCliente, $telefone, $display_name, $email );

            $content  = str_replace( $search_array, $replace_array, $content );

            return $content;
        }

        /**
         * Method called when a user requests a Orcamento, created from the Frontend of the theme
         * @return bool
         */
        public function createOrcamento()
        {
            $post = array(
                'comment_status' => 'open',
                'ping_status' => 'closed',
                'pinged' => '',
                'post_author' => '',
                'post_category' => '',
                'post_content' => $this->defaultContent(''),
                'post_date' => date('Y-m-d G:i:s'),
                'post_date_gmt' => date('Y-m-d G:i:s'),
                'post_parent' => '',
                'post_password' => '',
                'post_status' => 'draft',
                'post_title' => 'Pendente',
                'post_type' => $this->_postType,
            );

            $id = wp_insert_post( $post );

            return $id;
        }

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

                $produtos = $this->_metadata['produto'];

                $totalOrcamento = 0;

                foreach($produtos as $produto)
                {
                    $total = str_replace(',', '', $produto['total']);

                    $totalOrcamento += (float)$total;
                   // var_dump($totalOrcamento);
                }
                $item->total = $totalOrcamento;

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
         * This function gets all elements defined in the serializedFields and will try to output
         * their content in their respective columns on the edit.php page
         * @param $column
         * @param $post_id
         * @return void
         * @hook manage_posts_custom_columns
         */
        public function customPostTypeColumnsContent( $column, $post_id )
        {
            if ($column == 'total')
            {
                $this->id = $post_id;
                $this->fetchPostMetadata();

                $produtos = $this->_metadata['produto'];

                $totalOrcamento = 0;

                foreach($produtos as $produto)
                {
                    $total = str_replace(',', '', $produto['total']);

                    $totalOrcamento += (float)$total;
                   // var_dump($totalOrcamento);
                }
                echo number_format($totalOrcamento, 2);
            }

            parent::customPostTypeColumnsContent($column, $post_id);
        }
    }
}
?>
