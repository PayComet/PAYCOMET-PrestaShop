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

<li class="lnk_paytpv">
	<a href="{$link->getModuleLink('paytpv', 'account')|escape:'htmlall':'UTF-8'}" title="{l s='My Cards' mod='paytpv'}">
		<i class="icon-paytpv"><img src="{$module_template_dir}views/img/tarjetas.gif" class="ui-li-icon ui-li-thumb" class="icon" alt="{l s='My Cards paytpv' mod='paytpv'}"/></i>
		<span>{l s='My Cards and Subscriptions' mod='paytpv'}</span>
	</a>
</li>