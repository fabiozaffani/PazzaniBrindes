<?php
/**
 *
 * Class responsible for returning the values do orçamento atual
 *
 * Esta classe extends a PlulzPosAbstract
 * 
 */

if (!class_exists('PazzaniBrindesProdutoController'))
{
    class PazzaniBrindesProdutoController extends PlulzPostControllerAbstract
    {
        public function __construct()
        {
            $this->_post        =   new PazzaniBrindesProduto();
            
            $this->_nonce       =   'pazzanibrindes_produto_nonce';

            $this->_metaboxCSS  =   $this->_assets . 'css/plulz-metabox-style.css';

            $this->setFilter( 'excerpt_length', array('_post', 'newExcerptLength') );
            $this->setFilter( 'excerpt_more', array('_post',  'newExcerptMore') );
            $this->setAction( 'post_class', array('_post',  'newPostCSSClasses') );

            parent::__construct();

        }

        public function startAdmin()
        {
            parent::startAdmin();

            // Extra fields for gravacao custom taxonomy
            $this->setAction( 'gravacao_edit_form_fields', array('_post', 'createTaxonomyCustomField') );
            $this->setAction( 'edited_gravacao', array('_post', 'saveTaxonomyCustomField') );
        }

        public function startPostBack()
        {
            parent::startPostBack();
            $this->setAction( 'add_meta_boxes', 'customMetaboxes' );
        }

        public function startPostFront()
        {
            $this->setAction( 'post_class', array('_post',  'newPostCSSClasses') );
        }

        public function adminAssets()
        {
            wp_register_style( 'PlulzMetaboxStylesheet', $this->_assets . 'css/plulz-metabox-style.css' );
            wp_enqueue_style( 'PlulzMetaboxStylesheet' );

            wp_enqueue_script( 'PlulzToolsJS', $this->_assets . 'js/tools.js', array( 'jquery' ) );
            wp_enqueue_script( 'PlulzProdutoJS', $this->_assets . 'js/produto.js', array( 'jquery', 'PlulzToolsJS') );

            wp_localize_script(
                'PlulzProdutoJS',
                'PazzaniBrindesProduto',
                array(
                    'ajaxurl'               =>  $this->_adminAjaxUrl,
                    // generate a nonce with a unique ID "plulzajax-post-comment-nonce"
                    // so that you can check it later when an AJAX request is sent
                    'produtoAjaxNonce'      =>  wp_create_nonce( $this->_nonce ),
                    'actionProduto'         =>  'produto-ajax',
                    'basename'              =>  $this->_name,
                    'extraFields'           =>  json_encode(array('nome', 'valor'))
                )
            );
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

                $produtoInfo = $_POST[$this->_name];
            }
            else
            {
                $produtoInfo = $data;
            }

            $this->_post->id = $post_id;

            $this->_post->validateInformacoesProduto($produtoInfo);

            // Lets clear the invalid output for the fields
            foreach($produtoInfo['extraList'] as $key => $extra)
            {
                $produtoInfo['extraList'][$key]['nome'] = PlulzTools::NormalizeString($extra['nome']);
            }

            // Independentemente se houver erros ou não, precisa ser salvo para nao fuder o nome do orcamento
            $updated = $this->_post->savePostCustomData( $produtoInfo );

            if ($this->PlulzNotices->hasErrors())
                $this->_post->savePostAsDraft(array(&$this, 'handlePost'));

            return $updated;
        }

        public function customMetaboxes()
        {
            add_meta_box('produto_metabox', 'Informações do Produto', array( &$this, 'customMetaboxProduto'));

            add_meta_box('produto_extra_metabox', 'Informações Extras do Produto',array( &$this, 'customMetaboxProdutoExtra'));
        }

        /**
         * Metabox exibido na pagina de criação de produto post_type=produto
         * @param $post
         * @return void
         */
        public function customMetaboxProduto( $post )
        {
            $this->_post->id = $post->ID;
            $this->_post->fetchPostMetadata();

            $this->outputNonce();

            $this->set('metadata', $this->_post->getMetadata());
            $this->includeTemplate('InfoMetabox', 'Produto');
        }

        /**
         * Adiciona campos para valores aleatórios específicos de cada produto que impactam no preço
         * final dele
         * @param $post
         * @return void
         */
        public function customMetaboxProdutoExtra( $post )
        {
            $this->_post->id = $post->ID;
            $this->_post->fetchPostMetadata();

            $this->outputNonce();

            $metadata = $this->_post->getMetadata();

            $this->set('metadata', $metadata);
            $this->set('extraList', $metadata['extraList']);
            $this->includeTemplate('ExtraMetabox', 'Produto');
        }

        /*
        public function transferenciaProdutos()
        {
            return;
            set_time_limit( 3600 );

            // ACERTAR OS TELEFONES

            $produtosCSV = fopen($this->_appControllerDir . 'backup/produtos.csv', "r");

            $header = fgetcsv($produtosCSV, 1000, ";");

            $produtos = array();
            while ($row = fgetcsv($produtosCSV, 1000, ";") )
            {
                $arr = array();

                foreach ($header as $i => $col)
                {
                    $arr[$col] = $row[$i];
                }

                $produtos[] = $arr;
            }

            foreach($produtos as $produto)
            {
                $data = array(
                    'codigo'            =>  $produto['cod_prod'],
                    'descricao'         =>  $produto['prod_description'],
                    'custo_unitario'    =>  $produto['preco'],
                    'frete'             =>  $produto['frete'],
                    'prazo'             =>  $produto['prazo_entrega'],
                    'minimo'            =>  $produto['pedido_min'],
                    'margem'            =>  $produto['mcu_min'],
                    'valEmbalagem'      =>  $produto['valor_embalagem'],
                    'uniEmbalagem'      =>  $produto['embalagem_unidades'],
                    'codigoFornecedor'  =>  $produto['cod_fornecedor']
                );

                $post = array(
                    'comment_status' => 'open',
                    'ping_status' => 'closed',
                    'pinged' => '',
                    'post_author' => '',
                    'post_category' => '',
                    'post_content' => 'nhe',
                    'post_date' => date('Y-m-d G:i:s'),
                    'post_date_gmt' => date('Y-m-d G:i:s'),
                    'post_parent' => '',
                    'post_password' => '',
                    'post_status' => 'publish',
                    'post_title' => '',
                    'post_type' => $this->_postType,
                );

                $id = wp_insert_post( $post );

                $this->transferenciaSaveCustomMetaboxContent( $id, $data);
            }
            die('produtos done');
        }*/
    }
}
