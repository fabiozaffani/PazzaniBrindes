<?php
        if (!empty($this->extraList)) :
            foreach ( $this->extraList as $key => $extra):

                echo $this->PlulzForm->addRow(array(
                                        'name'      =>  array('extraList', $key, 'nome'),
                                        'type'      =>  'text',
                                        'label'     =>  __('Nome', $this->_name)
                                ), $this->metadata);

                echo $this->PlulzForm->addRow(array(
                                        'name'      =>  array('extraList', $key, 'valor'),
                                        'type'      =>  'text',
                                        'label'     =>  __('Valor', $this->_name),
                                ), $this->metadata);

            endforeach;

        else :

            echo $this->PlulzForm->addRow(array(
                                    'name'      =>  array('extraList', 0, 'nome'),
                                    'type'      =>  'text',
                                    'label'     =>  __('Nome', $this->_name)
                            ), $this->metadata);

            echo $this->PlulzForm->addRow(array(
                                    'name'      =>  array('extraList', 0, 'valor'),
                                    'type'      =>  'text',
                                    'label'     =>  __('Valor', $this->_name),
                            ), $this->metadata);

        endif;
?>
<span class='extra_fields_button'><a href='#' id='add_extra' class='button'>Adicionar</a>
<a href='#' id='remove_extra' class='button'>Remover</a></span>