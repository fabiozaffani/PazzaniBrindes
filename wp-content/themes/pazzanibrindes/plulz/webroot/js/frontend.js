$(document).ready(function(){

    var contato = {}, EnviarSignup = {}, EnviarLogin = {}, EnviarMensagem = {}, carrinho = {}, AdicionarCarrinho = {}, AtualizarItemCarrinho = {}, RemoverItemCarrinho = {}, signup = {};

    signup = {

        // here we declare the parameters to send along with the request
        // the following action hooks will be fired wp_ajax_nopriv_ and wp_ajax_
        action          : SignUp.action,

        // Link
        ajaxurl         : SignUp.ajaxurl,

        // send the nonce along with the request
        ajaxNonce       : SignUp.ajaxNonce,


        home            : SignUp.home
    }


    contato = {

        // here we declare the parameters to send along with the request
        // the following action hooks will be fired wp_ajax_nopriv_ and wp_ajax_
        action          : Contato.action,

        // Link
        ajaxurl         : Contato.ajaxurl,

        // send the nonce along with the request
        ajaxNonce       : Contato.ajaxNonce,


        home            :   Contato.home

    }


    carrinho = {

            // here we declare the parameters to send along with the request
            // the following action hooks will be fired wp_ajax_nopriv_ and wp_ajax_
            action :         Carrinho.action,

            // Link
            ajaxurl         : Carrinho.ajaxurl,

            // send the nonce along with the request
            ajaxNonce       : Carrinho.ajaxNonce,

            fechamento      :   Carrinho.fechamento,

            home            :   Carrinho.home

    }

    /* Atualizar todos os locais onde ha conteudo do carrinho na pagina atual */
    var UpdateCart = (function ($, carrinho)
    {
        var methods = {}, CartInfo = {};

        CartInfo = {
            action              :   carrinho.action,
            ajaxNonce           :   carrinho.ajaxNonce,
            todo                :   'status'
        };

        var Sidebar = function(response)
        {
            var CarrinhoSidebarBody, CarrinhoSidebarTable;

            $('#CarrinhoSidebar').animate({backgroundColor: "#900" }, 'fast');

            CarrinhoSidebarTable = jQuery('table#CarrinhoSidebar_tabela');

            CarrinhoSiderBody = '<tbody>';

            if (!response)
            {
                CarrinhoSiderBody += '<tr><td colspan="2"></td></tr>';

                $('#CarrinhoSidebar h3').text('Orçamento Vazio');
                CarrinhoSidebarTable.hide();

                $('#CarrinhoSidebar_fechar_wrapper').hide();
            }
            else
            {
                $.each(response, function (k, v){
                    CarrinhoSiderBody += '<tr>\
                        <td class="CarrinhoSidebar_produto">' +	k  + '</td>\
                        <td class="CarrinhoSidebar_quantidade">' +	v + '</td>\
                        </tr>';
                });

                $('#CarrinhoSidebar h3.carrinhoSidebarTitle').text('Meu Orçamento');
                CarrinhoSidebarTable.show();

                $('#CarrinhoSidebar_fechar_wrapper').show();
            }

            CarrinhoSiderBody += '</tbody>';

            $('#CarrinhoSidebar #CarrinhoSidebar_tabela tbody').remove();
            $('#CarrinhoSidebar #CarrinhoSidebar_tabela').append(CarrinhoSiderBody);
            $('#CarrinhoSidebar').animate({backgroundColor: "#fff" }, 'fast');
        };

        var Resumo = function(response)
        {
            var CartResumo;

            $('#GoogleTracking').siblings().remove();

            CartResumo = '<h3>Sua lista de orçamento está vazia</h3> <br /><a href="' + carrinho.home + '">&laquo; Continuar orçando</a>';

            $('.post').append(CartResumo);

        };

        var Produto = function(response)
        {

            var NovoFormulario;

            NovoFormulario =    '<p>Item já adicionado ao orçamento, deseja alterar a <strong><a href="/?page_id=' + carrinho.fechamento + '">quantidade</a></strong>?</p>\
                                <a id="FecharCarrinho" href="/?page_id=' + carrinho.fechamento + '" class="button">Editar/Fechar Orçamento »</a>\
                                <a id="ContinuarOrcamento" href="' + carrinho.home + '">Continuar Orçamento")</a>'

            $('#ShoppingCart').html(NovoFormulario);

        };

        var Ajax = function()
        {
            $.ajax({
                url: carrinho.ajaxurl,
                type: 'POST',
                data: CartInfo,
                timeout: 6000,
                error:  function (xhr, ajaxOptions, thrownError){

                    alert(xhr.status);
                    alert(thrownError);
                    alert(xhr.statusText);

                },
                success: function( response ){

                    PaginaProduto   =   $('#ShoppingCart').length;
                    PaginaResumo    =   $('#CarrinhoDetalhes').length;
                    SidebarExists   =   $('#CarrinhoSidebar').length;

                    if (SidebarExists)
                        Sidebar(response);

                    if (PaginaResumo && !response)
                        Resumo();

                    if (PaginaProduto)
                        Produto(response);
                }
            });
        }

        return {
            Ajax : Ajax
        }

    }(jQuery, carrinho));


    /*  Aceitar somente numeros nos campos de quantidade,
     *  caso existam
     */

    var inputQuantidadeExists, FaleConoscoTelefoneExists, TelefoneExists, TelefoneSignup;

  	inputQuantidadeExists       =   $('input.quantidade').length;
    FaleConoscoTelefoneExists   =   $( '#pazzanibrindes_Telefone' ).length;
    TelefoneExists              =   $( '#pazzanibrindes_cliente_telefone' ).length;
    TelefoneSignup              =   $( '#pazzanibrindes_TelefoneSignup' ).length;


	if(inputQuantidadeExists)
		$('input.quantidade').bind('keydown', Tools.NumbersOnly);

    if(FaleConoscoTelefoneExists)
        $( '#pazzanibrindes_Telefone' ).mask( '(99)9999-9999?9' );

    if(TelefoneExists)
        $( '#pazzanibrindes_cliente_telefone' ).mask( '(99)9999-9999?9' );

    if(TelefoneSignup)
        $( '#pazzanibrindes_TelefoneSignup' ).mask( '(99)9999-9999?9' );

   /*
    * Adicionar Itens ao Carrinho
    ************************************************************************/

    $('#container').on('click', '#AdicionarCarrinho', function(){
        $('#FormularioCarrinho').submit();
        return false;
    });

	$('#ShoppingCart').on('submit', '#FormularioCarrinho', function(){

        var isErrorMin, minimo, quantidade;

		isErrorMin = $('#errormin').length;

		if (isErrorMin)
			$('#errormin').remove();

		minimo = parseFloat( $('[name=minimo]').val() );
		quantidade = parseFloat( $('#quantidade').val() );

		if (quantidade < minimo)
		{
			$('#quantidade').after('<span id="errormin" style="width:100%;display:block;color:red;font-size:12px">Mínimo é maior ou igual a '+minimo+ '</span>');
		}
		else
		{
            $.each($('#FormularioCarrinho').serializeArray(), function(i, field) {
                AdicionarCarrinho[field.name] = field.value;
            });

            // The action to be performed on the cart
            AdicionarCarrinho['todo']               =   'adicionar';
            AdicionarCarrinho['action']             =   carrinho.action;
            AdicionarCarrinho['ajaxNonce']          =   carrinho.ajaxNonce;

			$('#FormularioCarrinho').fadeOut('fast', function(){

				$('.AjaxLoading').fadeIn('fast', function(){

                    $.ajax({
                        url: carrinho.ajaxurl,
                        type: 'POST',
                        data: AdicionarCarrinho,
                        timeout: 6000,
                        error:  function (xhr, ajaxOptions, thrownError){

                            alert(xhr.status);
                            alert(thrownError);
                            alert(xhr.statusText);

                        },
                        success: function( response ){

                            // Bye loading graph
                            $('.AjaxLoading').fadeOut('fast', function(){

                                if (!response)
                                    $('#FormularioCarrinho').fadeIn();
                                else
                                    $('#FormularioCarrinho').remove();

                                UpdateCart.Ajax();

                            });
                        }
                    });
                });
			});


		}
    return false;
  });


  /*
   * Atualizando itens do carrinho
   ********************************************************************************/

    // Limpar os erros quando o campo input for selecionado
	$('input.quantidade').focus(function(){

        var hasError = $(this).siblings('.errormin').length;

		if(hasError)
			$(this).siblings('.errormin').remove();

	});

	$('.CarrinhoDetalhes_atualizar').click(function(){

        var id, inputQuantidade, minimo, quantidade;


		id = parseFloat($(this).attr('rel'));

        inputQuantidade = $('tr#'+id+' .CarrinhoDetalhes_quantidade input' );

        minimo = parseFloat($(this).siblings('input.minimo').val());

        quantidade = parseFloat(inputQuantidade.val());

        if ( quantidade < minimo)
        {
            $(inputQuantidade).after('<span class="errormin round">Min. '+minimo+' itens</span>');
            $('.errormin').fadeTo('fast', 0.6);
        }

        AtualizarItemCarrinho = {

            action              :   carrinho.action,

            ajaxNonce           :   carrinho.ajaxNonce,

            todo                :   'atualizar',

            quantidade          :   quantidade,

            id                  :   id
        }

		$.ajax({
			url: carrinho.ajaxurl,
			type: 'POST',
            data: AtualizarItemCarrinho,
			timeout: 6000,
			error:  function (xhr, ajaxOptions, thrownError){

                    alert(xhr.status);
                    alert(thrownError);
                    alert(xhr.statusText);

            },
			success: function( response ){

                // O valor do input deve ser atualizado com o que retorna do back end
                // assim caso o usuário tenha, de alguma forma, tentado e conseguido inputar um
                // valor que seja abaixo do minimo isso será corrigido no retorno do ajax
                // quer dizer que fica impossível de se burlar este sistema
                // Mas uma verificação prévia deve ser executado no front end

                if (response)
                {
                    UpdateCart.Ajax();
                };
			}
		});

		return false;

	});

  /*
   * Removendo itens do carrinho com AJAX
   ********************************************************************************/

	$('.CarrinhoDetalhes_remover').click(function(){

        var id;

        id = $(this).attr('rel');

        RemoverItemCarrinho = {

            action              :   carrinho.action,

            ajaxNonce           :   carrinho.ajaxNonce,

            todo                :   'remover',

            id                  :   id
        }

		$.ajax({
			url: carrinho.ajaxurl,
			type: 'POST',
            data: RemoverItemCarrinho,
			timeout: 6000,
			error:  function (xhr, ajaxOptions, thrownError){

                    alert(xhr.status);
                    alert(thrownError);
                    alert(xhr.statusText);

            },
			success: function( response ){

                if (response)
                {
                    $('tr#'+id).fadeOut('fast');

                    UpdateCart.Ajax();
                }
			}
		});

		return false;
	});

    /*
     * Enviado Formulário de Contato via Ajax na Página do Produto
     ********************************************************************************/

    var ProdutoContatoForm = $('#produto-contato form');

    ProdutoContatoForm.submit(function(){

        EnviarMensagem = {

            action              :   contato.action,

            ajaxNonce           :   contato.ajaxNonce,

            todo                :   'enviarMensagem',

            nome                :   $(this).find('#pazzanibrindes_Nome').val(),

            email               :   $(this).find('#pazzanibrindes_Email').val(),

            telefone            :   $(this).find('#pazzanibrindes_Telefone').val(),

            mensagem            :   $(this).find('#pazzanibrindes_Mensagem').val(),

            codigo              :   $(this).find('#pazzanibrindes_Codigo').val()
        }


        var formInterna = $('div#form-interna');
        var backup = formInterna.children();

        formInterna.empty().append('<p style="text-align: center;width: 693px;">Aguarde...</p>');

        formInterna.fadeTo(1000, 0.6, function(){

            $.ajax({
                url: contato.ajaxurl,
                type: 'POST',
                data: EnviarMensagem,
                timeout: 7000,
                error:  function (xhr, ajaxOptions, thrownError){

                    console.log(xhr.status);
                    console.log(thrownError);
                    console.log(xhr.statusText);

                },
                success: function( response ){

                    console.log(response);

                    if (response)
                    {
                        formInterna.empty().append('<p style="text-align: center;width: 693px;">Mensagem enviada com Sucesso! Entraremos em contato em breve!</p>');
                    }
                    else
                    {
                        formInterna.empty().append('<ul class="erros" style="width:660px;"><li style="width:660px;">Por Favor, Preencha todos os Campos Obrigatórios.</li></ul>').append(backup);
                    }

                    formInterna.fadeTo(500, 1);

                }
            });

        });

        return false;
    });

    /**
     * Registrando Usuário via Ajax pelo Painel de Ajuda
     */

    /*
     * Enviado Formulário de Contato via Ajax na Página do Produto
     ********************************************************************************/

    var SignupButton = $('#menu-item-25983 a');
    var Signup = $('#signup form');

    SignupButton.leanModal({closeButton: ".modal_close"});

    if(!logged)
    {
        setTimeout(function(){
            if(Signup.is(':hidden'))
            {
                SignupButton.click();
            }
        }, 5000);

    }

    Signup.submit(function(){

        Signup.parent().find('ul.erros').remove();

        EnviarSignup = {

            action              :   signup.action,

            ajaxNonce           :   signup.ajaxNonce,

            todo                :   'signup',

            nome                :   $(this).find('#pazzanibrindes_NomeSignup').val(),

            email               :   $(this).find('#pazzanibrindes_EmailSignup').val(),

            telefone            :   $(this).find('#pazzanibrindes_TelefoneSignup').val()
        }

        $(this).fadeOut('fast', function(){
            Signup.parent().append('<p id="warn-signup">Por favor, aguarde enquanto processamos sua requisição...</p>');
        })

        $.ajax({
            url: signup.ajaxurl,
            type: 'POST',
            data: EnviarSignup,
            timeout: 7000,
            error:  function (xhr, ajaxOptions, thrownError){

                console.log(xhr.status);
                console.log(thrownError);
                console.log(xhr.statusText);

            },
            success: function( response ){

                if (response)
                {
                    EnviarLogin = {

                        action              :   signup.action,

                        ajaxNonce           :   signup.ajaxNonce,

                        todo                :   'ajaxlogin',

                        email               :   EnviarSignup.email
                    }


                    $.ajax({
                        url: signup.ajaxurl,
                        type: 'POST',
                        data: EnviarLogin,
                        timeout: 7000,
                        error:  function (xhr, ajaxOptions, thrownError){

                            console.log(xhr.status);
                            console.log(thrownError);
                            console.log(xhr.statusText);

                        },
                        success: function( newresponse )
                        {
                            if (newresponse)
                            {
                                console.log('logado');
                            }
                            else
                            {
                                console.log('nao logado');
                            }
                        }
                    });
                }
                else
                {
                    $('#warn-signup').remove();
                    Signup.before('<ul class="erros"><li>Ocorreu um erro, preencha os dados abaixo corretamente</li></ul>');
                    Signup.fadeIn('fast');
                }

            }
        });

        return false;
    });


    /** Efeitos visuais da Home **/

    var Arrows = {
        Superior : {
            Executed : false,
            Showed   : false
        },
        Lateral : {
            Executed : false,
            Showed   : false
        },
        Orcamento : {
            Executed : false,
            Showed   : false
        }
    };

    $('#homeSetaSuperior, #homeSetaLateral, #homeSetaOrcamento').click(function(){ return false });

    var Timer = 1000;

    $('#homeSetaSuperior').hover(
        function(){
            if (!Arrows.Superior.Showed)
            {
                Arrows.Superior.Executed = true;
                Arrows.Superior.Showed = true;
                $('#setaSuperior').fadeIn('fast').animate({
                    top : '+=20'
                }, Timer, function(){
                    // animated over
                });
            }
        }, function(){
            if (Arrows.Superior.Executed)
            {
                $('#setaSuperior').fadeOut('fast').animate({
                    top : '-=20'
                }, Timer, function(){
                    Arrows.Superior.Executed = false;
                });
            }
        }
    );

    $('#homeSetaLateral').hover(
        function(){
            if (!Arrows.Lateral.Showed)
            {
                Arrows.Lateral.Showed = true;
                Arrows.Lateral.Executed = true;
                $('#setaSidebar').fadeIn('fast').animate({
                    top : '+=20'
                }, Timer, function(){
                    // animated over
                });
            }
        }, function(){
            if (Arrows.Lateral.Executed)
            {
                $('#setaSidebar').fadeOut('fast').animate({
                    top : '-=20'
                }, Timer, function(){
                    Arrows.Lateral.Executed = false;
                });
            }
        }
    );

    $('#homeSetaOrcamento').hover(
        function(){
            if (!Arrows.Orcamento.Showed)
            {
                Arrows.Orcamento.Showed = true;
                Arrows.Orcamento.Executed = true;
                $('#setaOrcamento').fadeIn('fast').animate({
                    top : '+=20'
                }, Timer, function(){
                    // animated over
                });
            }
        }, function(){
            if (Arrows.Orcamento.Executed)
            {
                $('#setaOrcamento').fadeOut('fast').animate({
                    top : '-=20'
                }, Timer, function(){
                    Arrows.Orcamento.Executed = false;
                });
            }
        }
    );


}); // Fim do function.ready

