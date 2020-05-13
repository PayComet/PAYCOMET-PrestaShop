{*
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
*}


{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8':FALSE}">{l s='My account' mod='paytpv'}</a>
    <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8':FALSE}</span>
        {l s='My Cards and Subscriptions' mod='paytpv'}</a>
        
{/capture}


<script type="text/javascript">
    var url_removecard = "{$url_removecard|escape:'htmlall':'UTF-8':FALSE}";
    var url_cancelsuscription = "{$url_cancelsuscription|escape:'htmlall':'UTF-8':FALSE}";
    var url_savedesc = "{$url_savedesc|escape:'htmlall':'UTF-8':FALSE}";
    var msg_cancelsuscription = "{l s='Cancel Subscription' mod='paytpv'}"
    var msg_removecard = "{l s='Remove Card' mod='paytpv'}";
    var msg_accept = "{l s='You must accept the terms and conditions of the service' mod='paytpv'}";
    var msg_savedesc = "{l s='Save description' mod='paytpv'}";
    var msg_descriptionsaved = "{l s='Description saved' mod='paytpv'}";
    var status_canceled = "{$status_canceled|escape:'htmlall':'UTF-8':FALSE}";
    
</script>

{if {$error}!=""}
<div class="alert alert-danger">{$error|escape:'htmlall':'UTF-8':FALSE}</div>
{/if}

<div id="row">
    <h2>{l s='My Cards' mod='paytpv'}</h2>
    {if isset($saved_card[0])}
        <div class="span6" id="div_tarjetas">
            {l s='Available Cards' mod='paytpv'}:
            {section name=card loop=$saved_card}   
                <div class="bankstoreCard" id="card_{$saved_card[card].IDUSER|escape:'htmlall':'UTF-8':FALSE}">  
                    {$saved_card[card].CC|escape:'htmlall':'UTF-8':FALSE} ({$saved_card[card].BRAND|escape:'htmlall':'UTF-8':FALSE})
                    <input type="text" maxlength="32" style="width:300px" id="card_desc_{$saved_card[card].IDUSER|escape:'htmlall':'UTF-8':FALSE}" name="card_desc_{$saved_card[card].IDUSER|escape:'htmlall':'UTF-8':FALSE}" value="{$saved_card[card].CARD_DESC|escape:'htmlall':'UTF-8':FALSE}" placeholder="{l s='Add a description' mod='paytpv'}">
                    <label class="button_del">
                        <a href="#" id="{$saved_card[card].IDUSER|escape:'htmlall':'UTF-8':FALSE}" class="save_desc">
                         {l s='Save description' mod='paytpv'}
                        </a>
                         | 
                        <a href="#" id="{$saved_card[card].IDUSER|escape:'htmlall':'UTF-8':FALSE}" class="remove_card">
                         {l s='Remove Card' mod='paytpv'}
                        </a>
                       
                        <input type="hidden" name="cc_{$saved_card[card].IDUSER|escape:'htmlall':'UTF-8':FALSE}" id="cc_{$saved_card[card].IDUSER|escape:'htmlall':'UTF-8':FALSE}" value="{$saved_card[card].CC|escape:'htmlall':'UTF-8':FALSE}">
                    </label>
                </div>
            {/section}
        </div>
   
    {else}
        <p class="warning">{l s='You still have no card associated.' mod='paytpv'}</p>
    {/if}

    <div id="storingStep" class="box">
        <h4>{l s='STREAMLINE YOUR FUTURE PURCHASES!' mod='paytpv'}</h4>
        <p>{l s='Link a card to your account to be able to make all procedures easily and quickly.' mod='paytpv'}</p>

        <p class="checkbox">
            <span class="checked"><input type="checkbox" name="paytpv_savecard" id="paytpv_savecard"></span>
            <label for="paytpv_savecard">{l s='By linking a card you accept the ' mod='paytpv'}<a id="open_conditions" href="#conditions" class="link"><strong>{l s='terms and conditions of the service' mod='paytpv'}</strong></a></label>
        </p>
        <p>
            <a href="javascript:void(0);" onclick="vincularTarjeta();" title="{l s='Link card' mod='paytpv'}" class="button button-small btn btn-default">
                <span>{l s='Link card' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
            </a>
            <a href="javascript:void(0);" onclick="close_vincularTarjeta();" title="{l s='Cancel' mod='paytpv'}" class="button button-small btn btn-default" id="close_vincular" style="display:none">
                <span>{l s='Cancel' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
            </a>
        </p>

        <div class="payment_module paytpv_iframe" id="nueva_tarjeta" style="display:none">
            {if ($paytpv_integration==0)}
                <iframe src="{$url_paytpv|escape:'htmlall':'UTF-8':FALSE}" id="paytpv_iframe" name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 342px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
            {else}
                <form action="{$paytpv_jetid_url|escape:'htmlall':'UTF-8'}" method="POST" class="paytpv_jet" id="paycometPaymentForm">
                    {include file='modules/paytpv/views/templates/hook/inc_payment_jetIframe.tpl'}
                </form>

            {/if}
        </div>
    </div>
    <hr>
    <h2>{l s='My Subscriptions' mod='paytpv'}</h2>
    {if isset($suscriptions[0])}
        <div class="span6" id="div_suscripciones">
            {l s='Subscriptions' mod='paytpv'}:
            <ul>
                {section name=suscription loop=$suscriptions} 
                    <li class="suscriptionCard" id="suscription_{$suscriptions[suscription].ID_SUSCRIPTION|escape:'htmlall':'UTF-8':FALSE}">  
                        <a href="{$link->getPageLink('order-detail',true,null,"id_order={$suscriptions[suscription].ID_ORDER}")|escape:'html'}">{l s='Order' mod='paytpv'}: {$suscriptions[suscription].ORDER_REFERENCE}</a>
                        <br>
                        {l s='Every' mod='paytpv'} {$suscriptions[suscription].PERIODICITY|escape:'htmlall':'UTF-8':FALSE} {l s='days' mod='paytpv'} - {l s='repeat' mod='paytpv'} {$suscriptions[suscription].CYCLES|escape:'htmlall':'UTF-8':FALSE} {l s='times' mod='paytpv'} - {l s='Amount' mod='paytpv'}: {$suscriptions[suscription].PRICE|escape:'htmlall':'UTF-8':FALSE} - {l s='Start' mod='paytpv'}: {$suscriptions[suscription].DATE_YYYYMMDD|escape:'htmlall':'UTF-8':FALSE}
                        <label class="button_del">
                            {if $suscriptions[suscription].STATUS==0}
                                <a href="#" id="{$suscriptions[suscription].ID_SUSCRIPTION|escape:'htmlall':'UTF-8':FALSE}" class="cancel_suscription">
                                 {l s='Cancel Subscription' mod='paytpv'}
                                </a>
                            {else if $suscriptions[suscription].STATUS==1}
                                <span class="canceled_suscription">
                                    {l s='CANCELLED' mod='paytpv'}
                                </span>
                            {else if $suscriptions[suscription].STATUS==2}
                                <span class="finised_suscription">
                                    {l s='ENDED' mod='paytpv'}
                                </span>
                            {/if}
                        </label>
                        <div class="span6" id="div_suscripciones_pay">
                            {$suscription_pay = $suscriptions[suscription].SUSCRIPTION_PAY}
                            <ul >
                                {section name=suscription_pay loop=$suscription_pay}
                                <li class="suscription_pay" id="suscription_pay{$suscription_pay[suscription_pay].ID_SUSCRIPTION|escape:'htmlall':'UTF-8':FALSE}">
                                     <a href="{$link->getPageLink('order-detail',true,null,"id_order={$suscription_pay[suscription_pay].ID_ORDER}")|escape:'html'}">{l s='Order' mod='paytpv'}: {$suscription_pay[suscription_pay].ORDER_REFERENCE}</a>
                                     {l s='Amount' mod='paytpv'}: {$suscription_pay[suscription_pay].PRICE|escape:'htmlall':'UTF-8':FALSE} - {l s='Date' mod='paytpv'}: {$suscription_pay[suscription_pay].DATE_YYYYMMDD|escape:'htmlall':'UTF-8':FALSE}

                                </li>
                                {/section}
                            </ul>

                        </div>
                    </li>
                {/section}
            </ul>
        </div>
   
    {else}
        <p class="warning">{l s='There are no subscriptions.' mod='paytpv'}</p>
    {/if}

    <div id="alert" style="display:none">
        <p class="title"></p>
    </div>

    <div id="confirm" style="display:none">
        <p class="title"></p>
        <input type="button" class="confirm yes button" value="{l s='Accept' mod='paytpv'}" />
        <input type="button" class="confirm no button" value="{l s='Cancel' mod='paytpv'}" />
        <input type="hidden" name="paytpv_cc" id="paytpv_cc">
        <input type="hidden" name="paytpv_iduser" id="paytpv_iduser">
        <input type="hidden" name="id_suscription" id="id_suscription">
        <input type="hidden" name="newpage_payment" id="newpage_payment" value="{$newpage_payment|escape:'htmlall':'UTF-8':FALSE}">
    </div>

    <div style="display: none;">
        {include file='modules/paytpv/views/templates/hook/inc_payment_conditions.tpl'}
    </div>


    
</div>