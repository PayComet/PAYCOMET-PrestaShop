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
    var msg_accept = "{l s='You must accept save card to continue' mod='paytpv'}";
    var msg_savedesc = "{l s='Save description' mod='paytpv'}";
    var msg_descriptionsaved = "{l s='Description saved' mod='paytpv'}";
    var status_canceled = "{$status_canceled|escape:'htmlall':'UTF-8':FALSE}";
    
</script>

{if {$error}!=""}
<div class="alert alert-danger">{$error|escape:'htmlall':'UTF-8':FALSE}</div>
{/if}

<div id="paytpv_block_account">
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
        <p class="checkbox">
            <span class="checked"><input type="checkbox" name="savecard" id="savecard"></span>
            <label for="savecard">{l s='Save card for future purchases' mod='paytpv'}.<span class="paytpv-pci">{l s='Card data is protected by the Payment Card Industry Data Security Standard (PCI DSS)' mod='paytpv'}.</span></label>
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
                <iframe src="{$url_paytpv|escape:'htmlall':'UTF-8':FALSE}" id="paytpv_iframe" name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 360px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
            {else}
                <form action="{$paytpv_jetid_url|escape:'htmlall':'UTF-8':FALSE}" method="POST" class="paytpv_jet" id="paytpvPaymentForm" onsubmit="return takingOff();">
                <ul>
                    <li>
                        <label for="MERCHANT_PAN">{l s='Credit Card Number' mod='paytpv'}:</label>
                         <input type="text" data-paytpv="paNumber" width="360" maxlength="16" value="" required="required" placeholder="1234 5678 9012 3456" pattern="{literal}[0-9]{15,16}{/literal}" onclick="this.value='';">
                    </li>
                    <li class="vertical">
                        <ul>
                            <li>
                                <label for="expiry_date">{l s='Expiration' mod='paytpv'}</label>
                                <input id="expiry_date" maxlength="5" placeholder="{l s='mm/yy' mod='paytpv'}" id="expiry_date" required="required" pattern="{literal}[0-9]{2}/+[0-9]{2}{/literal}"  type="text" onChange="buildED();">
                                <input type="hidden" data-paytpv="dateMonth" maxlength="2" id="mm" value="">
                                <input type="hidden" data-paytpv="dateYear" maxlength="2" id="yy" value="">
                            </li>

                            <li>
                                <label for="MERCHANT_CVC2">CVV</label>
                                <input type="text" data-paytpv="cvc2" maxlength="4"  value="" required="required" placeholder="123" pattern="{literal}[0-9]{3,4}{/literal}" onclick="this.value='';">
                                
                            </li>
                            <small class="help">{l s='The CVV is a numerical code, usually 3 digits behind the card' mod='paytpv'}.</small>
                        </ul>
                    </li>
                    <li>
                        <label for="Nombre">{l s='Cardholder name' mod='paytpv'}</label>
                        <input type="text" class="paytpv_cardholdername" data-paytpv="cardHolderName" width="360" maxlength="50" value="" required="required" placeholder="{l s='Name surname' mod='paytpv'}" onclick="this.value='';">
                    </li>
                    <li>
                        
                        <input type="submit" class="button" value="{l s='Save Card' mod='paytpv'}" id="btnforg" style="display: inline-block;font-size: 21px;font-weight: 300;line-height: 46px;height:46px;padding: 0 0px;text-align: center;width: 100%;" onclick="buildED();">
                        
                        <div class="button" id="clockwait_jet" style="display:none;"><img src="{$this_path|escape:'htmlall':'UTF-8':FALSE}views/img/loader.gif" title="{l s='Wait' mod='paytpv'}" /></div>

                        <span style="color:red;font-weight:bold;" id="paymentErrorMsg"></span>
                    </li>
                </ul>
                </form>

                <script type="text/javascript" src="{$jet_paytpv|escape:'htmlall':'UTF-8':FALSE}?id={$jet_id|escape:'htmlall':'UTF-8':FALSE}&language={$jet_lang|escape:'htmlall':'UTF-8':FALSE}"></script>
                <script type="text/javascript">
                    {$js_code|escape:'htmlall':'UTF-8':FALSE}
                </script>
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
                        <a href="{$link->getPageLink('order-detail',true,null,"id_order={$suscriptions[suscription].ID_ORDER|escape:'htmlall':'UTF-8':FALSE}")|escape:'htmlall':'UTF-8'}">{l s='Order' mod='paytpv'}: {$suscriptions[suscription].ORDER_REFERENCE|escape:'htmlall':'UTF-8':FALSE}</a>
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
                                     <a href="{$link->getPageLink('order-detail',true,null,"id_order={$suscription_pay[suscription_pay].ID_ORDER|escape:'htmlall':'UTF-8':FALSE}")|escape:'htmlall':'UTF-8'}">{l s='Order' mod='paytpv'}: {$suscription_pay[suscription_pay].ORDER_REFERENCE|escape:'htmlall':'UTF-8':FALSE}</a>
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
        <div id="conditions" style="overflow:auto;">
            <h1 class="estilo-tit1">{l s='Related Cards' mod='paytpv'}</h1>
            <p>
            {l s='This business does not store or transmit credit card or debit card data. Data are sent over an encrypted and secure channel to the PAYCOMET platform.' mod='paytpv'}
            </p>
            <p>
            {l s='At any time, the user can add or remove data from their linked cards. In the section My account, they will see a section "My linked cards" where stored cards are displayed and they may be removed.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Security Policy' mod='paytpv'}</h2>
            <p>
            {l s='All transaction information transmitted between this site and PAYCOMET systems is encrypted using 256-bit SSL certificates. All cardholder information is transmitted encrypted and all messages sent to your servers from PAYCOMET are signed using SHA hashing to prevent tampering. The information that is transmitted to PAYCOMET servers cannot be examined, scanned, used or modified by any external party that gains access to confidential information.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Encryption and Data Storage' mod='paytpv'}</h2>
            <p>
            {l s='Once in the PAYCOMET systems, confidential information is protected using standard 1024-bit encryption. Encryption keys are kept in volatile high security systems with double authentication, which makes their extraction impossible. Banks, security agents and banking institutions perform regular audits to ensure data protection.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='System Safety' mod='paytpv'}</h2>
            <p>
            {l s='PAYCOMET systems are reviewed quarterly by specific ISO tools, an independent Qualified Security Assessor (QSA) and a scanning vendor (ASV) approved by the payment card brands.' mod='paytpv'}
            </p>
            <p>
            {l s='PAYCOMET is also subject to an annual audit according to the standards of data security of the Payment Card Industry (PCI DSS) and is a fully approved Level 1 provider of payment services, which is the highest level of compliance.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Links to banking institutions' mod='paytpv'}</h2>
            <p>
            {l s='PAYCOMET has multiple private links to banking networks that are completely independent of the Internet and which do not cross any public access network. All the information of the holder sent to banks and all the authorization messages sent in response are protected and cannot be manipulated.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Internal security' mod='paytpv'}</h2>
            <p>
            {l s='PAYCOMET is audited in access controls to production environments. The CPD where systems are hosted operate according to the requirements for Tier III centers. This ensures that safety is not put at risk at any time. It has sophisticated alarm systems, surveillance by means of closed circuit TV and security guards 24 hours a day, 7 days a week on site, as well as rigorous monitoring and maintenance. All the information about transactions and customer cards is protected even from our own employees.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Disaster Recovery' mod='paytpv'}</h2>
            <p>
            {l s='PAYCOMET has Backup systems hosted in different countries to ensure optimal safety of the systems and high availability. It also has a complete business continuity and disaster recovery policy.' mod='paytpv'}
            </p>
            <p>&nbsp;</p>
        </div>
    </div>


    
</div>