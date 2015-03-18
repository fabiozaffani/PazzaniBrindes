<ul id='comentarios'>
<?php
        if (!empty($this->commentList)) :
            foreach( $this->commentList as $comentario ) :
?>
                <li>
                    <p><?php echo $comentario['conteudo']; ?></p>
                    <p class='meta'>
                        <?= $comentario['data']; ?> por <?= $comentario['autor'];?>
                        <a href='#remover_comment' id='<?= $comentario['ID']; ?>' class='remove_comment'>Remover</a>
                    </p>
                </li>

<?php          
            endforeach;
        endif;
?>
</ul>
<?php

        echo $this->PlulzForm->addRow( array(
                            'name'      =>  'comentario',
                            'type'      =>  'textarea',
                            'class'     =>  'comentario',
                            'label'     =>  __('Comentários', $this->_name),
                            'required'  =>  true
                        ) );

        echo $this->PlulzForm->addInput('submit', 'addComment', 'addComment', 'Adicionar Comentário', array('class' => 'button-primary') );

?>