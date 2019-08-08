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

function upgrade_module_6_1_2($object)
{
	try{
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paytpv_terminal` (
			`id` INT(2) UNSIGNED NOT NULL,
			`idterminal` INT(4) UNSIGNED NOT NULL,
			`password` VARCHAR(30) NOT NULL,
			`currency_iso_code` VARCHAR(3) NOT NULL,
			`terminales` SMALLINT(1) NOT NULL DEFAULT 0,
			`tdfirst` SMALLINT(1) NOT NULL DEFAULT 1,
			`tdmin` DECIMAL(17,2),
			PRIMARY KEY (`id`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');

	}catch (exception $e){}

	// Insert Client Terminal info in paytpv_terminal table
	try{
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency = new Currency(intval($id_currency));

		$tdmin = (Configuration::get('PAYTPV_3DMIN')=="")?0:Configuration::get('PAYTPV_3DMIN');

		$sql = 'INSERT INTO '. _DB_PREFIX_ .'paytpv_terminal (id,idterminal,password,currency_iso_code,terminales,tdfirst,tdmin) VALUES(1,'.Configuration::get('PAYTPV_TERM').',"'.Configuration::get('PAYTPV_PASS').'","'.$currency->iso_code.'",'.Configuration::get('PAYTPV_TERMINALES').','.Configuration::get('PAYTPV_3DFIRST').','.$tdmin.')';
		Db::getInstance()->Execute($sql);

	}catch (exception $e){}


	// Valores a eliminar
	Configuration::deleteByName('PAYTPV_3DFIRST');
	Configuration::deleteByName('PAYTPV_3DMIN');
	Configuration::deleteByName('PAYTPV_TERMINALES');
	Configuration::deleteByName('PAYTPV_TERM');
	Configuration::deleteByName('PAYTPV_PASS');
	
	return true;
}