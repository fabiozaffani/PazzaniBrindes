<div id="sidebar" class="widget-area round">


    <div class="widget" id="CarrinhoSidebar">
        <h3 class="carrinhoSidebarTitle">
<?php
            if ( $this->ItensCart == 0 ) :
                echo "Orçamento Vazio";
            else:
                echo "Meu Orçamento";
            endif;
?>
        </h3>

        <table id="CarrinhoSidebar_tabela" <?php if ( $this->ItensCart == 0 ) { echo 'style="display:none;"'; } ?>>

            <thead>
                <tr>
                <th class="CarrinhoSidebar_produto">
                    <?php _e('Produto', $this->_name ); ?>
                </th>
                <th class="CarrinhoSidebar_quantidade">
                    <?php _e('Qtde', $this->_name ); ?>
                </th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td id="CarrinhoSidebar_fechar_wrapper" colspan="2">

                    <a class="button" id="CarrinhoSidebar_fechar" href="<?php echo $this->FechamentoLink; ?>" >
                        <?php _e('Editar/Fechar Orçamento', $this->_name );?>
                    </a>

                </td>
            </tr>
        </tfoot>

        <tbody>
<?php
            if( $this->ItensCart == 0 ):
?>          	<tr>
                    <td colspan="2">
                    </td>
                 </tr>
<?php
            else :

                foreach ($this->Carrinho as $code => $quantidade):

?>			        <tr>

                        <td class="CarrinhoSidebar_produto">
                            <?php echo $code; ?>
                        </td>

                        <td class="CarrinhoSidebar_quantidade">
                            <?php echo $quantidade;  ?>
                        </td>

                    </tr>

<?php			endforeach;
            endif;
?>
            </tbody>
        </table>
    </div> <!-- Fim do #cart_content_sidebar -->

    <!-- Show Categories -->
    <div class="widget">
        <h3 class="widget-title">Categorias</h3>
        <ul id="categorias">
<?php       $args = array(
                'orderby'            => 'name',
                'order'              => 'ASC',
                'show_last_update'   => 0,
                'style'              => 'list',
                'show_count'         => 0,
                'hide_empty'         => 0,
                'use_desc_for_title' => 1,
                'child_of'           => 0,
                'hierarchical'       => true,
                'title_li'           => '',
                'show_option_none'   => __('Nenhuma Categoria'),
                'number'             => NULL,
                'echo'               => 1,
                'depth'              => 0,
                'current_category'   => 1,
                'pad_counts'         => 0,
                'taxonomy'           => 'tipo'
            );

            wp_list_categories($args); ?>

        </ul>
    </div>

    <!-- Contatos -->

    <div class="widget">
        <h3 class="widget-title">Fale Conosco</h3>
        <ul id="contato">
            <li class="telefone">(11) 3963-4549</li>
            <li class="email">contato@pazzanibrindes.com.br</li>
        </ul>
    </div>

    <!-- Outras Empresas -->

     <div class="widget">

        <h3 class="widget-title">Empresas do Grupo</h3>

         <ul id="empresas_relacionadas">
            <li><a href="<?php echo $this->HomeLink;?>" target="_blank" >Brindes</a></li>
            <li><a href="http://www.descontoparamulheres.com.br" target="_blank" >Desconto para Mulheres</a></li>
            <li><a href="http://www.guitarpro6.com.br" target="_blank" >Guitar Pro</a></li>
            <li><a href="http://www.pazzanicastanhas.com.br" target="_blank" >Castanhas de Caju</a></li>
       <!--     <li><a href="<?php echo $this->HomeLink;?>/mapa-do-site" target="_blank" >Mapa do Site</a></li> -->
        </ul>

    </div>

    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' );   ?>
	<?php endif; ?>

    <?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-2' );   ?>
	<?php endif; ?>

</div>