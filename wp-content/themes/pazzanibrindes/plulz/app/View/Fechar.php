<?php

global $current_user;

get_currentuserinfo();

$usermeta = get_user_meta($current_user->ID);
$telefone = $usermeta['telefone'][0];

if(is_user_logged_in())
{
    $_POST['pazzanibrindes']['cliente']['email'] = $current_user->user_email;
    $_POST['pazzanibrindes']['cliente']['nome'] = $current_user->user_firstname . ' ' . $current_user->user_lastname;
    $_POST['pazzanibrindes']['cliente']['telefone'] = $telefone;
}

?>
<div id="container">

    <div <?php post_class(); ?>>

    	<div id="GoogleTracking">
            <!-- Google Code for Resumo de Or&ccedil;amento Conversion Page -->
            <script type="text/javascript">
            /* <![CDATA[ */
            var google_conversion_id = 1061285384;
            var google_conversion_language = "pt";
            var google_conversion_format = "3";
            var google_conversion_color = "ffffff";
            var google_conversion_label = "wElsCKDKlgIQiNyH-gM";
            var google_conversion_value = 0;
            /* ]]> */
            </script>
            <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
            </script>
            <noscript>
            <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1061285384/?label=wElsCKDKlgIQiNyH-gM&amp;guid=ON&amp;script=0"/>
            </div>
            </noscript>
		</div>

<?php

        if (!$this->ItensCart):

?>		    <h3>Sua lista de orçamento está vazia</h3> <br />

            <a href="<?php echo $this->HomeLink; ?>">&laquo; <?php _e('Continuar orçando', $this->_name); ?> </a>
<?php

        else:

?>
            <h3> <?php _e('Veja abaixo os detalhes da sua cotação:', $this->_name ); ?> </h3>

            <?php   do_action('front_notices'); ?>

            <?php $this->PlulzForm->create($this->FormAction); ?>

                <table id="CarrinhoDetalhes">

                    <thead>
                        <tr>
                            <th class="CarrinhoDetalhes_codigo"><?php  _e( 'Cód.', $this->_name );?></th>
                            <th class="CarrinhoDetalhes_produto"><?php _e( 'Produto(s)', $this->_name );?></th>
                            <th class="CarrinhoDetalhes_imagem"><?php  _e( 'Imagem', $this->_name );?></th>
                            <th class="CarrinhoDetalhes_quantidade" ><?php   _e( 'Quantidade', $this->_name );?></th>
                            <th class="CarrinhoDetalhes_acoes" ></th>
                        </tr>
                    </thead>

                    <tfoot>

                        <tr>
                            <td colspan="5">

                                <h3 class="fecharContatoTitle"><?php _e('Informações para Contato', $this->_name ); ?></h3>

                                    <ul class="formList">

                                        <li>
                                            <?php echo $this->PlulzForm->addRow(array(
                                                                        'type' => 'text',
                                                                        'name' => array('cliente', 'email'),
                                                                        'label' => 'E-mail*',
                                                                        'id' => 'email',
                                                                        'class' => 'round'
                                                        ), $_POST[$this->_name]);
                                            ?>
                                        </li>

                                        <li>
                                            <?php echo $this->PlulzForm->addRow(array(
                                                                        'type' => 'text',
                                                                        'name' => array('cliente', 'nome'),
                                                                        'label' => 'Nome*',
                                                                        'id' => 'nome',
                                                                        'class' => 'round'
                                                        ), $_POST[$this->_name]);
                                            ?>
                                        </li>

                                        <li>
                                            <?php echo $this->PlulzForm->addRow(array(
                                                                        'type' => 'text',
                                                                        'name' => array('cliente', 'telefone'),
                                                                        'label' => 'Telefone*',
                                                                        'id' => 'telefone',
                                                                        'class' => 'round'
                                                        ), $_POST[$this->_name]);
                                            ?>
                                        </li>
                                        <li class="submit clearfix">
                                            <?php echo $this->PlulzForm->addInput('submit', 'enviado', 'ReplacementSubmit', 'Solicitar Orçamento', array('class' => 'button')); ?>
                                        </li>

                                    </ul>

                                    <?php echo $this->PlulzForm->addInput( 'hidden', 'action', 'action', 'novo_orcamento' ) ;?>

                                    <a href="<?php echo $this->HomeLink; ?>">&laquo; <?php _e('Voltar', $this->_name ); ?>  </a>

                            </td>
                        </tr>

                    </tfoot>

                    <tbody>

<?php
                        $count = 0;
                        foreach ( $this->itensCarrinho as $id => $item ) :

                            $code       = $item['code'];
                            $quantidade = $item['quantidade'];
                            $minimo     = $item['minimo'];
                            $descricao  = $item['descricao'];
                            $imagem     = $item['imagem'];
?>
                            <tr id="<?php echo $id; ?>" >

                                <td class="CarrinhoDetalhes_codigo">
                                    <?php echo $this->PlulzForm->addInput( 'hidden', array('produto', $count, 'id'), null, $id ) ;?>
                                    <?php echo $code; ?>
                                </td>

                                <td class="CarrinhoDetalhes_produto">
                                    <?php echo $descricao; ?>
                                </td>

                                <td class="CarrinhoDetalhes_imagem">
                                    <?php echo $imagem; ?>
                                </td>

                                <td class="CarrinhoDetalhes_quantidade">
                                    <?php echo $this->PlulzForm->addInput( 'text', array('produto', $count, 'quantidade'), null, $quantidade, array( 'maxlength' => 5, 'class' => 'round quantidade') ) ;?>
                                </td>

                                <td class="CarrinhoDetalhes_acoes">

                                    <?php echo $this->PlulzForm->addInput( 'hidden', array('produto', $count, 'minimo'), null, $minimo, array( 'class' => 'minimo' ) ) ;?>

                                    <a href="#" rel="<?php echo $id; ?>" title="Atualizar" class="CarrinhoDetalhes_atualizar">
                                        <?php _e( 'Atualizar', $this->_name ); ?>
                                    </a>

                                    <a href="#" rel="<?php echo $id; ?>" title="Remover" class="CarrinhoDetalhes_remover">
                                        <?php _e( 'Remover', $this->_name ); ?>
                                    </a>

                                </td>

                            </tr>
<?php
                            $count++;
                        endforeach;
?>
                    </tbody>

                </table>

            <?php $this->PlulzForm->close(); ?>
<?php
    endif;
?>

    </div>

</div>
