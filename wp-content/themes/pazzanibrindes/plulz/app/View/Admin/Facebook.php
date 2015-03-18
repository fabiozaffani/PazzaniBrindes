<div id="plulzwrapper" class="wrap">

    <a id="plulzico" href="<?php echo $this->domain; ?>" target="_blank">Facebook General Config</a>
    <h2>Facebook General Config</h2>

<?php

    $this->PlulzMetabox->createMetaboxArea('70%');

        $this->PlulzForm->create($this->_adminOptionsUrl);

            settings_fields( $this->group );

            $this->PlulzMetabox->createMetabox('Páginas');

            echo $this->PlulzForm->addRow(array(
                                        'name'      =>  'app',
                                        'type'      =>  'text',
                                        'label'     =>  __('APP ID', $this->_name),
                                        'required'  =>  true,
                                        'small'     =>  'Need help creating your App ID? <a href="http://www.plulz.com/how-to-create-a-facebook-app" target="_blank">Find How to Create a Facebook APP</a>'
                                    ), $this->data );

            echo $this->PlulzForm->addRow(array(
                                        'name'      =>  'secret',
                                        'type'      =>  'text',
                                        'label'     =>  __('APP Secret', $this->_name),
                                        'required'  =>  true,
                                        'small'     =>  ''
                                    ), $this->data );

            echo $this->PlulzForm->addRow(array(
                                        'name'      =>  'admin',
                                        'type'      =>  'text',
                                        'label'     =>  __('Admin/User ID', $this->_name),
                                        'required'  =>  true,
                                        'small'     =>  'Need help to find your User ID? <a href="http://www.plulz.com/how-to-get-my-facebook-user-id" target="_blank">Find To Find My User ID</a>'
                                    ), $this->data );

            echo $this->PlulzForm->addRow(array(
                                        'name'      =>  'fanpage',
                                        'type'      =>  'text',
                                        'label'     =>  __('Facebook Fan Page Link', $this->_name),
                                        'required'  =>  true,
                                        'small'     =>  __('Could be other links but normally it\'s the fan page', $this->_name)
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