<div id="plulzwrapper" class="wrap">

    <a id="plulzico" href="<?php echo $this->domain; ?>" target="_blank">Configurações Carrinho</a>
    <h2>Configurações Carrinho</h2>

<?php
    $this->PlulzMetabox->createMetaboxArea('70%');

        $this->PlulzForm->create($this->_adminOptionsUrl);

            settings_fields( $this->group );

            $this->PlulzMetabox->createMetabox('Páginas');

            echo $this->PlulzForm->addRow(array(
                                            'name'      =>   'fechamento',
                                            'type'      =>  'text',
                                            'label'     =>  __('Página de Fechamento', $this->_name),
                                            'required'  =>  true,
                                            'small'     =>  'Link página de fechamento. Ex: /fechamento'
                                        ), $this->data );

            echo $this->PlulzForm->addRow( array(
                                            'name'      =>  'sucesso',
                                            'type'      =>  'text',
                                            'label'     =>  __('Página de Sucesso', $this->_name),
                                            'required'  =>  true,
                                            'small'     =>  'Link página de sucesso. Ex: /sucesso'
                                        ), $this->data );

            $this->PlulzMetabox->closemetabox();



?>
        <p class="submit">
            <?php   echo $this->PlulzForm->addInput('submit', 'submit', 'enviar', 'Save Changes', array('class' => 'button-primary')); ?>
        </p>
<?php
        $this->PlulzForm->close();

        $this->PlulzMetabox->closeMetaboxArea();

        $this->PlulzMetabox->createMetaboxArea('29%');

            $this->lovedMetabox();
            $this->helpMetabox();
            $this->donateMetabox();

        $this->PlulzMetabox->closeMetaboxArea();
?>
</div>