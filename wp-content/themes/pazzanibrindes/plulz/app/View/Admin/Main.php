<div id="plulzwrapper" class="wrap">

    <a id="plulzico" href="<?php echo $this->domain; ?>" target="_blank">Configurações Gerais</a>
    <h2>Configurações Gerais</h2>

<?php
    $this->PlulzMetabox->createMetaboxArea('70%');

        $this->PlulzForm->create($this->_adminOptionsUrl);

            settings_fields( $this->group );

            $this->PlulzMetabox->createMetabox('Geral');

            echo $this->PlulzForm->addRow(array(
                                               'name'      =>  'metaDescription',
                                               'type'      =>  'textarea',
                                               'label'     =>  'Meta Description',
                                               'required'  =>  true,
                                               'small'     =>  'Descrição Meta Tag da página inicial do site'
                                           ), $this->data );

            echo $this->PlulzForm->addRow(array(
                                            'name'      =>  'atendimento',
                                            'type'      =>  'text',
                                            'label'     =>  'Telefone de Atendimento do Site',
                                            'required'  =>  true,
                                            'small'     =>  'Pode conter texto'
                                        ), $this->data );

            echo $this->PlulzForm->addRow(array(
                                            'name'      =>  'contato',
                                            'type'      =>  'text',
                                            'label'     =>  'Página de Contato do Site',
                                            'required'  =>  true,
                                            'small'     =>  'ID da página de contato do site'
                                        ), $this->data );

            echo $this->PlulzForm->addRow(array(
                                            'name'      =>  'footerText',
                                            'type'      =>  'text',
                                            'label'     =>  'Texto do Footer',
                                            'required'  =>  true,
                                            'small'     =>  'Texto de informações que aparecerá na parte inferior do rodapé da página'
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