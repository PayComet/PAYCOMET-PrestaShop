/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author     PAYCOMET <info@paycomet.com>
*  @copyright  2019 PAYTPV ON LINE ENTIDAD DE PAGO S.L
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/


$(document).ready(function() {

    $("body").on("click",".exec_directpay",function(event) {
        event.preventDefault();
        $.fancybox.close();
        $(".paytpv_pay").hide();
        $("#clockwait").show();
        if ($("#paytpv_suscripcion").is(':checked')){
            $("#pago_directo").attr("action",$("#paytpv_iframe_aux").val());
        } else {
            $("#pago_directo").attr("action",$("#card").val());
        }
        $("#card option,#paytpv_suscripcion").attr("disabled", true);
        $("#pago_directo").submit();
    });


    $("body").on("change",".paytpv #paytpv_periodicity, .paytpv #paytpv_cycles",function(){
        validateSuscription($(this));
    });

    $("body").on("change","#card",function(event){

        if ($("#payment_mode_paytpv")){
            $("#payment_mode_paytpv").attr("data-payment-link",$(this).val());
        }
    });

});

function paytpv_initialize(){
    $("#div_periodicity,.paytpv_iframe").hide();
    checkCard();
}

function check_suscription(){
    if ($("#paytpv_suscripcion").is(':checked')){
        if ($("#newpage_payment").val()!=2)
            $("#exec_directpay").hide();
        $("#div_periodicity").show();
        suscribeJQ();
        $("#cards_paytpv, #storingStep").hide();
    }else{
        $("#cards_paytpv").show();
        $("#div_periodicity,.paytpv_iframe").hide();
        addCardJQ();
        checkCard();
    }
}

function checkCard(){
    // Pago integrado o dentro del comercio en nueva pagina
    if ($("#newpage_payment").val()==0 || $("#newpage_payment").val()==1){
        // Show Cards only if exists saved cards
        if ($("#card option").length>1){
            $("#saved_cards").show();
        }
        // Si está seleccionada NUEVA TARJETA
        if ($("#card").val()=="0"){
            $("#storingStep,.paytpv_iframe").removeClass("hidden").show();
            $("#exec_directpay").hide();
        }else{
            $("#storingStep,.paytpv_iframe").hide();
            $("#exec_directpay").show();
        }
    // Pago en pagina de PAYTPV Fullscreen
    } else if ($("#newpage_payment").val()==2){
        $("#saved_cards").show();
        if ($("#card option").length==1){
            $("#cards_paytpv").hide();
        }
        // El boton de pagar lo mostramos siempre
        $("#exec_directpay").show();

        // Ocultar select de Tarjetas si solo hay NUEVA TARJETA
        if ($("#card option").length==1){
            $("#form_cards").hide();
            $("#storingStep,.paytpv_iframe").removeClass("hidden").show();
        // Si hay mas tarketas
        }else{
            // Si se ha selecciona Nueva Tarjeta mostramos Recordar Tarjeta
            if ($("#card").prop('selectedIndex')==$("#card").length){
                $("#storingStep,.paytpv_iframe").removeClass("hidden").show();
            }else{
                $("#storingStep,.paytpv_iframe").removeClass("hidden").hide();
            }
        }


    }
}


function validateSuscription(element){
    switch (element.attr("id")){
        case 'paytpv_periodicity':
            $("#paytpv_cycles option").each(function() {
                if ($(this).val()*element.val()>(365*5))
                    $(this).hide();
                else
                    $(this).show();
            });
        break;

        case 'paytpv_cycles':
            $("#paytpv_cycles option").each(function() {
                if ($(this).val() * $("#paytpv_periodicity").val()>(365*5))
                    $(this).hide();
                else
                    $(this).show();
            });
        break;
    }
}


function confirm(msg, modal, callback) {
    $.fancybox("#confirm",{
        modal: modal,
        beforeShow: function() {
            $(".title").html(msg);
        },
        afterShow: function() {
            $(".confirm").on("click", function(event){
                if($(event.target).is(".yes")){
                    ret = true;
                } else if ($(event.target).is(".no")){
                    ret = false;
                }
                $.fancybox.close();
            });
        },
        afterClose: function() {
            callback.call(this, ret);
        }
    });
}


function addParam(url,param){

    var hasQuery = url.indexOf("?") + 1;
    var hasHash = url.indexOf("#") + 1;
    var appendix = (hasQuery ? "&" : "?") + param;

    return hasHash ? href.replace("#", appendix + "#") : url + appendix;

}


function saveOrderInfoJQ(paytpv_suscripcion){
    switch (paytpv_suscripcion){
        case 0: // Normal Payment
            paytpv_agree = $("#paytpv_savecard").is(':checked')?1:0;
            paytpv_periodicity = 0;
            paytpv_cycles = 0;
        break;
        case 1: // Suscription
            paytpv_agree = 0;
            paytpv_periodicity = $("#paytpv_periodicity").val();
            paytpv_cycles = $("#paytpv_cycles").val()
            break;
    }

    $.ajax({
        url: addParam($("#paytpv_module").val(),'process=saveOrderInfo'),
        type: "POST",
        data: {
            'paytpv_agree': paytpv_agree,
            'paytpv_suscripcion': paytpv_suscripcion,
            'paytpv_periodicity': paytpv_periodicity,
            'paytpv_cycles': paytpv_cycles,
            'id_cart' : $("#id_cart").val(),
            'ajax': true
        },
        dataType:"json"
    })

    if (paytpv_suscripcion == 1){
        suscribeJQ();
    }
}

function addCardJQ(){
    $("#paytpv_iframe").attr("src","");
    $(".paytpv_iframe").show();
    $("#ajax_loader").show();
    paytpv_agree = $("#paytpv_savecard").is(':checked')?1:0;
    $.ajax({
        url: addParam($("#paytpv_module").val(),'process=addCard'),
        type: "POST",
        data: {
            'paytpv_agree': paytpv_agree,
            'id_cart' : $("#id_cart").val(),
            'ajax': true
        },
        success: function(result)
        {
            if (result.error=='0')
            {
                $("#paytpv_iframe").attr("src",result.url).one("load",function() {
                    $("#ajax_loader").hide();
                });

                //$(".paytpv_iframe").show(500);
            }
        },
        dataType:"json"
    });
}

function suscribeJQ(){
    $("#paytpv_iframe").attr("src","");
    $(".paytpv_iframe").show();
    $("#ajax_loader").show();
    $.ajax({
        url: addParam($("#paytpv_module").val(),'process=suscribe'),
        type: "POST",
        data: {
            'paytpv_agree': 0,
            'paytpv_suscripcion': 1,
            'paytpv_periodicity': $("#paytpv_periodicity").val(),
            'paytpv_cycles': $("#paytpv_cycles").val(),
            'id_cart' : $("#id_cart").val(),
            'ajax': true
        },
        success: function(result)
        {

            if (result.error=='0')
            {
                $("#storingStep").hide();
                $("#paytpv_iframe_aux").val(result.url);
                $("#paytpv_iframe").attr("src",result.url).one("load",function() {
                    $("#ajax_loader").hide();
                });;
                //$(".paytpv_iframe").show(500);
            }
        },
        dataType:"json"
    });
}

