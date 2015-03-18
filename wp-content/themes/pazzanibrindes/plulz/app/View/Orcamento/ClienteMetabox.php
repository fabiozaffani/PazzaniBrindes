<?php

    echo $this->PlulzForm->addRow(array(
        'name'      =>  array( 'cliente', 'origem'),
        'type'      =>  'select',
        'label'     =>  __( 'Origem', $this->_name ),
        'options'   =>  $this->origens,
        'required'  =>  true
    ), $this->metadata, true);

    //email
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  array( 'cliente', 'email'),
                        'type'      =>  'text',
                        'label'     =>  __( 'E-mail', $this->_name ),
                        'required'  =>  true
                    ), $this->metadata);
    // nome
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  array( 'cliente', 'nome'),
                        'type'      =>  'text',
                        'label'     =>  __( 'Nome', $this->_name ),
                        'required'  =>  true
                    ), $this->metadata);
    // telefone
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  array( 'cliente', 'telefone'),
                        'type'      =>  'text',
                        'label'     =>  __( 'Telefone', $this->_name ),
                        'required'  =>  true
                    ), $this->metadata);
    // prazo
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  array( 'pagamento', 'prazo'),
                        'type'      =>  'select',
                        'label'     =>  __( 'Forma de Pagamento', $this->_name ),
                        'options'   =>  array('15 dias da Data de Entrega', 'Entrada, 15, 25 dias', '50% entrada e 50% após recebimento', 'À Vista'),
                        'required'  =>  true
                    ), $this->metadata);

    // forma
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  array( 'pagamento', 'forma'),
                        'type'      =>  'select',
                        'label'     =>  __( 'Prazo de pagamento', $this->_name ),
                        'options'   =>  array('Boleto Bancário', 'Depósito em Conta'),
                        'required'  =>  true
                    ), $this->metadata);

    // frete
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  array( 'pagamento', 'frete'),
                        'type'      =>  'text',
                        'label'     =>  __( 'Frete', $this->_name ),
                        'value'     =>  'FOB - Pago pelo Cliente',
                        'required'  =>  true
                    ), $this->metadata);

?>