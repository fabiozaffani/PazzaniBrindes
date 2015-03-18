jQuery(document).ready(function() {

    //Globals Namespaces
    var orcamento = {}, usuario = {}, produto = {}, comentarios = {};

    // For Updating
    var AtualizarProduto;
    var InformacoesUsuario;
    var AdicionarComentario;
    var RemoverComentario;
    var AutoCompleteUserEmail;

    // Implementations

    orcamento = {

        // the following action hooks will be fired:
        // wp_ajax_nopriv_ and wp_ajax_

        action  :   PazzaniBrindesOrcamento.actionOrcamento,


        // nonce to be send in the ajax requests, for safety

        orcamentoAjaxNonce : PazzaniBrindesOrcamento.orcamentoAjaxNonce,


        // URL para submeter o ajax request

        ajaxurl : PazzaniBrindesOrcamento.ajaxurl,


        // ID do orcamento atual

        ID : PazzaniBrindesOrcamento.postID,


        // Campos monetarios

        moneyFields : PazzaniBrindesOrcamento.moneyFields.replace(/&quot;/g, '"'),


        // Campos read only

        readOnly    : PazzaniBrindesOrcamento.readOnly.replace(/&quot;/g, '"'),


        // Campos para cada linha do produto

        camposProduto : PazzaniBrindesOrcamento.camposProduto.replace(/&quot;/g, '"'),


        // List with all the product codes

        codeList : PazzaniBrindesOrcamento.codeList.replace(/&quot;/g, '"'),


        // The basename for all name of all input fields

        basename : PazzaniBrindesOrcamento.basename

    };

    usuario = {

        metabox :   '#orcamento_cliente_metabox',

        email : {
            'id' : '#pazzanibrindes_orcamento_cliente_email',
            'name' : orcamento.basename + '[cliente][email]'
        },

        nome : {
            id : '#pazzanibrindes_orcamento_cliente_nome',
            name : orcamento.basename + '[cliente][nome]'
        },

        telefone : {
            id : '#pazzanibrindes_orcamento_cliente_telefone',
            name : orcamento.basename + '[cliente][telefone]'
        }
    };

    produto = {


        // The metabox of the products in the orcamento page

        metabox         :   '#orcamento_produtos_metabox',


        // List of additional options for each product, defaults to a empty array()

        extraList       :   PazzaniBrindesOrcamento.extraList.replace(/&quot;/g, '"'),


        // Default name para os input fields dos produtos

        basename        :   orcamento.basename + '[produto]',


        // Gravacoes disponíveis para o produto escolhido, defaults to a empty array()

        gravacoes_list  :    PazzaniBrindesOrcamento.gravacoes_list.replace(/&quot;/g, '"')

    };

    comentarios = {

        metabox             :   '#orcamento_comentarios_metabox',

        content             :   {
            id              :   '#pazzanibrindes_orcamento_comentario'
        },
        box                 :   {
            id            :   '#addComment',
            name          :   orcamento.basename + '[addComment]'
        }

    };

    function newExtrasList(row, extraList) {

        var newExtras;
        var extra;
        var nome;

        newExtras = '';

        if (Tools.isDefined(extraList))
        {
            if (!Tools.isEmpty(extraList))
            {
                jQuery.each(extraList, function(i, v) {

                    nome = v.nome;

                    newExtras +='<p class="checkbox prazo">' +
                                    '<label for="pazzanibrindes_orcamento_produto_'+row+'_extras_'+nome+'">'+ nome.capitalize() +'</label>' +
                                    '<input name="pazzanibrindes_orcamento[produto]['+row+'][extras]['+nome+']" ' +
                                            'class="checkbox prazo" ' +
                                            'type="checkbox" value="1" ' +
                                            'id="pazzanibrindes_orcamento_produto_'+row+'_extras_'+nome+'">' +
                                 '</p>';
                });
            }
        }

        return newExtras;

    }

    function appendDialog(toCall, row) {

        var dialog;
        var evento;

        dialog = jQuery(toCall);

        dialog.dialog({
            autoOpen: false,
            show: "blind",
            hide: "explode",
            minWidth: 400,
            minHeight: 100,
            modal: false,
            buttons: {
                    Ok: function() {
                        jQuery( this ).dialog( "close" );
                    }
            },
            close: function(event, ui){

                // Nao e necessario resetar os valores para atualizar o resto
                evento = {
                    data : {
                        values : 0
                    },
                    target : event.target
                };

                UpdateProductAjax(evento)
            }
        });

        jQuery(produto.metabox + ' .inside table tbody tr').eq(row).find('td.extras').append(dialog.parent());

        dialog.dialog('open');

    }

    function priceFormat(jQuerySelection) {

        jQuerySelection.priceFormat({
            prefix: 'R$ ',
            centsSeparator: '.',
            thousandsSeparator: ',',
            limit: 7,
            centsLimit: 2
        });

    }

    function totalProduto(quantidade, unitario) {

        var TamanhoString;
        var TempNumber;
        var CustoTotalFinal;
        var StringCustoFinal;
        var CustoTotalLength;

        // Preparando o número para transformá-lo em int

        TamanhoString = unitario.length;

        unitario = unitario.substr(3, TamanhoString);

        TempNumber = unitario.split(',');

        unitario = parseFloat(TempNumber.join('.'));


        // Calculando o Custo Total Final

        CustoTotalFinal = quantidade * unitario;
        CustoTotalFinal = CustoTotalFinal.toFixed(2); // um float com o valor total

        CustoTotalFinal = CustoTotalFinal.split('.');

        // Custo Total Final (com o valor nominal da moeda corrente

        StringCustoFinal = 'R$ ' + CustoTotalFinal.join(',');

        CustoTotalFinal = CustoTotalFinal.join('');
        CustoTotalLength = CustoTotalFinal.length;

        //voltando a uma string

        if (CustoTotalLength >= 6) {
            var NewNumber = '';
            var K = 6;
            var c = true;

            for (i = 0; i < CustoTotalLength; i++) {
                var ThousandsPos = CustoTotalLength - K;

                if (i == CustoTotalLength - 3)
                    NewNumber += CustoTotalFinal[i] + ',';
                else if (i == ThousandsPos)
                    NewNumber += CustoTotalFinal[i] + '.';
                else
                    NewNumber += CustoTotalFinal[i];

                if (i >= 6 && K + 2 - i === 0)
                    K += 3;

            }
            StringCustoFinal = 'R$ ' + NewNumber;
        }

        return StringCustoFinal;
    }

    function UpdateProductAjax(event)
    {

        var Row = jQuery(event.target).parents('tr.produto');
        var RowNumber = Row.index();

        var tr = jQuery('tr#row_'+RowNumber);
        var id = tr.find('td.codigo .codigo').val();
        var comissao = jQuery('input#comissao').val();

        if (!event.data.values)
        {
            var gravacoes       = tr.find('td.gravacoes .gravacoes').val();
            var quantidade      = tr.find('td.quantidade .quantidade').val();
            var venda_unitario  = tr.find('td.venda_unitario .venda_unitario').val();
            var descricao       = tr.find('td.descricao .descricao').val();
            var extrasList      = tr.find('td.extras .checkbox');

            // Catpure the extra fields
            // Importante o ^ antes do = por o name nao ser inteiro e este localiza portanto apenas o começo
            var extras = {};

            if (Tools.isDefined(extrasList))
            {
                if (!Tools.isEmpty(extrasList))
                {
                    var ExtraNome;

                    jQuery.each(extrasList, function(k, v) {

                        // Caso a checkbox esteja marcada
                        if ( jQuery(v).is(':checked') )
                        {
                            ExtraNome = jQuery(v).attr('name');
                            ExtraNome = ExtraNome.split(']');

                            // Retirar o ' [ '
                            ExtraNome = ExtraNome[3].substr(1);

                            extras[ExtraNome] = '1';
                        }
                    });
                }
            }
        }

        // Sem codigo, não há o que ser atualizado
        if (Tools.isEmpty(id))
            return false;

        // Transformando em int

        if (Tools.isEmpty(comissao))
            comissao = 0;
        else
            comissao = parseFloat(comissao);


        // Change opacity so user knows something is hapenning

        jQuery(produto.metabox + ' .inside table').fadeTo('slow', 0.3);

        AtualizarProduto = {

            // Choose if we should update all values with sended ones or refresh from database
            resetValues     : parseFloat(event.data.values),

            orcamentoAjaxNonce : orcamento.orcamentoAjaxNonce,

            action          :   orcamento.action,

            todo            :   'atualizar_produto',

            quantidade      :   quantidade,

            id              :   id,

            gravacoes       :   gravacoes,

            venda_unitario  :   venda_unitario,

            descricao       :   descricao,

            comissao        :   comissao,

            extras          :   extras

        };

        jQuery.ajax({
            type: 'POST',
            url: orcamento.ajaxurl,
            data: AtualizarProduto,
            success: function(jsonCurrentProductInfo) {

                var Opcoes, Current;

                var InputList = new Array(
                    'gravacoes', 'quantidade', 'descricao', 'desconto', 'custo_unitario', 'venda_unitario', 'total', 'prazo', 'extras'
                );

                // Gravacoes List

                Current = tr.find('td.gravacoes .gravacoes');
                Current.children().remove();

                Opcoes = '<option value=""></option>';

                if (Tools.isDefined(jsonCurrentProductInfo.gravacoes_list) && !Tools.isEmpty(jsonCurrentProductInfo.gravacoes_list))
                {
                    jQuery.each(jsonCurrentProductInfo.gravacoes_list, function(k, v) {
                        Opcoes += '<option value="' + k + '">' + v + '</option>';
                    });
                }

                Current.append(Opcoes);

                // Extralist

                Current = jQuery('div.extra_row_' + RowNumber);

                Current.children().remove();

                Opcoes = newExtrasList(RowNumber, jsonCurrentProductInfo.extraList);

                Current.append(Opcoes);

                // Filling values

                jQuery.each(jsonCurrentProductInfo, function(key, value) {

                    if (Tools.inArray(key, InputList))
                    {
                        if ( key == 'extras' )
                        {
                            Current = tr.find('td.extras input');

                            Current.attr("checked", false);

                            jQuery.each(value, function(k, v){
                                Current.filter(':checkbox[name$="['+k+']"]').attr("checked", true);
                            });
                        }
                        else
                        {
                            Current = tr.find('td.'+key+' .'+key);
                            Current.val(value);
                        }

                    }

                });

            },
            error:  function (xhr, ajaxOptions, thrownError) {

                alert(xhr.status);
                alert(thrownError);
                alert(xhr.statusText);

            },
            complete : function() {
                jQuery(produto.metabox + ' .inside table').fadeTo('slow', 1.0);
            }
        });

        return false;
    }

    function UpdateUserAjax() {
        var email = jQuery(usuario.email.id).val();

        var nome = jQuery(usuario.nome.id).val();

        var telefone = jQuery(usuario.telefone.id).val();

        // Lets quit if there name and phone are already typed int
        if (!Tools.isEmpty(nome) && !Tools.isEmpty(email))
            return;

        // Only runs if the email is not empty
        if (email != '') {

            InformacoesUsuario = {

                orcamentoAjaxNonce  :   orcamento.orcamentoAjaxNonce,

                action              :   orcamento.action,

                todo                :   'atualizar_usuario',

                email               :   email

            };

            // Change opacity so user knows something is hapenning
            jQuery(usuario.metabox).fadeTo('slow', 0.3);

            jQuery.ajax({
                type: 'POST',
                url: orcamento.ajaxurl,
                data: InformacoesUsuario,
                success: function(jsonClientInfo) {

                    jQuery(usuario.nome.id).val(jsonClientInfo['nome']);
                    jQuery(usuario.telefone.id).val(jsonClientInfo['telefone']);

                },
                error:  function (xhr, ajaxOptions, thrownError) {

                    alert(xhr.status);
                    alert(thrownError);
                    alert(xhr.statusText);

                },
                complete : function() {
                    // Change opacity so user knows something is hapenning
                    jQuery(usuario.metabox).fadeTo('fast', 1);
                    jQuery(usuario.nome.id).blur();
                }
            });

        }

    }

    // Hack para deixar o post content abaixo das infos do orçamento

    jQuery('#advanced-sortables').remove().insertBefore('#postdivrich');


    // Telephone Mask

    jQuery(usuario.telefone.id).mask('(99)9999-9999?9');


    // Price format on the needed fields

    priceFormat(jQuery('.monetaria'));
    


    /***
     * Atualizar as informações do produto, dependendo de qual campo se está modificando
     *******************************************************************************************/

    var OrcamentoProdutoMetabox = jQuery('#orcamento_produtos_metabox');
    OrcamentoProdutoMetabox.on('keydown', '#comissao', Tools.NumbersOnly);
    OrcamentoProdutoMetabox.on('keydown', 'input.quantidade', Tools.NumbersOnly);
    OrcamentoProdutoMetabox.on('change', 'select.codigo', {values : 1}, UpdateProductAjax);
    OrcamentoProdutoMetabox.on('change', 'select.gravacoes', {values : 0}, UpdateProductAjax);
    OrcamentoProdutoMetabox.on('blur', 'input.quantidade', {values : 0}, UpdateProductAjax);

    /**
     * Automatically change username on content if it is still %%NOMECLIENTE%%
     */
    jQuery(usuario.nome.id).blur(function(){

        var nome;
        var span;
        var novoNome;

        nome = jQuery(this).val();

        if (Tools.isEmpty(nome))
            nome = '%%NOMECLIENTE%%';

        span = jQuery('#content_ifr').contents().find('span#nome-do-cliente');

        novoNome = nome.split(" ");

        span.text(novoNome[0]);
    });

    /**
     * Adicionar Novos Campos de Produto
     */

    jQuery('#AdicionarNovoProduto').click(function() {

        var CurrentRow;
        var extra;
        var extraList;
        var camposProduto;
        var moneyFields;
        var readOnly;
        var novaLinhaProduto;
        var selectList;
        var Row;
        var NovoPriceFormat;

        Row         =   jQuery(produto.metabox + ' .inside table tbody tr');
        CurrentRow  =   Row.length;

        // Limite máximo de 15 produtos por orçamento

        if (CurrentRow < 15)
        {
            camposProduto   = jQuery.parseJSON(orcamento.camposProduto);
            moneyFields     = jQuery.parseJSON(orcamento.moneyFields);
            readOnly        = jQuery.parseJSON(orcamento.readOnly);

            novaLinhaProduto = '<tr class="produto" id="row_'+CurrentRow+'">';

            jQuery.each(camposProduto, function(k, v) {
                extra = '';
                selectList = '';

                if (Tools.inArray(v, readOnly))
                    extra += 'readonly="readonly"';

                if (Tools.inArray(v, moneyFields))
                    extra += ' class="text ' + v + ' monetaria"';
                else
                    extra += ' class="text ' + v + '"';

                if (v == 'id')
                {
                    selectList = jQuery.parseJSON(orcamento.codeList);
                    extra = 'class="select codigo"';
                }

                if (v == 'gravacoes')
                {
                    selectList = jQuery.parseJSON(produto.gravacoes_list);
                    extra = 'class="select gravacoes"';
                }

                if (v == 'id' || v == 'gravacoes')
                {
                    novaLinhaProduto += '<td '+ extra+ '><select ' + extra + ' name="' + produto.basename + '[' + CurrentRow + '][' + v + ']" ><option value=""></option>';

                    // codeList e criado no arquivo SolicitacaoTPL.php com infos do back

                    if (Tools.isDefined(selectList))
                    {
                        if(!Tools.isEmpty(selectList))
                        {
                            jQuery.each(selectList, function(key, value) {
                                novaLinhaProduto += '<option value="' + key + '">' + value + '</option>';
                            })
                        }
                    }
                    novaLinhaProduto += '</select></td>';
                }
                else if (v == 'extraList')
                {
                    extra = ' class="checklist_extra extra_row_' + CurrentRow + '"';

                    extraList = jQuery.parseJSON(produto.extraList);
                    
                    novaLinhaProduto += '<td class="extras">';

                    novaLinhaProduto += '<a href="#" class="add_extra preview button">+</a>';

                    novaLinhaProduto += '<div' + extra + '>';

                    novaLinhaProduto +=  newExtrasList(CurrentRow, extraList);

                    novaLinhaProduto += '</div>';

                    novaLinhaProduto += '</td>';

                }
                else
                    novaLinhaProduto += '<td '+extra+'><input type="text" name="' + produto.basename + '[' + CurrentRow + '][' + v + ']" ' + extra + ' value=""/></td>';

            });

            novaLinhaProduto += '</tr>';

            NovoPriceFormat = jQuery(produto.metabox + ' div.inside table tbody').append(novaLinhaProduto);

           priceFormat(NovoPriceFormat.find('tr#row_'+CurrentRow+' .monetaria'));
        }
        else
            alert('Permitido máximo de 15 itens por orçamento');

        return false;
    });

    /**
     *  Removendo Campos de Produtos
     */

    jQuery('#RemoverProduto').click(function() {

        var isOnlyOne;

        isOnlyOne = jQuery(produto.metabox + ' .inside table tbody tr').length;

        if (isOnlyOne > 1)
            jQuery(produto.metabox + ' .inside table tbody tr:last').remove();

        return false;
    });

    /********************************************************************************************
     *                              AJAX TIME
     *******************************************************************************************/

    /**
     *  Auto Complete para quando se estiver preenchendo as informações do usuário (captura o email)
     ****************************************************************************************************/

    jQuery(usuario.email.id).autocomplete({

        source: function(request, response) {

            AutoCompleteUserEmail = {

                orcamentoAjaxNonce  :   orcamento.orcamentoAjaxNonce,

                action              :   orcamento.action,

                todo                :   'usuarios_email_ajax',

                email               :   request.term

            }

            jQuery.ajax({
                type: 'POST',
                url: orcamento.ajaxurl,
                data: AutoCompleteUserEmail,
                success: function(data) {

                    var users = jQuery.map(data, function(item) {
                        return {
                            label: item.user_email,
                            value: item.user_email
                        }
                    });

                    response(users);

                },
                error:  function (xhr, ajaxOptions, thrownError) {

                    alert(xhr.status);
                    alert(thrownError);
                    alert(xhr.statusText);

                }

            });
        },

        minLength: 1,

        open: function() {
            jQuery(this).removeClass("ui-corner-all").addClass("ui-corner-top");
        },

        close: function() {
            jQuery(this).removeClass("ui-corner-top").addClass("ui-corner-all");
        },

        focus: function(event, ui) {
            jQuery(this).val(ui.item.value);
        },

        select: function() {
            UpdateUserAjax();
        }

    });

    /**
     * Adicionando Comentário
     *******************************************************************************/

    jQuery(comentarios.box.id).click(function() {

        var content;
        var error;
        
        content = jQuery(comentarios.content.id).val();

        // Run normal ajax if there is no ID yet (for new posts, must be saved before)
        if (!Tools.isEmpty(orcamento.ID))
        {

            AdicionarComentario = {

                orcamentoAjaxNonce  :   orcamento.orcamentoAjaxNonce,

                action              :   orcamento.action,

                todo                :   'adicionar_comentario',

                content             :   content,

                id                  :   orcamento.ID

            }

            jQuery.ajax({
                type: 'POST',
                url: orcamento.ajaxurl,
                data: AdicionarComentario,
                success: function(jsonComments) {

                    var output;
                    var ul_comentarios;
                    var childrens;

                    // Only do something if the comment were sucesfully added
                    if (jsonComments)
                    {

                        output = '';

                        jQuery.each(jsonComments, function(k, v) {

                            output +=   "<li><p>" + v.comment_content + "</p>" +
                                            "<p class='meta'>" + v.comment_date + " por " + v.comment_author + " " +
                                                "<a href='#remover_comment' id='" + v.comment_ID + "' class='remove_comment'>Remover</a>" +
                                            "</p>" +
                                        "</li>";

                        });

                    }

                    ul_comentarios = comentarios.metabox + ' .inside #comentarios';
                    childrens = jQuery(ul_comentarios).children();

                    if (childrens.length)
                        childrens.remove();

                    jQuery(ul_comentarios).append(output).fadeIn('slow');

                },
                error:  function (xhr, ajaxOptions, thrownError) {

                    alert(xhr.status);
                    alert(thrownError);
                    alert(xhr.statusText);

                }
            });
        }
        else
        {
            error = '<div id="error">Necessário primeiro salvar o orçamento como rascunho antes de poder comentar nele</div>';

            jQuery('body').append(error);

            jQuery('#error').dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        jQuery(this).dialog("close");
                    }
                }
            });

        }

        return false;

    });

    /**
     *  Removendo Comentário
     *************************************************************************/

    jQuery('#orcamento_comentarios_metabox').live('click', '.remove_comment', function() {

        var ele;
        var id;

        ele = jQuery(this);
        id = jQuery(this).attr('id');

        RemoverComentario = {

            orcamentoAjaxNonce  :   orcamento.orcamentoAjaxNonce,

            action              :   orcamento.action,

            todo                :   'remover_comentario',

            id                  :   id

        }

        jQuery.ajax({
            type: 'POST',
            url: orcamento.ajaxurl,
            data: RemoverComentario,
            success: function(boolean) {

                ele.parent().parent().fadeOut('slow', function() {
                    jQuery(this).remove();
                });

            },
            error:  function (xhr, ajaxOptions, thrownError) {

                alert(xhr.status);
                alert(thrownError);
                alert(xhr.statusText);

            }
        });

        return false;

    });


    /**
     *  Alteração do valor total quando o campo venda_unitario for modificado
     ***********************************************************************************/

    jQuery('input.monetaria.venda_unitario').live('blur', function() {

        var Row;
        var RowNumber;
        var Quantidade;
        var VendaUnitario;
        var VendaTotal;

        Row         = jQuery(this).parent().parent();
        RowNumber   = jQuery(produto.metabox + ' .inside table tbody tr').index(Row);

        if (jQuery(this).hasClass('monetaria'))
        {
            Quantidade = jQuery(Row).find('input[name="' + produto.basename + '[' + RowNumber + '][quantidade]"]').val();
            VendaUnitario = jQuery(this).attr('value');
        }
        else
        {
            Quantidade = jQuery(this).attr('value');
            VendaUnitario = jQuery(Row).find('input[name="' + produto.basename + '[' + RowNumber + '][venda_unitario]"]').val();
        }

        VendaTotal = totalProduto(Quantidade, VendaUnitario);

        jQuery(Row).find('input[name="' + produto.basename + '[' + RowNumber + '][total]"]').val(VendaTotal);
    });


    /**
     * Dialog e as opções extras referentes ao produto
     */

    jQuery.fx.speeds._default = 500;

    jQuery('a.add_extra').live('click', function() {

        var Row;
        var RowNumber;
        var dialog;
        var dialogClass;

        Row = jQuery(this).parent().parent();
        RowNumber = jQuery(produto.metabox + ' div.inside table tbody tr').index(Row);

        dialogClass = '.extra_row_' + RowNumber;

        appendDialog(dialogClass, RowNumber);

        return false;
    })

});