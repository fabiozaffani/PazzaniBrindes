<?php
/**
 * Implementação do post_type orçamento no Wordpress
 *
 * Esta classe extend a PlulzPostControllerAbstract
 * 
 */
if ( !class_exists('PazzaniBrindesOrcamentoController') )
{
	class PazzaniBrindesOrcamentoController extends PlulzPostControllerAbstract
	{
		public function __construct()
        {
            $this->_ajaxEvents = array(
                   'public'   =>  array(
                        'orcamento-ajax'
                   ),
                   'restricted'    =>  array(
                        'orcamento-ajax'
                   )
            );

            $this->_nonce       =   'pazzanibrindes_orcamento_nonce';

            $this->_post        =   new PazzaniBrindesOrcamento();

            // Set the custom metaboxes to be used in all kind of post types

            parent::__construct();

        }

        public function startPostBack()
        {
            parent::startPostBack();
            $this->setAction( 'add_meta_boxes', 'customMetaboxes' );
            $this->setFilter( 'default_content', array('_post', 'defaultContent' ) );
            $this->setFilter( 'the_editor_content', array('_post', 'preContent' ) );
            $this->setFilter( 'title_save_pre', array('_post', 'orcamentoPostTitle') );
        }


        public function adminAssets()
        {
            wp_register_style( 'PlulzMetaboxStylesheet', $this->_assets . 'css/plulz-metabox-style.css' );
            wp_enqueue_style( 'PlulzMetaboxStylesheet' );

            global $post;

            // Styles
            wp_register_style( 'jQueryUICSS', $this->_assets . 'js/plugins/jquery-ui-1.10.0.custom/css/ui-lightness/jquery-ui-1.10.0.custom.min.css' );
            wp_enqueue_style(  'jQueryUICSS' );

            wp_enqueue_script( 'jQueryUITools', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/jquery-ui.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'PlulzPriceFormatJS', $this->_assets . 'js/plugins/priceformat.jquery.js', array( 'jquery' ) );
            wp_enqueue_script( 'maskedinput', $this->_assets . 'js/plugins/maskedinput.jquery.js', array('jquery') );
            wp_enqueue_script( 'PlulzToolsJS', $this->_assets . 'js/tools.js', array( 'jquery' ) );
            wp_enqueue_script( 'PlulzOrcamentoJS', $this->_assets . 'js/orcamento.js', array( 'jquery', 'jQueryUITools', 'PlulzPriceFormatJS', 'maskedinput', 'PlulzToolsJS' ) );

            wp_localize_script(
                'PlulzOrcamentoJS',
                'PazzaniBrindesOrcamento',
                array(
                    'ajaxurl'               =>  admin_url( 'admin-ajax.php' ),
                    // generate a nonce with a unique ID "plulzajax-post-comment-nonce"
                    // so that you can check it later when an AJAX request is sent
                    'orcamentoAjaxNonce'    =>  wp_create_nonce( $this->_nonce ),
                    'actionOrcamento'       =>  'orcamento-ajax',
                    'basename'              =>  $this->_name,
                    'postID'                =>  $post->ID,
                    'extraList'             =>  json_encode(array()),
                    'codeList'              =>  json_encode($this->_post->PlulzProduto->getCodeList()),
                    'moneyFields'           =>  json_encode($this->_post->PlulzProduto->getMoneyFields()),
                    'readOnly'              =>  json_encode($this->_post->PlulzProduto->getReadOnly()),
                    'camposProduto'         =>  json_encode($this->_post->PlulzProduto->getCamposProduto()),
                    'gravacoes_list'        =>  json_encode(array()) // theres no default value for this for now
                )
            );
        }


        public function handleAjax()
        {
            header('Content-type: application/json');

            $nonce = PlulzTools::getValue('orcamentoAjaxNonce');

             // check to see if the submitted nonce matches with the generated nonce we created earlier
            if ( ! wp_verify_nonce( $nonce, $this->_nonce ) )
                die ( 'Busted!');

            $action     =   PlulzTools::getValue('todo');

            switch ($action)
            {

                case 'adicionar_comentario':

                    $id = PlulzTools::getValue('id');
                    $content = PlulzTools::getValue('content');

                    if (empty($id) || empty($content))
                        return false;

                    $this->_post->id = $id;

                    if( !$this->_post->addComment( $content ) )
                        return false;

                    $postCommentList = $this->_post->getApprovedComments();

                    echo json_encode($postCommentList);

                break;

                case 'remover_comentario':

                    $id = PlulzTools::getValue('id');

                    if (empty($id))
                        return false;

                    echo wp_delete_comment( $id );

                break;

                case 'usuarios_email_ajax':

                    $email = PlulzTools::getValue('email');

                    if (empty($email))
                        return false;

                    echo json_encode($this->_post->PlulzUser->searchUsersByEmail($email));

                break;

                case 'atualizar_usuario':

                    $username = PlulzTools::getValue('email');

                    $response['nome']       =   $this->_post->PlulzUser->getUserNomeCompleto( $username );
                    $response['telefone']   =   $this->_post->PlulzUser->getUserContact( $username, 'telefone');

                    echo json_encode($response);

                break;


                case 'atualizar_produto':

                    $newData = (bool)PlulzTools::getValue('resetValues');

                    $data = array();
                    
                    $id =  PlulzTools::getValue('id');
                    $data['comissao'] = PlulzTools::getValue('comissao', 0);

                    if ( !$newData )
                    {
                        $data['quantidade']     = PlulzTools::getValue('quantidade');
                        $data['descricao']      = PlulzTools::getValue('descricao');
                        $data['gravacoes']      = PlulzTools::getValue('gravacoes');
                        $data['venda_unitario'] = PlulzTools::ConvertMoneyToNumber(PlulzTools::getValue('venda_unitario'));
                        $data['extras']         = PlulzTools::getValue('extras');
                    }

                    $this->_post->PlulzProduto->id = $id;

                    $response = $this->_post->PlulzProduto->getValoresProduto( $data );

                    echo json_encode($response);

                break;

            }

            exit;
        }

        /**
         * Default method responsible for saving our custom metaboxes data in wordpress custom post meta.
         * Must be overriden in children class if want to validate the fields before saving on the db
         * or any other thing
         *
         * @param $post_id
         * @param string $data
         * @return bool
         */
        public function handlePost( $post_id, $data = '' )
        {
            if ( empty($data) )
            {
                if( !$this->validateRequest( $post_id ) )
                    return false;

                $orcamento = $_POST[$this->_name];
            }
            else
            {
                $orcamento = $data;
            }

            $this->_post->PlulzUser->createCliente( $orcamento['cliente'] );

            $this->_post->id = $post_id;


            // Validar os produtos e acertar as informações faltantes (caso hajam)

            $produtos = $orcamento['produto'];
            $applyComissao = !empty($orcamento['atualizarTudo']) ? true : false;

            foreach($produtos as $key => $produto)
            {
                $this->_post->PlulzProduto->id = $produto['id'];

                $produto['comissao'] = isset($orcamento['comissao']) ? $orcamento['comissao'] : 0;

                $orcamento['produto'][$key] = $this->_post->PlulzProduto->getValoresProduto($produto, true, $applyComissao);
            }

            // Verifica se foi há novo comentário e, caso verdadeiro, adiciona ele
            if ( isset($orcamento['addComment']) && !empty($orcamento['comentario']) )
                $this->_post->addComment( $orcamento['comentario'] );

            // Independentemente se houver erros ou não, precisa ser salvo para nao fuder o nome do orcamento
            $updated = $this->_post->savePostCustomData( $orcamento );

            if ($this->PlulzNotices->hasErrors())
                $this->_post->savePostAsDraft(array(&$this, 'handlePost'));

            $oldStatus      =   PlulzTools::getValue('original_post_status', 'draft-user');
            $currenStatus   =   get_post_status($post_id);

            if ( in_array($oldStatus, $this->_post->acceptedStatus) && $currenStatus == 'publish'  )
            {
                $template = array(
                    'img'       =>  $this->_assets . 'img/email/',
                    'message'   =>  file_get_contents($this->_serverAssets . "email/new_orcamento.txt")
                );
                $this->_post->sendOrcamentoEmail( $template );
            }

            return $updated;
        }

        public function customMetaboxes()
        {
            add_meta_box('orcamento_cliente_metabox', __('Informações do Orçamento', $this->_name), array(
               &$this, 'customMetaboxOrcamentoCliente') );

            add_meta_box('orcamento_produtos_metabox', __('Produtos do Orçamento', $this->_name), array(
               &$this, 'customMetaboxOrcamentoProdutos') );

            add_meta_box('orcamento_comentarios_metabox', __('Comentários do Orçamento', $this->_name), array(
               &$this, 'customMetaboxOrcamentoComentarios') );
        }

        /**
         * Output do que sera exibido dentro da metabox Cliente
         * @param $post
         * @return void;
         */
        public function customMetaboxOrcamentoCliente( $post )
        {
            $this->_post->id = $post->ID;
            $this->_post->fetchPostMetadata();

            $this->outputNonce();

            $this->set('origens', $this->_post->PlulzUser->getOrigens());
            $this->set('metadata', $this->_post->getMetadata());
            $this->includeTemplate('ClienteMetabox', 'Orcamento');
        }
        
        /*
         * Output do que sera exibido dentro da metabox Produto
         * @param $post
         * @return void
         */
        public function customMetaboxOrcamentoProdutos( $post )
        {
            $this->_post->id = $post->ID;
            $this->_post->fetchPostMetadata();

            $this->outputNonce();

            $metadata = $this->_post->getMetadata();

            $produtos = count($metadata['produto']);

            if ($produtos < 1)
                $produtos = 1;

            $listaProdutos = array();

            for ( $i=0; $i < $produtos; $i++)
            {
                if (isset($metadata['produto']) && !empty($metadata['produto']))
                {
                    $id = $metadata['produto'][$i]['id'];

                    if( !empty($id))
                    {
                        $this->_post->PlulzProduto->id = $id;
                        $gravacoes = $this->_post->PlulzProduto->getProdutoTiposGravacao();
                        $extraList = $this->_post->PlulzProduto->getProdutoExtraFields();
                    }
                }

                $listaProdutos[$i] = array(
                    'id'       => !isset($id) ? array() : $id,
                    'gravacoes'=> !isset($gravacoes) ? array() : $gravacoes,
                    'extraList'=> !isset($extraList) ? array() : $extraList
                );

            }

            $this->set('metadata', $metadata);
            $this->set('codeList', $this->_post->PlulzProduto->getCodeList());
            $this->set('listaProdutos', $listaProdutos);
            $this->includeTemplate('ProdutoMetabox', 'Orcamento');
        }

        /**
         * Output do que sera exibido dentro da metabox Comentarios
         * @param $post
         * @return void
         */
        public function customMetaboxOrcamentoComentarios( $post )
        {
            $this->_post->id = $post->ID;

            $this->outputNonce();

            $postCommentList = $this->_post->getApprovedComments();

            $comments = array();
            foreach ($postCommentList as $comment)
            {
                $comments[] = array(
                    'ID'            =>  $comment->comment_ID,
                    'conteudo'      =>  $comment->comment_content,
                    'data'          =>  $comment->comment_data,
                    'autor'         =>  $comment->comment_author
                );
            }

            $this->set('commentList', $comments);
            $this->includeTemplate('ComentariosMetabox', 'Orcamento');
        }

        public function createOrcamento( $data )
        {
            $cliente    = $data['cliente'];
            $produtos   = $data['produto'];

            // Validar o usuario
            if(!$this->_post->PlulzUser->validateUserData( $cliente ))
                return false;

            // Validar os produtos, se o minimo esta ok
            foreach($produtos as $produto)
            {
                $this->_post->PlulzProduto->id = $produto['id'];

                if( $minimo = $this->_post->PlulzProduto->validarQuantidade( $produto['quantidade'] ) )
                {
                    $codigo = $this->_post->PlulzProduto->getCode();
                    $this->PlulzNotices->addError($this->_name, 'O produto cód. ' . $codigo . ' está abaixo do mínimo: ' . $minimo . ' unidades');
                }
            }

            if ($this->PlulzNotices->hasErrors())
                return false;

            $id = $this->_post->createOrcamento();

            if (is_wp_error($id))
            {
                $errors = $id->get_error_messages();

                foreach($errors as $erro)
                    $this->PlulzNotices->addError($this->_name, $erro);

            }
            else if (!$id)
            {
                $this->PlulzNotices->addError($this->_name, 'Ocorreu um erro ao criar o orçamento');
            }
            else
            {
                $orcamento = array(
                    'cliente'   =>  $cliente,
                    'produto'   =>  $produtos
                );

                if ($this->handlePost($id, $orcamento))
                {
                    $orcamento['template'] = array(
                        'img'       =>  $this->_assets . 'img/email/',
                        'cliente'   =>  file_get_contents($this->_serverAssets . "email/new_order_customer.txt"),
                        'admin'     =>  file_get_contents($this->_serverAssets . "email/new_order_admin.txt")
                    );

                    $this->_post->sendConfirmationEmail( $orcamento );
                }
            }

            if ($this->PlulzNotices->hasErrors())
                return false;

            return true;
        }

        /**
        * Gets the searched params, sees if it is a custom field and if it is serialized or not and deal with
        * all that
        * @param $query
        * @return
        *
        * @internal param $ parse_query
        * @hook parse_query
        */
       public function customPostTypeMetadaFilter( $query )
       {
           $serializedFields = $this->_post->getSerializedFields();

           // If no serialized fields were defined, just leave
           if ( empty($serializedFields) )
               return $query;

           global $pagenow;

           if ( !is_admin() || $pagenow != 'edit.php' )
               return $query;

           $postMetaValuefilter   =   PlulzTools::getValue('post_meta_value_filter_value', '');
           $postOrderBy           =   PlulzTools::getValue('orderby', '');

           // Executa a busca por posts baseado nos filtros passados
           if ( !empty($postMetaValuefilter) )
           {
               // Possible mysql manipulation before the query
               // (posts_where, posts_join, posts_groupby, posts_orderby, posts_distinct, posts_fields,
               // post_limits, posts_where_paged, posts_join_paged, and posts_request

               $this->setFilter( 'posts_join', array('_post', 'serializedSearchJoin') );
               $this->setFilter( 'posts_where',  array('_post', 'serializedSearchWhere') );
           }

           // Ordenar os resultados obtidos da busca
           if ( !empty($postOrderBy) )
           {
               // Lets manipulate the orderby only if it is a serialized field, otherwise degrades to wp default
               if (PlulzTools::inMultidimensionalArray($postOrderBy, $this->_post->_serializedFields))
               {
                   $this->setFilter( 'the_posts', array('_post', 'serializedQueryPostData') );
               }
               else if ($postOrderBy == 'total')
               {
                   $this->setFilter( 'the_posts', array('_post', 'serializedQueryPostData') );
               }
               else
                   return $query;
           }
       }

        /**
        * Method para migrar as bases de dados
        */
    /*    public $lastAddedID;

        public function transferenciaCreateOrcamento( )
        {
            return;
            set_time_limit( 9600 );
            $orcamentoCSV = fopen($this->_appControllerDir . 'backup/orcamentos.csv', "r");
            $detalhesOrcamentoCSV = fopen($this->_appControllerDir . 'backup/orcamentos_details.csv', "r");
            $comentariosCSV = fopen($this->_appControllerDir . 'backup/orcamentos_comments.csv', "r");

            // ORCAMENTOS

            $header = fgetcsv($orcamentoCSV, 1000, ";");
            $orcamentos = array();
            while ($row = fgetcsv($orcamentoCSV, 1000, ";") )
            {
                $arr = array();

                foreach ($header as $i => $col)
                {
                    if (   $col == 'forma_pagamento'
                    || $col == 'enviado'
                    || $col == 'link_orcamento'
                    || $col == 'fechado'
                    || $col == 'desconto_agressivo'
                    || $col == 'pending')
                        continue;
                    else
                        $arr[$col] = $row[$i];
                }

                $orcamentos[] = $arr;
            }

            // DETALHES ORCAMENTO

            $header = fgetcsv($detalhesOrcamentoCSV, 1000, ";");
            $detalhesOrcamento = array();

            $taxonomiasGravacao =  get_terms( 'gravacao', 'hide_empty=0' );
            $novoTaxonomiasGravacao = array();

            foreach($taxonomiasGravacao as $gravacao)
            {
                $novoTaxonomiasGravacao[] = array(
                    'id'    =>  $gravacao->term_id,
                    'nome'  =>  (string)PlulzTools::NormalizeString(strtolower($gravacao->name)),
                    'parent'=>  $gravacao->parent
                );
            }

            // Taxonomias agora podem ser comparadas e buscadas direto pelo id delas
            $taxonomiasGravacao = $novoTaxonomiasGravacao;

            $numbersToRemove = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            while ($row = fgetcsv($detalhesOrcamentoCSV, 1000, ";") )
            {
                $arr = array();
                $index = array_search('id_orcamento', $header);
                $indexIDProduto = array_search('id_produto', $header);
                $curKey = $row[$index];

                foreach ($header as $i => $col)
                {

                    if ($col =='id' || $col == 'id_orcamento' || $col == 'desconto' || $col == 'comissao_externa' )
                        continue;

                    if ($col == 'id_produto')
                    {
                        $key = 'id';

                        // Alguns produtos mudaram de ID devido a remoção de alguns itens para a nova base
                        $normalize = array(
                            '113' => '105',
                            '114' => '106',
                            '115' => '107',
                            '116' => '108',
                            '117' => '109',
                            '118' => '110',
                            '119' => '111'
                        );

                        $row[$i] = strtr($row[$i], $normalize);

                    }
                    else if($col == 'description')
                        $key = 'descricao';
                    else if ($col == 'unitario')
                        $key = 'venda_unitario';
                    else if ( $col == 'cores' )
                    {
                        $key = 'gravacoes';

                        $idProduto  =   $row[$indexIDProduto];

                        $this->_post->PlulzProduto->id = $idProduto;

                        $codigo     =   $this->_post->PlulzProduto->getCode();
                        $abrev      =   strtoupper(str_replace($numbersToRemove, '', $codigo));

                        // Agora vamos localizar o ID da gravacao correta, o $row[$i] certo
                        $cor = trim((string)PlulzTools::NormalizeString(strtolower($row[$i])));

                        // Normalize shit
                        $normalize = array(
                            'laser 2x' => 'laser', '2 cores' => '2', '5' => 'cromia', 'serigrafia' => 'cromia'
                        );
                        $cor = strtr($cor, $normalize);

                        $tampografia = array('1', '2', '3', '4', '5', 'cromia', 'serigrafia');

                        $parent = '';
                        $nome = '';

                        if (is_numeric($cor))
                            $artigo = $cor == '1' ? '-cor' : '-cores';
                        else
                            $artigo = '';


                        if ($abrev == 'CP')
                        {
                            $parent = 'canetas';
                            $nome = $cor . $artigo;
                        }
                        else if ($abrev == 'CM')
                        {
                            $parent = 'laser';
                            $nome = 'laser cm';
                        }
                        else if ($abrev == 'CH')
                        {
                            if ($codigo == 'CH007' || $codigo == 'CH008' || $codigo == 'CH009')
                                $nome = 'resina';
                            else if ($codigo == 'CH021' || $codigo == 'CH020' || $codigo == 'CH012')
                                $nome = 'hot stamping';
                            else if ($codigo == 'CH006')
                                $nome = 'silk screen';
                            else if ( $codigo == 'CH011')
                                $nome = 'relevo';
                            else
                                $nome = 'laser ch';
                        }
                        else if ($abrev == 'ES')
                        {
                            if ($cor == 'laser')
                                $nome = 'laser es';
                            else
                                $nome = 'sem gravacao';
                        }
                        else if ($abrev == 'FR')
                        {
                            $parent = 'geral';
                            $nome = $cor . $artigo;
                        }
                        else if ($abrev == 'LF')
                        {
                            if ($cor == 'laser')
                            {
                                $parent = 'laser';
                                $nome = 'laser lf';
                            }
                            else if (in_array($cor, $tampografia))
                            {
                                $parent = 'geral';
                                $nome = $cor . $artigo;
                            }
                        }
                        else if ($abrev == 'ME')
                        {
                            if ($cor == 'laser')
                            {
                                $parent = 'laser';
                                $nome = 'laser me';
                            }
                            else if (in_array($cor, $tampografia))
                            {
                                $parent = 'geral';
                                $nome = $cor . $artigo;
                            }
                        }
                        else if ($abrev == 'PD')
                        {
                            $parent = 'laser';
                            $nome   = 'laser pd';
                        }
                        else if ($abrev == 'PC')
                        {
                            if ($codigo == 'PC001' || $codigo == 'PC002' || $codigo == 'PC003')
                            {
                                $cor = 'relevo colorido';
                            }

                            if (in_array($cor, $tampografia))
                            {
                                $parent = 'geral';
                                $nome = $cor . $artigo;
                            }
                        }
                        else if ($abrev == 'SE')
                        {
                            $parent = 'geral';
                            $nome = $cor . $artigo;
                        }
                        else if ($abrev == 'SQ')
                        {
                            if ($cor == 'laser')
                            {
                                $parent = 'laser';
                                $nome = 'laser sq';
                            }
                            else if (in_array($cor, $tampografia))
                            {
                                $parent = 'geral';
                                $nome = $cor . $artigo;
                            }
                        }

                        // Vamos procurar os ids dos respectivos
                        if ( !empty($parent) )
                        {
                            $parent         =   PlulzTools::searchMultidimensionalArray($taxonomiasGravacao, 'nome', $parent);
                            $parentID       =   $parent[0]['id'];
                            $gravacoesDisp  =   PlulzTools::searchMultidimensionalArray($taxonomiasGravacao, 'parent', $parentID);


                            foreach($gravacoesDisp as $gravacao)
                            {
                                if ( $gravacao['nome'] == $nome )
                                    $row[$i] = $gravacao['id'];
                            }
                        }
                        else
                        {
                            $gravacoesDisp  =   PlulzTools::searchMultidimensionalArray($taxonomiasGravacao, 'nome', $nome);
                            $row[$i]        =   $gravacoesDisp[0]['id'];
                        }
                    }
                    else
                        $key = $col;

                    $arr[$key] = $row[$i];
                }

                $detalhesOrcamento[$curKey][] = $arr;
            }

            // COMENTARIOS

            $header = fgetcsv($comentariosCSV, 1000, ";");
            $mensagens = array();
            $comentarios = array();
            while ($row = fgetcsv($comentariosCSV, 1000, ";") )
            {
                $index = array_search('id_orcamento', $header);
                $mensagem = array_search('comentario', $header);
                $tipo = array_search('type', $header);

                $curKey = $row[$index];

                $mensagem = $row[$mensagem];

                if ($row[$tipo] == 'email')
                    $mensagens[$curKey] = $mensagem;
                else
                    $comentarios[$curKey] = $mensagem;
            }

            foreach( $orcamentos as $orcamento )
            {
                $idAntigo   =   $orcamento['id_orcamento'];
                $clienteID  =   $orcamento['id_cliente'];
                $content    =   $mensagens[$idAntigo];

                $title = $this->lastAddedID + 1;

                $post = array(
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_author' => $orcamento['id_vendedor'],
                    'post_category' => '',
                    'post_content' => $content,
                    'post_date' => $orcamento['data'],
                    'post_date_gmt' => $orcamento['data'],
                    'post_parent' => '0',
                    'post_status' => 'publish',
                    'post_title' => (string)$title,
                    'post_type' => $this->_postType,
                );

                $this->lastAddedID = wp_insert_post( $post );

                // Informacoes produtos e usuarios

                $clienteData = $this->_post->PlulzUser->getUserByID( $clienteID );

                $cliente = array(
                    'email'     =>  $clienteData->user_email,
                    'telefone'  =>  $clienteData->telefone,
                    'nome'      =>  $clienteData->first_name . ' ' . $clienteData->last_name
                );

                $orcamento = array(
                    'cliente'   =>  $cliente,
                    'produto'   =>  $detalhesOrcamento[$idAntigo]
                );

                if (isset($comentarios[$idAntigo]))
                {
                    $orcamento['addComment'] = true;
                    $orcamento['comentario'] = $comentarios[$idAntigo];
                }

                $this->handlePost( $this->lastAddedID, $orcamento );
            }

        } */
	}
}

?>