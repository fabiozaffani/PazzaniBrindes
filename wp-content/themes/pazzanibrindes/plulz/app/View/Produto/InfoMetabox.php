<?php

    //codigo
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'codigo',
                        'type'      =>  'text',
                        'label'     =>  'Código',
                        'required'  =>  true,
                        'small'     =>  'O código interno do produto'
                    ), $this->metadata);

    //descricao
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'descricao',
                        'type'      =>  'text',
                        'label'     =>  'Descrição Curta',
                        'required'  =>  true,
                        'small'     =>  'Uma descrição com poucas palavras do produto. Ex.: Caneta de Plástico'
                    ), $this->metadata);

    //custo unitario
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'custo_unitario',
                        'type'      =>  'text',
                        'label'     =>  'Custo Compra do Produto',
                        'required'  =>  true,
                        'small'     =>  'O custo de compra do produto deve ficar no seguinte formato 3.99'
                    ), $this->metadata);

    // prazo
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'prazo',
                        'type'      =>  'text',
                        'label'     =>  'Prazo de Entrega',
                        'required'  =>  true,
                        'small'     =>  'Prazo de entrega do produto em dias. Ex: 15'
                    ), $this->metadata);

    // frete
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'frete',
                        'type'      =>  'text',
                        'label'     =>  'Frete de Envio',
                        'required'  =>  true,
                        'small'     =>  'O gasto com frete para fazer o produto. Formato 41.00'
                    ), $this->metadata);

    // minimo
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'minimo',
                        'type'      =>  'text',
                        'label'     =>  'Quantidade Mínima',
                        'required'  =>  true,
                        'small'     =>  'A quantidade mínima para venda deste produto'
                    ), $this->metadata);

    // margem
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'margem',
                        'type'      =>  'text',
                        'label'     =>  'Margem de Lucro',
                        'required'  =>  true,
                        'small'     =>  'Margem de Lucro do Produto. Formato 0.50 para 50%'
                    ), $this->metadata);

    // val embalagem
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'valEmbalagem',
                        'type'      =>  'text',
                        'label'     =>  'Valor da Embalagem',
                        'required'  =>  true,
                        'small'     =>  'Quanto custa a embalagem usada para enviar este produto. Formato 0.1'
                    ), $this->metadata);

    // uni embalagem
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'uniEmbalagem',
                        'type'      =>  'text',
                        'label'     =>  'Quantidade por Embalagem',
                        'required'  =>  true,
                        'small'     =>  'Quantos itens deste produto cabe na embalagem especificada'
                    ), $this->metadata);

    // codigo fornecedor
    echo $this->PlulzForm->addRow(array(
                        'name'      =>  'codigoFornecedor',
                        'type'      =>  'text',
                        'label'     =>  'Código Fornecedor',
                        'required'  =>  true,
                        'small'     =>  'O código deste produto no fornecedor'
                    ), $this->metadata);

?>