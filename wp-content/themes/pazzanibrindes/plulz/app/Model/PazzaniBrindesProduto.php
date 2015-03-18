<?php

if (!class_exists('PazzaniBrindesProduto'))
{
    class PazzaniBrindesProduto extends PlulzPost
    {
        /**
         * The fields that should be presented in money format to the user
         * @var array
         */
        protected $_moneyFields = array(
            'custo_unitario', 'venda_unitario', 'total'
        );

        /**
        * Lista com os valores de todos os produtos disponiveis no sistema
        * @var array
        */
        protected $_infoProdutos;

        /**
        * Guarda todos os valores referentes a algum produto específico
        * @var array
        */
        protected $_valorProduto;

        /**
        * Full list of codes of all registered products
        * @var array
        */
        protected $_codeList;

        /**
         * The list from the above that is not suposed to be saved on the database
         * @var array
         */
        protected $_excludedList = array(
            'comissao', 'minimo', 'extraList'
        );

        /**
         * Holds all the fields that should be displayed as read only
         * @var array
         */
        protected $_readOnlyFields = array(
            'descricao', 'custo_unitario', 'desconto'
        );

        /**
         * All the fields for each product
         * @var array
         */
        protected $_camposProduto = array(
            'id', 'quantidade','descricao','gravacoes','desconto','custo_unitario','venda_unitario','total', 'prazo', 'extraList'
        );

        public function __construct()
        {
            $this->_name  = 'pazzanibrindes_produto';

            $this->_postType = 'produto';

            $this->_postData = array(
                'config'    =>      array(
                    'labels' => array(
                        'name'              => __( 'Produtos', $this->_name ),
                        'singular_name'     => __( 'Produto', $this->_name ),
                        'add_new'           => __( 'Novo Produto', $this->_name ),
                        'add_new_item'      => __( 'Adicionar Produto', $this->_name ),
                        'edit'              => __( 'Editar', $this->_name ),
                        'search_items'      => __( 'Procurar Produto', $this->_name ),
                        'description'       => __( 'Cadastre todos os produtos que serão comercializados no site', $this->_name ),
                        'not_found'         => __( 'Nenhum produto encontrado', $this->_name ),
                        'not_found_in_trash'=> __( 'Nenhum produto na lixeira', $this->_name )
                    ),
                    'public'                => true,
                    'publicly_queryable'    => true,
                    'exclude_from_search'   => false,
                    'show_ui'               => true,
                    'menu_position'         => 5,
                    '_builtin'              => false,
                    '_edit_link'            => 'post.php?post=%d',
                    'capability_type'       => 'post',
                    'has_archive'           => true,
                    'hierarchical'          => false,
                    'rewrite'               => array(
                        'slug' => '/brindes', 'with_front' => false, 'slug_rewrite' => true
                    ),
                    'query_var'             => 'produto',
                    'supports'              => array('title','author', 'editor', 'revisions', 'thumbnail'),
                    'taxonomies'            => array('post_tag') // this is IMPORTANT
                ),
                'editPage'  =>  array(
                    'customColumns'         =>  array(
                        'title'             => __( 'Produto', $this->_name ),
                        'codigo'            => __( 'Código', $this->_name ),
                        'custo_unitario'    => __( 'Custo', $this->_name ),
                        'margem'            => __( 'MCU', $this->_name ),
                        'prazo'             => __( 'Prazo', $this->_name ),
                        'minimo'            => __( 'Mínimo', $this->_name ),
                        'author'            => __( 'Funcionário', $this->_name )
                    ),
                    'sortableColumns'   =>  array(
                        'codigo'         =>  'codigo',
                        'custo_unitario' =>  'custo_unitario',
                        'margem'         =>  'margem',
                        'prazo'          =>  'prazo',
                        'minimo'         =>  'minimo',
                    )
                )
            );

            // Set the custom taxonomies for this post type
            $customTaxonomies = array(
                0   =>  array(
                    'name'      =>  'tipo',
                    'args'      =>   array(
                        'label'         => 'Tipo',
                        'hierarchical'  => true,
                        'show_in_nav_menus' => true,
                        'query_var'     => true,
                        'rewrite'       => true,
                        'show_ui'       => true,
                        'query_var'     => 'tipo',
                        'rewrite'       => array(
                            'slug'  =>  'tipo', 'with_front' => false
                        ),
                    )
                ),
                1   =>  array(
                    'name'      =>  'fornecedores',
                    'args'      =>   array(
                        'label'         => 'Fornecedor',
                        'hierarchical'  => true,
                        'query_var'     => true,
                        'rewrite'       => true
                    )
                ),
                3   =>  array(
                    'name'          =>  'gravacao',
                    'args'          =>   array(
                        'label'         => 'Gravação',
                        'hierarchical'  => true,
                        'query_var'     => true,
                        'rewrite'       => true
                    ),
                    'extraFields'   =>  array(
                        0   =>  array(
                            'label'         =>  'Custo de Gravação',
                            'name'          =>  'custo_gravacao',
                            'description'   =>  'O custo de gravação por unidade'
                        ),
                        1   =>  array(
                            'label'         =>  'Coeficiente da Divisão',
                            'name'          =>  'coeficiente_divisao',
                            'description'   =>  'O número de unidades deste item para quando deve-se dobrar o valor do custo da gravação'
                        )
                    )
                )
            );

            $this->_serializedFields = array(
                'codigo', 'custo_unitario', 'margem', 'prazo', 'minimo'
            );

            $this->PlulzTaxonomy = new PlulzTaxonomy($this->_name, $this->_postType, $customTaxonomies);

            parent::__construct();

        }

        public function createTaxonomyCustomField( $tag )
        {
            $this->PlulzTaxonomy->createTaxonomyCustomField($tag);
        }

        public function saveTaxonomyCustomField( $termID )
        {
            $this->PlulzTaxonomy->saveTaxonomyCustomField( $termID );
        }

        public function validateInformacoesProduto( $info )
        {
            if(is_array($info))
            {
                foreach($info as $key => $value)
                {
                    if (empty($value))
                    {
                        $this->PlulzNotices->addError($this->_name, "O campo $key não pode ficar vazio");
                    }
                    else if (in_array($key, $this->_moneyFields) && !is_numeric($value))
                    {
                        $this->PlulzNotices->addError($this->_name, "O campo $key tem que ser numérico");
                    }
                    else if (is_numeric($value))
                    {
                        if ($value == 0)
                            $this->PlulzNotices->addError($this->_name, "O campo $key tem que ser maior que 0 ");
                    }
                }

                if ($this->PlulzNotices->hasErrors())
                    return false;
            }
            else
                return false;

            return true;
        }

        /**
         * Fetch all the product metadata info into the infoProduto variable
         * @return void
         */
        public function fetchProdutosInfo()
        {
            global $wpdb;

            // Get all meta information from all published(only) products metadata
            $sql = "SELECT wpm.meta_value, wp.ID
                    FROM {$wpdb->postmeta} as wpm
                    INNER JOIN {$wpdb->posts} as wp
                    ON wp.ID = wpm.post_id
                    WHERE wp.post_status ='publish' AND wpm.meta_key = '{$this->_name}'";

            $metaValues = $wpdb->get_results($sql);

            foreach ( $metaValues as $metaValue )
            {
                $infoProduto = unserialize($metaValue->meta_value);

                foreach ( $infoProduto as $info => $value )
                    $this->_infoProdutos[$metaValue->ID][$info] = $value;
            }

            // Generate the code list of all products
            foreach ($this->_infoProdutos as $produtoId => $data)
            {
                $codigos[] = $data['codigo'];
                $this->_codeList[0]  = ''; // first field empty
                $this->_codeList[$produtoId] = $data['codigo'];
            }

            natsort($this->_codeList);
        }

        /**
         * Get the pricing values from the current product
         *
         * @param string $newValues
         *
         * @param bool $saving
         * @param bool $applyComissao
         * @internal param $prod_id
         * @internal param int $post_id
         * @return array
         */
        public function fetchValorProduto( $newValues = '', $saving = false, $applyComissao = false )
        {
            $this->fetchPostMetadata();

            $defaults = array(
                'id'            =>  $this->id,
                'codigo'        =>  '',
                'minimo'        =>  0,
                'quantidade'    =>  0,
                'custo_unitario'=>  0,
                'venda_unitario'=>  0,
                'total'         =>  0,
                'prazo'         =>  '15',
                'descricao'     =>  'brinde personalizado',
                'frete'         =>  50.00,
                'margem'        =>  0.70,
                'uniEmbalagem'  =>  250,
                'valEmbalagem'  =>  6.00,
                'gravacoes'     =>  0,
                'comissao'      =>  0,
                'extras'        =>  null        // Guarda as opções extras que foram selecionadas
            );

            $values = $this->_replaceDefaults($defaults, $this->_metadata);

            if ( !empty($newValues) )
                $values = $this->_replaceDefaults($values, $newValues);

            extract($values);

            $venda_unitario = PlulzTools::ConvertMoneyToNumber($venda_unitario);

            if ( $quantidade == 0 )
                $quantidade = $minimo;

            if ($venda_unitario > 0 && $saving)
                $original_unitario = $venda_unitario;

            /*
             * Use some default gravacao if none is defined
             */

            if ( $gravacoes == 0 ) // gravacoes busca o tipo de gravacao (laser, 1, 2 cores, etc..)
            {
                $gravacoes_list = $this->getProdutoTiposGravacao();
                $gravacoesKeys = array_keys( $gravacoes_list );
                $gravacoes = array_slice($gravacoesKeys, 0, 1);
                $gravacoes = $gravacoes[0];
            }

            $custo_unitario = (float)PlulzTools::ConvertMoneyToNumber( $custo_unitario );


            $custoGravacao = $this->getCustoGravacaoUnitario( $quantidade, $gravacoes );


                                //calculo do custo unitário de gravacao 	  //custo unitario                  // custo das embalagens
            $totalCusto = $custoGravacao + ( $custo_unitario * $quantidade ) + $frete + ( $quantidade / $uniEmbalagem * $valEmbalagem );


            // Desconto, se houver , id por enquanto esta inutil ainda

            $desconto = $this->getDiscount($quantidade, $minimo);

            $totalCusto = $totalCusto * ( ( 100 - $desconto ) / 100 );


            // Custo unitario total final

            $custo_final_unitario = $totalCusto / $quantidade;

            $custo_final_unitario = $this->applyExtraValues( $custo_final_unitario, $extras);


            // Preco Unitário Final do Produto
            if ($venda_unitario > 0 && $saving)
                $venda_unitario = PlulzTools::ConvertMoneyToNumber($original_unitario);
            else
                $venda_unitario = PlulzTools::RoundUp( $custo_final_unitario * $margem + $custo_final_unitario );

            // Aplicar comissao, se houver
            if ($applyComissao)
                $venda_unitario = $this->applyComissao($venda_unitario, $comissao);


            // Preço total final do Produto

            $total = $venda_unitario * $quantidade;


            //Montando o array final para retorno

            $this->_valorProduto['id']               =   $this->id;
            $this->_valorProduto['minimo']           =   $minimo;
            $this->_valorProduto['quantidade']       =   $quantidade;
            $this->_valorProduto['custo_unitario']   =   number_format($custo_final_unitario, 2);
            $this->_valorProduto['desconto']         =   $desconto;
            $this->_valorProduto['venda_unitario']   =   number_format($venda_unitario, 2);
            $this->_valorProduto['total']            =   number_format($total, 2);
            $this->_valorProduto['prazo']            =   $prazo;
            $this->_valorProduto['descricao']        =   $descricao;
            $this->_valorProduto['gravacoes']        =   $gravacoes;
            $this->_valorProduto['comissao']         =   $comissao;

            if (isset($extras) && !empty($extras))
                $this->_valorProduto['extras']       =   $extras;
            else
                unset($this->_valorProduto['extras']);
        }

        /**
         * Retorna o custo de gravação unitário para algum produto e tipo de gravação específico
         *
         * @param $quantidade
         * @param $gravacao
         * @return float|int
         */
        public function getCustoGravacaoUnitario( $quantidade, $gravacao )
        {
            $customFields = $this->PlulzTaxonomy->getCustomTaxanomyExtraFieldValue( $gravacao );
            $valGravacao = $customFields['custo_gravacao'];
            $coefDivis   = $customFields['coeficiente_divisao'];

            // Make sure we dont do some shit like division by 0

            if ($valGravacao == 0 || $coefDivis == 0 )
                return 0;


            return ceil($quantidade / $coefDivis) * $valGravacao;
        }

        /**
         * Aplica um percentual em cima de qualquer valor numérico
         *
         * @param $valor
         * @param int $montante
         * @return int
         */
        public function applyComissao($valor, $montante = 0)
        {
           return $valor * $montante/100 + $valor;
        }

        /**
         * Method that returns the current product formated for the user
         * @param array $data
         * @param bool $saving
         * @param bool $applyComissao
         * @return array
         */
        public function getValoresProduto( $data = array(), $saving = false, $applyComissao = false )
        {
             // Lets remove the read only fields, they can never be changed from the front
             if (!empty($data))
             {
                 foreach($data as $key => $field)
                 {
                     if (in_array($key, $this->_readOnlyFields))
                         unset($data[$key]);
                 }
             }

             $this->fetchValorProduto($data, $saving, $applyComissao);

             $response = array();

             if ($saving)
             {
                 foreach($this->_valorProduto as $key => $value)
                 {
                     if(!in_array($key, $this->_excludedList))
                         $response[$key] = $value;
                 }
             }
             else
             {
                 $response = $this->_valorProduto;
                 $response['gravacoes_list'] = $this->getProdutoTiposGravacao();
                 $response['extraList']      = $this->getProdutoExtraFields();

                 foreach ($response as $key => $field)
                 {
                   if(in_array($key, $this->getMoneyFields()))
                       $response[$key] = $this->convertNumberToMoney( $field );
                 }
             }

             return $response;
        }

        /**
         * Retorna todos os tipos de gravação associadas com este produto
         * @return array|string
         */
        public function getProdutoTiposGravacao()
        {
            $tiposGravacao = array();

            $terms = wp_get_post_terms( $this->id, 'gravacao', 'orderby=name&hide_empty=0' );

            // If something goes wrong or the specified terms cant be found
            if (is_wp_error($terms))
            {
                $errors = $terms->get_error_messages();
                foreach ($errors as $erro)
                {
                    $this->_PlulzNotices->addError($this->_name, $erro);
                }
                return '';
            }

            foreach ( $terms as $term)
            {
                $tiposGravacao[$term->term_id] = $term->name;
            }

            return $tiposGravacao;
        }

        /**
         * Returns all the images associated with the current post, unset the other attachment types
         * @return mixed
         */
        public function getProdutoImageGallery()
        {
            $args = array(
                'post_type'     => 'attachment',
                'numberposts'   => -1, // bring them all
                'post_status'   => null,
                'post_mime_type'=> 'image/jpeg',
                'exclude'       => get_post_thumbnail_id( $this->id ),
                'post_parent'   => $this->id // post id with the gallery
                );

            $attachments = get_posts($args);

            $imageList = array();
            foreach ( $attachments as $image )
            {
                $alt    =   trim(strip_tags( get_post_meta($image->ID, '_wp_attachment_image_alt', true) ));
                $title  =   trim(strip_tags( $image->post_title ));

                if (empty($alt))
                    $alt = $title;

                $imageList[] = array(
                    'info'  =>  wp_get_attachment_image_src($image->ID), // default returns thumbnail
                    'large' =>  wp_get_attachment_image_src($image->ID, 'large'),
                    'alt'   =>  $alt,
                    'title' =>  $title
                );
            }

            return $imageList;
        }

        /**
         * Return the link for the produto(post) thumbnail
         * @return mixed
         */
        public function getProdutoThumbnailLink()
        {
            $info = wp_get_attachment_url( get_post_thumbnail_id($this->id) );
            return $info;
        }

        /**
         * Returns the minimum value of this product in order to allow addition
         * @return bool|int
         */
        public function getMinimo()
        {
            $this->fetchPostMetadata();

            if (isset($this->_metadata['minimo']))
                return $this->_metadata['minimo'];
            else
                return false;
        }

        /**
         * Return the code for the currenct product
         * @return string
         */
        public function getCode()
        {
            // If _codeList is set and not empty, lets not open an connection to the db
            if (isset($this->_codeList) && !empty($this->_codeList))
                $code = $this->_codeList[$this->id];

            // Else lets do the least resource intensive thing
            if ( isset($code) && !empty($code) )
            {
                return $code;
            }
            else
            {
                 $this->fetchPostMetadata();

                return $this->_metadata['codigo'];
            }
        }

        /**
         * Get the product short description
         * @return string
         */
        public function getDescricao()
        {
            $this->fetchPostMetadata();

            if (isset($this->_metadata['descricao']))
                return $this->_metadata['descricao'];
            else
                return false;
        }

        /**
         * Convert any number to money
         * @param $number
         * @return string
         */
        public function convertNumberToMoney( $number )
        {
            return 'R$ '. $number;
        }

        /**
         * Valida se a quantidade e maior que o minimo aceito
         * @param $quantidade
         * @internal param $prod_id
         * @return bool|int
         */
        public function validarQuantidade($quantidade)
        {
            $minimo = $this->getMinimo();

            if ($quantidade < $minimo)
                return $minimo;

            return false;

        }

        /**
         * Retorna os campos extras do produto solicitado
         * @return array|bool
         */
        public function getProdutoExtraFields()
        {
            $this->fetchPostMetadata();

            if(isset($this->_metadata['extraList']))
            {
                if( !PlulzTools::isMultiDimensionalArrayEmpty($this->_metadata['extraList']))
                    return $this->_metadata['extraList'];
                else
                    return false;
            }
            else
                return false;
        }

        public function getReadOnly()
        {
            return $this->_readOnlyFields;
        }

        /**
         * Função que calcula o desconto ponderando vários fatores referentes ao produto
         * atualmente estático em relação a quantidade / mínimo
         * mas pode ser acrescentado valores aos produtos que determinariam uma variação no desconto possível
         *
         * @param string $quantidade
         * @param string $minimo
         * @return float|int
         */
        public function getDiscount( $quantidade = '', $minimo = '')
        {
            // Calculando desconto se maior que 7 x o mínimo

            if (empty($quantidade) || empty($minimo))
                return 0;

            if ( $quantidade >= $minimo )
            {
                // desconto maximo de 6%
                $limit = 6;

                $rate = $quantidade / $minimo;

                $increase = $rate - 1; // qual valor sera adicionado

                $desconto = ( $increase <= $limit ) ?   $increase   : $limit;
            }
            else
                $desconto = 0;

            return $desconto;
        }

        /**
         * Funcão que calcula o total de valor extra que deverá ser somado/reduzido ao preço unitário
         * final do produto
         *
         * @param $custo_unitario
         * @param $extras
         * @internal param $id
         * @return int
         */
        public function applyExtraValues( $custo_unitario, $extras )
        {
            $addValue = 0;

            $allExtraFields = $this->getProdutoExtraFields();

            if(isset($allExtraFields) && !empty($allExtraFields))
            {
                foreach ($allExtraFields as $extraField)
                {
                    $nome = $extraField['nome'];
                    $valor= $extraField['valor'];

                    // se o campo atual existir
                    if (isset($extras[$nome]))
                        $addValue += $valor;
                }
            }

            $new_custo_unitario = $custo_unitario + $addValue;

            return $new_custo_unitario;
        }

        /**
         * Generates a code list of the products of the site
         * @return array
         */
        public function getCodeList()
        {
            $this->fetchProdutosInfo();

            return $this->_codeList;
        }

        public function getMoneyFields()
        {
            return $this->_moneyFields;
        }

        public function getCamposProduto()
        {
            return $this->_camposProduto;
        }


        /**
        * Changes the default lenght of the excerpt
        * @param $length
        * @return int
        */
        public function newExcerptLength($length)
        {
           return 6;
        }

        /**
        * Changes the default more text in the excerpts
        * @param $more
        * @return string
        */
        public function newExcerptMore($more)
        {
           return ' ...';
        }


        /**
        * Loads extra CSS classes in the posts
        * @param $classes
        * @return array
        */
        public function newPostCSSClasses( $classes )
        {
            global $post;

            foreach( (get_the_category($post->ID)) as $category)
                $classes[] = $category->category_nicename;

            $classes[]  =   'post';
            $classes[]  =   'clearfix';
            $classes[]  =   'round';

            return $classes;
        }


        /**
         * Seleciona produtos relacioandos ao produto atual pela taxonomia dele
         *
         * @param $taxonomy
         * @return WP_Query
         */
        public function getProdutoRelated($taxonomy)
        {
            $tipos = get_the_terms($this->id, $taxonomy);

            $allPosts = array();

            foreach($tipos as $tipo)
            {
                $query = array(
                    'post_type' =>  $this->_postType,
                    'taxonomy'  =>  'tipo',
                    'term'      =>  $tipo->slug);

                $relatedPosts = new WP_Query($query);
            }

            // Randomize the post order
            shuffle($relatedPosts->posts);

            if ($relatedPosts->found_posts > 6)
            {
                $relatedPosts->posts = array_slice($relatedPosts->posts, 0, 6);
                $relatedPosts->found_posts = 6;
                $relatedPosts->post_count = 6;
            }
            return $relatedPosts;
        }

    }
}
