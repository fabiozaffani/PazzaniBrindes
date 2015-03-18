<div id="container">

    <div <?php post_class(); ?>>

<?php

  //Se o email foi enviado enviar uma mensagem de agradecimento
  if(isset($this->emailSent) && $this->emailSent == true) :
?>

    <h1>Sucesso!</h1>
        <!-- Google Code for Contato Sucesso Conversion Page -->
        <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 1061285384;
        var google_conversion_language = "pt";
        var google_conversion_format = "3";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "n1sJCPC8gAIQiNyH-gM";
        var google_conversion_value = 0;
        /* ]]> */
        </script>
        <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
        <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1061285384/?label=n1sJCPC8gAIQiNyH-gM&amp;guid=ON&amp;script=0"/>
        </div>
        </noscript>
      <p>Obrigado. Seu email foi enviado e entraremos em contato em breve.</p>
      <p>Voltar para a <a href="<?php bloginfo('url');?>">Página Principal</a></p>
<?php
    // Caso haja erros...
    else :
?>
        <h1>Fale Conosco</h1>
        <!-- Google Code for Pagina de Contato Conversion Page -->
        <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 1061285384;
        var google_conversion_language = "pt";
        var google_conversion_format = "3";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "JvIaCKjJlgIQiNyH-gM";
        var google_conversion_value = 0;
        /* ]]> */
        </script>
        <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
        <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1061285384/?label=JvIaCKjJlgIQiNyH-gM&amp;guid=ON&amp;script=0"/>
        </div>
        </noscript>

        <?php $this->PlulzForm->create($this->FormAction); ?>

            <p>Voc&ecirc; pode entrar em contato conosco através do telefone <strong>(11) 3963-4549 </strong>, pelo e-mail <strong>contato@pazzanibrindes.com.br</strong> ou pelo formulário abaixo:</p>

            <p><small>* campos obrigatórios</small></p>

            <?php   do_action('front_notices'); ?>


            <ul class="formList clearfix">
                <li class="clearfix">
                    <?php echo $this->PlulzForm->addRow(array(
                                                'type' => 'text',
                                                'name' => 'Nome',
                                                'label' => 'Nome*',
                                                'id' => 'FaleConoscoPaginaNome',
                                                'class' => 'round'
                                ), $this->PostData);
                    ?>
                </li>
                <li class="clearfix">
                    <?php echo $this->PlulzForm->addRow(array(
                                                'type' => 'text',
                                                'name' => 'Email',
                                                'label' => 'E-mail*',
                                                'id' => 'FaleConoscoPaginaEmail',
                                                'class' => 'round'
                                ), $this->PostData);
                    ?>
                </li>
                <li class="clearfix">
                    <?php echo $this->PlulzForm->addRow(array(
                                                'type' => 'text',
                                                'name' => 'Telefone',
                                                'label' => 'Telefone*',
                                                'id' => 'FaleConoscoPaginaTelefone',
                                                'class' => 'round'
                                ), $this->PostData);
                    ?>
                </li>
                <li class="textarea clearfix">
                    <?php echo $this->PlulzForm->addRow(array(
                                                'type' => 'textarea',
                                                'name' => 'Mensagem',
                                                'label' => 'Mensagem*',
                                                'id' => 'FaleConoscoPaginaMensagem',
                                                'class' => 'round'
                                ), $this->PostData);
                    ?>
                </li>
                <li class="submit clearfix">
                    <?php echo $this->PlulzForm->addInput('submit', 'Enviar', 'ReplacementSubmit', 'Enviar', array('class' => 'button')); ?>
                </li>
            </ul>
            <?php echo $this->PlulzForm->addInput('hidden', 'Enviado', 'FaleConoscoPaginaEnviado', 'true'); ?>

        <?php $this->PlulzForm->close(); ?>
<?php
    endif;
?>
    </div>
</div> <!-- Fim do #Content -->