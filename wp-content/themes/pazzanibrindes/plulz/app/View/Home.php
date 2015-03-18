<div id="container">
    <div id="intro" class="post round">
        <img src="<?php echo $this->_assets; ?>/img/ico/seta.png" id="setaSuperior" class="setas" />
        <img src="<?php echo $this->_assets; ?>/img/ico/seta.png" id="setaSidebar" class="setas" />
        <img src="<?php echo $this->_assets; ?>/img/ico/seta.png" id="setaOrcamento" class="setas" />
        <h1>Bem-Vindo a Pazzani Brindes Personalizados</h1>
        <p>Aqui você encontrará <strong>mais de 200 tipos de Brindes</strong> para sua empresa, todos os nossos brindes podem ser personalizados com seu logo e marca.</p>
        <p><strong>Para encontrar o que você procura é muito fácil:</strong></p>
        <p class="check">Se deseja navegar em alguma categoria específica utilize o <a href="#" id="homeSetaSuperior" rel="setaSuperior"><strong>menu superior</strong></a> ou a <a href="#" id="homeSetaLateral" rel="setaSidebar"><strong>barra lateral</strong></a>.</p>
        <p class="check">A forma mais rápida de fazer um orçamento é <strong>enviar orçamentos direto pelo site</strong>, basta adicionar os produtos desejados a <a href="#" id="homeSetaOrcamento" rel="setaOrcamento"><strong>lista de orçamento</strong></a>.</p>
        <p class="check">Você pode também realizar um orçamento conosco através da central de atendimento pelo <strong>telefone (11) 3963-4549</strong> ou pela nossa <a href="http://www.pazzanibrindes.com.br/fale-conosco"><strong>página de contato</strong></a>.</p>
    </div>
<?php
        if( have_posts() ) :
            $i = 0;
            while( have_posts() ) :

                the_post();

                ( $i % 3 == 1) ? $zebra = 'middle' : $zebra = 'side';
?>
                <div class="round post-list <?php echo $zebra;?>">

                    <div class="post-list-img">

                        <a href="<?php the_permalink();?>" title="<?php the_title();?>">
                            <?php the_post_thumbnail('list-thumb'); ?>
                        </a>

                    </div>

                    <div class="post-list-entry">

                        <h3><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title(); ?></a></h3>
                        <?php the_excerpt(); ?>
                        <a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
                            <?php _e('Veja Mais', $this->_name ); ?>
                        </a>

                    </div>

                </div>
<?php
                $i++;
            endwhile;
        endif;

        $this->navigation();
?>

</div>