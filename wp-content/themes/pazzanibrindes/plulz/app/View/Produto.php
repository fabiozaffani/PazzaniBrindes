<div id="container">

<?php
    if (have_posts()) :
?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <p id="breadcrumb">
                <a href="<?php echo $this->HomeLink;?>">Inicio</a> > <?php echo $this->ListaTipo; ?> > <?php the_title(); ?>
            </p>

            <h1 class="product_title page-title"><?php the_title(); ?></h1>

			    <div id="imagem-post">
                    <a href="<?php echo $this->ProdutoThumbnailLink; ?>" rel="lightbox" target="_blank">
                        <?php the_post_thumbnail('post-thumb'); ?>
                    </a>
                </div>

                <div id="content">

                    <?php the_content(); ?>
                    
                    <p class='AdicionalInfoTitle'>Informações Adicionais: </p>

                    <ul class='AdicionalInfo'>
                        <li> Código: <strong><?php echo $this->ProdutoCodigo; ?></strong></li>
                        <li> Quantidade Mínima: <strong><?php echo $this->ProdutoMinimo; ?></strong></li>
                    </ul>

                    <div id="ShoppingCart">

                        <p class="AjaxLoading"><img src="<?php echo $this->_assets; ?>js/loading.gif" height="50" width="50"/></p>
<?php
                        if( !$this->ProdutoNoCarrinho ):

?>                          <form id="FormularioCarrinho" action="<?php echo $this->HomeLink; ?>/?page=cart">

                                <input type="hidden" name="minimo"  value="<?php echo $this->ProdutoMinimo ;?>" />
                                <input type="hidden" name="id" id="id" value="<?php the_ID(); ?>" />
                                <label for="quantidade">Quantidade*</label>
                                <input name="quantidade" id="quantidade" type="text" maxlength="5" value="<?php echo $this->ProdutoMinimo; ?>" class="quantidade round" />

                                <a id="AdicionarCarrinho" class="button" href="#">
                                    <?php _e( "Adicionar ao Orçamento", $this->_name); ?>
                                </a>

                            </form>
<?php
                        else :

?>	                        <p>Item já adicionado ao orçamento, deseja alterar a <strong><a href="<?php echo $this->FechamentoLink; ?>" >quantidade</a></strong>?</p>

                            <a id="FecharCarrinho" href="<?php echo $this->FechamentoLink; ?>" class="button">
                                <?php _e( "Editar/Fechar Orçamento", $this->_name );?> &raquo;
                            </a>

                            <a id="ContinuarOrcamento" title="<?php _e( "Continuar Orçamento", $this->_name );?>" href="<?php echo $this->HomeLink; ?>">
                                <?php _e( "Continuar Orçamento", $this->_name );?>
                            </a>
<?php
                        endif;
?>
                    </div>
            </div>
<?php
            if (!empty($this->ProdutoImageGallery)) :
?>
                <ul id="galery-post">
<?php
                        $outputGallery = '';
                        foreach ( $this->ProdutoImageGallery as $image ) :

                            $link   = $image['info'][0];
                            $large  = $image['large'][0];
                            $w      = $image['info'][1];
                            $h      = $image['info'][2];
                            $alt    = $image['alt'];
                            $title  = $image['title'];

                            $outputGallery .= "<li><a href='{$large}' target='_blank' rel='lightbox'><img src='{$link}' alt='{$alt}' title='{$title}' w='{$w}' h='{$h}' /></a></li>";

                        endforeach;

                        echo $outputGallery;
?>
                </ul>
<?php
            endif;
?>
<?php   $this->includeThemeShared('Related');   ?>

        </div>

        <div id="produto-contato" class="clearfix round">

            <?php $this->PlulzForm->create($this->FormAction); ?>

            <?php   do_action('front_notices'); ?>


            <ul class="formList clearfix" id="form-contato-produto">

                    <li class="title">
                        Fale com um Vendedor
                    </li>

                    <?php echo $this->PlulzForm->addInput('hidden', 'ContatoCodigo', 'pazzanibrindes_Codigo', $this->ProdutoCodigo); ?>

                    <div id="form-interna">
                        <div class="left">
                            <li class="nome">
                                <?php echo $this->PlulzForm->addRow(array(
                                'type' => 'text',
                                'name' => 'Nome',
                                'label' => 'Nome*',
                                'class' => 'round'
                            ), $this->PostData);
                                ?>
                            </li>
                            <li class="email">
                                <?php echo $this->PlulzForm->addRow(array(
                                'type' => 'text',
                                'name' => 'Email',
                                'label' => 'E-mail*',
                                'class' => 'round'
                            ), $this->PostData);
                                ?>
                            </li>
                            <li class="telefone">
                                <?php echo $this->PlulzForm->addRow(array(
                                'type' => 'text',
                                'name' => 'Telefone',
                                'label' => 'Telefone*',
                                'class' => 'round'
                            ), $this->PostData);
                                ?>
                            </li>
                        </div>
                        <div class="right">
                            <li class="textarea right">
                                <?php echo $this->PlulzForm->addRow(array(
                                'type' => 'textarea',
                                'name' => 'Mensagem',
                                'label' => 'Mensagem*',
                                'class' => 'round'
                            ), $this->PostData);
                                ?>
                            </li>
                        </div>
                        <li class="submit">
                            <?php echo $this->PlulzForm->addInput('submit', 'Enviar', 'ReplacementSubmit', 'Enviar', array('class' => 'button')); ?>
                            <?php echo $this->PlulzForm->addInput('hidden', 'Enviado', 'FaleConoscoProdutoEnviado', 'true'); ?>
                        </li>
                    </div>
                </ul>
            <?php $this->PlulzForm->close(); ?>

        </div>
<?php
    endif;
?>

</div>