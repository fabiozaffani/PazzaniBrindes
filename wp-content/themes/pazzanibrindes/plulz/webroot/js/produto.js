jQuery(document).ready(function(){

    //Globals Namespaces
    var produto;

    produto = {

        basename :   PazzaniBrindesProduto.basename,

        extra   :   {
            metabox :   '#produto_extra_metabox',
            fields  :   PazzaniBrindesProduto.extraFields.replace(/&quot;/g, '"')
        }
    }

    jQuery('#add_extra').click(function(){

        var extraFields;
        var CurrentRow;
        var novaLinha;

        extraFields = jQuery.parseJSON(produto.extra.fields);

        CurrentRow = jQuery(produto.extra.metabox +' .inside p').length / 2;

        novaLinha = '';
        
        jQuery.each(extraFields, function(k,v)
        {
            novaLinha += '<p >';
            novaLinha += '<label for="">' + v.capitalize() + '</label>'
            novaLinha += '<input class="text" type="text" name="'+ produto.basename +'[extraList]['+CurrentRow+']['+v+']" value=""/></td>';
            novaLinha += '</p>';
        });

        jQuery(this).parent().before(novaLinha);
        
        return false;
    });

    jQuery('#remove_extra').click(function(){

        var paragraphs =  jQuery(produto.extra.metabox + ' .inside p');

        if (paragraphs.length > 2)
        {
            jQuery(produto.extra.metabox + ' .inside p input.text').parent().last().remove();
            jQuery(produto.extra.metabox + ' .inside p input.text').parent().last().remove();
        }
        return false;
    });

});