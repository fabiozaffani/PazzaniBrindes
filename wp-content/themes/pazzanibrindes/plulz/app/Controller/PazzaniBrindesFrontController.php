<?php

// Make sure there is no bizarre coincidence of someone creating a class with the exactly same name of this plugin
if ( !class_exists("PazzaniBrindesFrontController") )
{
    class PazzaniBrindesFrontController extends PlulzFrontControllerAbstract
    {
        public $PazzaniBrindesAdmin;      // Holds current theme main class

        public $PazzaniBrindesUser;

        public $PazzaniBrindesFacebook;

        public $PazzaniBrindesOrcamento;

        public $PazzaniBrindesContato;

        protected $PlulzContato;

        protected $PlulzCarrinho;

        protected $PlulzAdmin;

        protected $PlulzUser;

        public function __construct()
        {
            $this->_name    =   'pazzanibrindes';

            $this->_nonce   =   'pazzanibrindes_nonce';

            $this->_ajaxEvents = array(
                  'public'   =>  array(
                       'carrinho-ajax',
                       'contato-ajax',
                        'signup-ajax'
                  ),
                  'restricted'    =>  array(
                       'carrinho-ajax',
                       'contato-ajax',
                       'signup-ajax'
                  )
            );

            $this->PazzaniBrindesAdmin       =   new PazzaniBrindesAdminController();

            $this->PazzaniBrindesFacebook    =   new PazzaniBrindesFacebookController();

            $this->PazzaniBrindesOrcamento   =   new PazzaniBrindesOrcamentoController();

            $this->PazzaniBrindesProduto     =   new PazzaniBrindesProdutoController();

            $this->PazzaniBrindesUser        =   new PazzaniBrindesUserController();

            $this->PlulzUser        =   new PazzaniBrindesUser();

            $this->PlulzContato     =   new PazzaniBrindesContato();

            $this->PlulzCarrinho    =   new PazzaniBrindesCarrinho();

            $this->PlulzAdmin       =   new PazzaniBrindesAdmin();

            // This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
            add_theme_support(  'post-thumbnails' );
            add_image_size(     'post-thumb', 300, 300, true );
            add_image_size(     'list-thumb', 200, 200, true );
            add_image_size(     'mini-thumb', 50, 50, true );
            register_nav_menu(  'main', 'Main navigation menu');

            // Extra widget areas
            $this->setAction( 'widgets_init', 'widgets' );

            parent::__construct();
        }

        /**
         * Method that loads extra js in the theme frontend
         * @return void
         */
        public function loadAssets()
        {
            parent::loadAssets();


//            wp_register_style( 'lightboxCSS', $this->_assets . 'css/slimbox2.css' );
//            wp_enqueue_style(  'lightboxCSS' );

            wp_enqueue_script( 'maskedinput', $this->_assets . 'js/plugins/maskedinput.jquery.js', array('jquery'), '1.0', true);
            wp_enqueue_script( 'toolsjs', $this->_assets . 'js/tools.js', array( 'jquery' ), '1.0', true );
            wp_enqueue_script( 'colorsJS', $this->_assets . 'js/plugins/colors.jquery.js', array( 'jquery' ), '1.0', true );
            wp_enqueue_script( 'modal', $this->_assets . 'js/plugins/jquery.leanModal.min.js', array('jquery'), '1.0', true);
            wp_enqueue_script( 'frontend_script', $this->_assets . 'js/frontend.js', array('toolsjs', 'colorsJS', 'maskedinput', 'jquery'), '1.0', true );


            wp_localize_script(
                'frontend_script',
                'Carrinho',
                array(
                    'ajaxurl'           =>  $this->_adminAjaxUrl,
                    // generate a nonce with a unique ID "plulzajax-post-comment-nonce"
                    // so that you can check it later when an AJAX request is sent
                    'ajaxNonce'         =>  wp_create_nonce( $this->_nonce ),
                    'action'            =>  'carrinho-ajax',
                    'fechamento'        =>  $this->PlulzCarrinho->getFechamento(),
                    'home'              =>  $this->_homeUrl
                )
            );

            wp_localize_script(
                'frontend_script',
                'Contato',
                array(
                    'ajaxurl'           =>  $this->_adminAjaxUrl,
                    // generate a nonce with a unique ID "plulzajax-post-comment-nonce"
                    // so that you can check it later when an AJAX request is sent
                    'ajaxNonce'         =>  wp_create_nonce( $this->_nonce ),
                    'action'            =>  'contato-ajax'
                )
            );

            wp_localize_script(
                'frontend_script',
                'SignUp',
                array(
                    'ajaxurl'           =>  $this->_adminAjaxUrl,
                    // generate a nonce with a unique ID "plulzajax-post-comment-nonce"
                    // so that you can check it later when an AJAX request is sent
                    'ajaxNonce'         =>  wp_create_nonce( $this->_nonce ),
                    'action'            =>  'signup-ajax'
                )
            );
        }

        public function handleAjax( $action )
        {
            header('Content-type: application/json');

            $nonce = PlulzTools::getValue('ajaxNonce');

            // check to see if the submitted nonce matches with the generated nonce we created earlier
            if ( !wp_verify_nonce( $nonce, $this->_nonce ) )
                die ( 'Busted!');

            $action     =   PlulzTools::getValue('todo');

            switch ($action)
            {
                case 'adicionar':

                    $prod_id    =   PlulzTools::getValue('id');

                    $quantidade =   PlulzTools::getValue('quantidade');

                    if (!empty($quantidade))
                        $result = $this->PlulzCarrinho->AddtoCart($prod_id, $quantidade);

                    if (isset($result) && $result)
                        $response = array( 'status' => 'true' );

                break;

                case 'remover':

                    $prod_id    =   PlulzTools::getValue('id');

                    $result = $this->PlulzCarrinho->RemoveFromCart($prod_id);

                    if ($result)
                        $response = array( 'status' => 'true' );

                break;

                case 'atualizar':

                    $prod_id    =   PlulzTools::getValue('id');

                    $quantidade = PlulzTools::getValue('quantidade');

                    $result  = $this->PlulzCarrinho->UpdateCart($prod_id, $quantidade);

                     if ($result)
                         $response = array( 'status' => 'true' );

                break;

                case 'status':

                    $carrinho = $this->PlulzCarrinho->getAllItens();

                    if (!empty($carrinho))
                    {
                        foreach( $carrinho as $id => $quantidade )
                        {
                            $this->PlulzCarrinho->PlulzProduto->id = $id;

                            $code = $this->PlulzCarrinho->PlulzProduto->getCode();

                            $response[$code]    =   $quantidade;
                        }
                    }
                    else
                        $response = 0;

                break;

                case 'enviarMensagem' :

                    $response = 0;

                    $nome = PlulzTools::getValue('nome');
                    $email= PlulzTools::getValue('email');
                    $telefone = PlulzTools::getValue('telefone');
                    $mensagem = PlulzTools::getValue('mensagem');
                    $codigo = PlulzTools::getValue('codigo');

                    if(!empty($nome) && !empty($email) && !empty($telefone) && !empty($mensagem) && !empty($codigo))
                    {
                        $template = array(
                            'img'       =>  $this->_assets . 'img/email/',
                            'message'   =>  file_get_contents($this->_serverAssets . "email/contato_produto.txt")
                        );

                        $sended = $this->PlulzContato->sendProdutoContatoEmail($template, $nome, $email, $telefone, $codigo, $mensagem);

                        if($sended)
                        {
                            $response = array( 'status' => 'true' );
                        }
                    }

                break;

                case 'signup':

                    $response = 0;

                    $nome = PlulzTools::getValue('nome');
                    $email= PlulzTools::getValue('email');
                    $telefone = PlulzTools::getValue('telefone');

                    if(!empty($nome) && !empty($email) && !empty($telefone))
                    {
                        $data = array(
                            'email'     =>  $email,
                            'telefone'  =>  $telefone,
                            'nome'      =>  $nome
                        );

                        $this->PlulzUser->createCliente($data);

                        $response = array( 'status' => 'true' );
                    }

                break;

                case 'ajaxlogin':

                    $response = 0;

                    $info['user_login'] = PlulzTools::getValue('email');
                    $info['user_password'] = '1q2w3e';
                    $info['remember'] = 'forever';

                    $user_signon = wp_signon( $info, false );

                    if (!is_wp_error($user_signon) )
                    {
                        $response = array( 'status' => 'true' );
                    }

                break;

                default:

                    $response = 0;

                break;
            }

            echo json_encode($response);
            exit;
        }

        /**
         * Function that loads extra widgets in the Template
         * @return void
         */
        public function widgets()
        {
            register_sidebar( array(
                'name' => __( 'Sidebar Area One', $this->_name ),
                'id' => 'sidebar-1',
                'description' => __( 'An optional widget area for your sidebar', $this->_name ),
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget' => "</aside>",
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            ) );

            register_sidebar( array(
                'name' => __( 'Sidebar Area Two', $this->_name ),
                'id' => 'sidebar-2',
                'description' => __( 'An optional widget area for your sidebar', $this->_name ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => "</div>",
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            ) );

            register_sidebar( array(
                'name' => __( 'Footer Area One', $this->_name ),
                'id' => 'sidebar-3',
                'description' => __( 'An optional widget area for your site footer', $this->_name ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => "</div>",
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            ) );

            register_sidebar( array(
                'name' => __( 'Footer Area Two', $this->_name ),
                'id' => 'sidebar-4',
                'description' => __( 'An optional widget area for your site footer', $this->_name ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => "</asidivde>",
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            ) );

            register_sidebar( array(
                'name' => __( 'Footer Area Three', $this->_name ),
                'id' => 'sidebar-5',
                'description' => __( 'An optional widget area for your site footer',  $this->_name ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => "</div>",
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            ) );
        }


        public function renderTemplate()
        {
            if (is_home())
                $this->home();
            else if (is_page($this->PlulzCarrinho->getFechamento()))
                $this->fechar();
            else if (is_page($this->PlulzCarrinho->getSucesso()))
                $this->sucesso();
            else if (is_page($this->PlulzAdmin->getContato()))
                $this->contato();
            else if (is_tag())
            {
                $this->tag();
            }
            else if (is_archive('produto'))
                $this->listProduto();
            else if (is_singular('produto'))
                $this->singleProduto();
            else if (is_search())
                $this->search();
            else if (is_404())
            {
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                if( $paged > 1)
                    $this->tag();
                else
                    $this->notFound();
            }
            else if (is_page())
                $this->page();
            else if (is_single())
                $this->single();

            exit;

        }

        public function preLoad()
        {

            $itensCarrinho = $this->PlulzCarrinho->getAllItens();

            $carrinho = array();
            foreach($itensCarrinho as $id => $quantidade)
            {
                $this->PlulzCarrinho->PlulzProduto->id = $id;
                $code = $this->PlulzCarrinho->PlulzProduto->getCode();
                $carrinho[$code] = $quantidade;
            }

            $fechamentoLink = '/?page_id=' . $this->PlulzCarrinho->getFechamento();

            $this->set('MetaDescription', $this->PlulzAdmin->getMetaDescription());

            if (is_home() || is_front_page())
                $title =  get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description', 'display' );
            else if(is_search())
            {
                $search = get_search_query();

                $search = str_replace('-', ' ', $search);

                $title = ucwords($search) . ' | ' . get_bloginfo( 'description', 'display' );

                $this->set('SearchQuery', $search);
            }
            else
                $title = wp_title( '|', false, 'right') . get_bloginfo('name');

            $this->set('Title', $title);
            $this->set('Atendimento', $this->PlulzAdmin->getAtendimento());
            $this->set('FooterText', $this->PlulzAdmin->getFooterText());
            $this->set('ItensCart', $this->PlulzCarrinho->itensInCart());
            $this->set('Carrinho', $carrinho);
            $this->set('FechamentoLink', $fechamentoLink );
            $this->set('HomeLink', $this->_homeUrl);

        }

        public function home()
        {
            global $wp_query;

            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query = array_merge( $wp_query->query, array(
                               'paged' => $paged,
                               'posts_per_page' => '21',
                               'post_type' =>  'produto'));

            query_posts($query);

            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $this->includeTemplate('Home');

            $this->includeThemeShared('Footer');
        }

        public function listProduto()
        {
            global $wp_query;

            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

            $query = array_merge( $wp_query->query, array(
                                            'paged' => $paged,
                                           'posts_per_page' => '21',
                                           'post_type' =>  'produto'));

            query_posts($query);

            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');
            $this->includeTemplate('Produtos');
            $this->includeThemeShared('Footer');
        }

        public function singleProduto()
        {
            global $post;

            if (have_posts())
                setup_postdata($post);

            $this->PlulzCarrinho->PlulzProduto->id = get_the_ID();

            $this->set('MetaDescription', $this->PlulzCarrinho->PlulzProduto->getMetaExcerpt());

            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $this->set('ListaTipo', get_the_term_list(get_the_ID(), 'tipo') );
            $this->set('ProdutoCodigo', $this->PlulzCarrinho->PlulzProduto->getCode());
            $this->set('ProdutoNoCarrinho', $this->PlulzCarrinho->isProductInCart( get_the_ID() ) );
            $this->set('ProdutoMinimo', $this->PlulzCarrinho->PlulzProduto->getMinimo() );
            $this->set('ProdutoImageGallery', $this->PlulzCarrinho->PlulzProduto->getProdutoImageGallery() );
            $this->set('ProdutoThumbnailLink', $this->PlulzCarrinho->PlulzProduto->getProdutoThumbnailLink() );
            $this->set('RelatedPosts', $this->PlulzCarrinho->PlulzProduto->getProdutoRelated('tipo'));

            $this->includeTemplate('Produto');

            $this->includeThemeShared('Footer');
        }

        /**
         * Fechamento do Orçamento
         */
        public function fechar()
        {
            $this->_postData = PlulzTools::getValue($this->_name);

            $this->_action = isset($this->_postData['action']) && !empty($this->_postData['action']) ? $this->_postData['action'] : PlulzTools::getValue('action');

            if ($this->_action == 'novo_orcamento')
            {
                $result = $this->PazzaniBrindesOrcamento->createOrcamento( $this->_postData );

                if ($result)
                {
                    $redirect   = '/?page_id=' . $this->PlulzCarrinho->getSucesso();

                    $this->PlulzCarrinho->clearCart();

                    wp_redirect($redirect);

                    exit;
                }
            }

            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');

            $itensCarrinho = $this->PlulzCarrinho->getAllItens();

            foreach($itensCarrinho as $id => $item)
            {
                $this->PlulzCarrinho->PlulzProduto->id = $id;

                $itensCarrinho[$id] = array(
                    'quantidade'    =>  $item,
                    'code'          =>  $this->PlulzCarrinho->PlulzProduto->getCode(),
                    'minimo'        =>  $this->PlulzCarrinho->PlulzProduto->getMinimo(),
                    'descricao'     =>  $this->PlulzCarrinho->PlulzProduto->getDescricao(),
                    'imagem'        =>  get_the_post_thumbnail( $id, 'mini-thumb' )
                );
            }

            $FormAction = $this->_homeUrl . '/?page_id=' . $this->PlulzCarrinho->getFechamento();

            $this->set('FormAction', $FormAction );
            $this->set('itensCarrinho', $itensCarrinho);
            $this->includeTemplate('Fechar');
            $this->includeThemeShared('Footer');
        }

        public function sucesso()
        {
            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');
            $this->includeTemplate('Sucesso');
            $this->includeThemeShared('Footer');
        }

        public function tag()
        {
            global $wp_query;

           $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
           $query = array_merge( $wp_query->query, array(
                    'paged' => $paged,
                    'posts_per_page' => '21',
                    'post_type' =>  'produto'));

            query_posts($query);

            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');
            $this->includeTemplate('Tags');
            $this->includeThemeShared('Footer');
        }

        public function contato()
        {
            $PostData = PlulzTools::getValue($this->_name);

            $Enviado = $PostData['Enviado'];

            if (isset($Enviado) && !empty($Enviado))
            {
                $nome = $PostData['Nome'];
                $email = $PostData['Email'];
                $telefone = $PostData['Telefone'];
                $mensagem = $PostData['Mensagem'];

                if(!$nome = $this->PlulzContato->validateNome($nome))
                {
                    $this->PlulzNotices->addError($this->_name, 'Preencha o campo Nome Corretamente.');
                }

                if(!$telefone = $this->PlulzContato->validateTelefone($telefone))
                {
                    $this->PlulzNotices->addError($this->_name, 'Preencha o campo Telefone Corretamente.');
                }

                if(!$email = $this->PlulzContato->validateEmail($email))
                {
                    $this->PlulzNotices->addError($this->_name, 'Preencha o E-mail Corretamente.');
                }

                if(!$mensagem = $this->PlulzContato->validateField($mensagem))
                {
                    $this->PlulzNotices->addError($this->_name, 'Esqueceu de digitar uma mensagem?');
                }


                if ($this->PlulzNotices->hasErrors())
                {
                    $this->set('emailSent', false);
                }
                else
                {
                    $template = array(
                        'img'       =>  $this->_assets . 'img/email/',
                        'message'   =>  file_get_contents($this->_serverAssets . "email/contato.txt")
                    );

                    $this->PlulzContato->sendContatoEmail($template, $nome, $email, $telefone, $mensagem);

                    $this->set('emailSent', true);
                }
            }

            $FormAction = $this->_homeUrl . '/?page_id=' . $this->PlulzAdmin->getContato();

            $this->set('FormAction', $FormAction );
            $this->set('PostData', $PostData);
            $this->includeThemeShared('Header');

            $this->includeThemeShared('Sidebar');
            $this->includeTemplate('Contato');
            $this->includeThemeShared('Footer');
        }
    }
}