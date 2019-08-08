<?php

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

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_6_3_4($object)
{

	try{
		Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'paytpv_terminal` 
			MODIFY idterminal int(6) null,
			MODIFY password varchar(30) null,
			ADD COLUMN `idterminal_ns` INT(6) AFTER idterminal,
			ADD COLUMN `password_ns` VARCHAR(30) AFTER password,
			ADD COLUMN `jetid_ns` VARCHAR(32) AFTER jetid
			');

	}catch (exception $e){}

	// Actualizar terminales No Seguros con la misma info que el Seguro
	try{
		
		$sql = 'UPDATE '. _DB_PREFIX_ .'paytpv_terminal set idterminal_ns = idterminal, password_ns = password, jetid_ns = jetid where terminales in (1,2)';
		Db::getInstance()->Execute($sql);

	}catch (exception $e){}

	return true;
}