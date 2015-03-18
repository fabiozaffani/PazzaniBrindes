        </div> <!-- Fim do div#center-inner -->
    </div> <!-- fim do div#center -->
</div> <!-- fim do div#wrapper -->
<div id="footer">

    <div id="footer-inner" class="transparencia">

        <div id="footer-inner-top" class="clearfix">

            <div class="widget-area first">
                <?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>

                        <?php dynamic_sidebar( 'sidebar-3' ); ?>

                <?php endif; ?>
            </div><!-- #first .-- #third .widget-area -->

        </div>

        <div id="footer-inner-lower">

            <p class="footer-bottom"><?php echo $this->FooterText;?></p>

        </div>

    </div>

</div>
<div id="signup">
    <div id="signup-ct">

        <div id="signup-header">
            <h2>Atendimento Personalizado</h2>
            <p>Preencha os dados abaixo para um atendimento personalizado.</p>
            <a class="modal_close" href="#"></a>
        </div>

        <?php $this->PlulzForm->create($this->FormAction); ?>

            <div class="txt-fld">
                <?php echo $this->PlulzForm->addRow(array(
                        'type' => 'text',
                        'name' => 'NomeSignup',
                        'label' => 'Nome Completo'
                    ), $this->PostData);
                ?>
            </div>
            <div class="txt-fld">
                <?php echo $this->PlulzForm->addRow(array(
                        'type' => 'text',
                        'name' => 'EmailSignup',
                        'label' => 'E-mail'
                    ), $this->PostData);
                ?>
            </div>
            <div class="txt-fld">
                <?php echo $this->PlulzForm->addRow(array(
                        'type' => 'text',
                        'name' => 'TelefoneSignup',
                        'label' => 'Telefone'
                    ), $this->PostData);
                ?>
            </div>
            <div class="btn-fld">
                <?php echo $this->PlulzForm->addInput('submit', 'Enviar', 'ReplacementSubmit', 'Enviar', array('class' => 'button')); ?>
                <?php echo $this->PlulzForm->addInput('hidden', 'Enviado', 'RegistreseEnviado', 'true'); ?>
            </div>

        <?php $this->PlulzForm->close(); ?>

    </div>
</div>
<!-- end footer -->
<?php wp_footer(); ?>

</body>
</html>