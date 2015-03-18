<table>
    <thead>
        <tr>
            <th class='id'>Cód.</th>
            <th class='quantidade'>Qtde.</th>
            <th class='descricao'>Descrição</th>
            <th class='gravacoes'>Gravações</th>
            <th class='desconto'>Desconto</th>
            <th class='custo_unitario'>Custo U.</th>
            <th class='venda_unitario'>Venda U.</th>
            <th class='total'>Total</th>
            <th class='prazo'>Prazos</th>
            <th class='extras'>Extras</th>
        </tr>
    </thead>
    <tfoot>
        <td colspan='4'>
            <a href='#' class='preview button' id='AdicionarNovoProduto'>Adicionar Produto</a>
            <a href='#' class='preview button' id='RemoverProduto'>Remover Produto</a>
        </td>
        <td>
            <label for='comissao'>Comissão</label></td>
        <td colspan='2'>
            <?php echo $this->PlulzForm->addInput('text', 'comissao', 'comissao', '', '', $this->metadata); ?>
        </td>
        <td colspan='3'>
            <input type='submit' name='<?php echo $this->_name; ?>[atualizarTudo]' class='button-primary' value='Aplicar Diferencial' />
        </td>
        <td colspan='2'>
        </td>
    </tfoot>
    <tbody>

<?php
        foreach( $this->listaProdutos as $key => $produto ) :

            $extraList  = $produto['extraList'];
?>
            <tr class='produto' id='row_<?= $key; ?>'>

<?php
                   //codigo
                    echo $this->PlulzForm->addRow( array(
                                'name'      =>  array('produto', $key, 'id'),
                                'type'      =>  'select',
                                'class'     =>  'codigo',
                                'options'   =>  $this->codeList,
                                'required'  =>  true
                        ), $this->metadata, true);

                    // Quantidade
                    echo $this->PlulzForm->addRow( array(
                            'name'      => array('produto', $key, 'quantidade'),
                            'type'      =>  'text',
                            'class'     =>  'quantidade',
                            'required'  =>  true,
                            'maxlength' =>  6
                        ), $this->metadata, true);

                    //descricao
                    echo $this->PlulzForm->addRow( array(
                            'name'      =>  array('produto', $key, 'descricao'),
                            'type'      =>  'text',
                            'class'     =>  'descricao',
                            'required'  =>  true,
                            'readonly'  =>  true
                        ), $this->metadata, true);

                    //gravacoes
                    echo $this->PlulzForm->addRow( array(
                            'name'      =>  array('produto', $key, 'gravacoes'),
                            'type'      =>  'select',
                            'class'     =>  'gravacoes',
                            'options'   =>  $produto['gravacoes'],
                            'required'  =>  true
                        ), $this->metadata, true);

                    //desconto
                    echo $this->PlulzForm->addRow( array(
                            'name'      =>  array('produto', $key, 'desconto'),
                            'type'      =>  'text',
                            'class'     =>  'desconto',
                            'required'  =>  true,
                            'readonly'  =>  true
                        ), $this->metadata, true);

                    //custo unitario
                    echo $this->PlulzForm->addRow( array(
                            'name'      =>  array('produto', $key, 'custo_unitario'),
                            'type'      =>  'text',
                            'class'     =>  'custo_unitario monetaria',
                            'required'  =>  true,
                            'readonly'  =>  true,
                            'maxlength' =>  6
                        ), $this->metadata, true);

                    //venda unitario
                    echo $this->PlulzForm->addRow( array(
                            'name'      =>  array('produto', $key, 'venda_unitario'),
                            'type'      =>  'text',
                            'class'     =>  'venda_unitario monetaria',
                            'required'  =>  true,
                            'maxlength' =>  8
                        ), $this->metadata, true);

                    // total
                    echo $this->PlulzForm->addRow( array(
                            'name'      =>  array('produto', $key,'total'),
                            'type'      =>  'text',
                            'class'     =>  'total monetaria',
                            'required'  =>  true,
                            'readonly'  =>  true,
                            'maxlength' =>  12
                        ), $this->metadata, true);

                    if(!$extraList) :

                        //prazo
                        echo $this->PlulzForm->addRow( array(
                                'name'      =>  array('produto', $key,'prazo'),
                                'type'      =>  'text',
                                'class'     =>  'prazo',
                                'required'  =>  true,
                                'last'      =>  true
                            ), $this->metadata, true);
?>

                        <td class='extras'>
                            <a href='#' class='add_extra preview button'>+</a>
                            <div class='checklist_extra extra_row_<?= $key; ?>'>
                            </div>
                        </td>
<?php
                    else :

                        //prazo
                        echo $this->PlulzForm->addRow( array(
                                'name'      =>  array('produto', $key,'prazo'),
                                'type'      =>  'text',
                                'class'     =>  'prazo',
                                'required'  =>  true
                            ), $this->metadata, true);
?>
                        <td class='extras'>
                            <a href='#' class='add_extra preview button'>+</a>
                            <div class='checklist_extra extra_row_<?= $key; ?>'>
<?php
                                $count_extra = 0;

                                $totalExtraList = count($extraList);

                                foreach($extraList as $extra) :

                                    // Nao faz sentido executar se o nome nao tiver nenhum conteudo
                                    if (empty($extra['nome']))
                                        continue;

                                    $label = ucwords($extra['nome']);

                                    $count_extra++;

                                    if ($count_extra == $totalExtraList)
                                        $last = true;
                                    else
                                        $last = false;

                                    echo $this->PlulzForm->addRow( array(
                                            'name'      =>  array('produto', $key, 'extras', $extra['nome']),
                                            'type'      =>  'checkbox',
                                            'class'     =>  'prazo',
                                            'label'     =>  __($label, $this->_name),
                                            'table'     =>  false,
                                            'last'      =>  $last
                                        ), $this->metadata);

                                endforeach;
?>
                            </div>
                        </td>
<?php
                    endif;
?>
                </tr>
<?php
        endforeach;
?>
    </tbody>
</table>