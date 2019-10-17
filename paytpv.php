<?php
/**
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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/ClassRegistro.php';
include_once dirname(__FILE__) . '/classes/PaytpvTerminal.php';
include_once dirname(__FILE__) . '/classes/PaytpvOrder.php';
include_once dirname(__FILE__) . '/classes/PaytpvOrderInfo.php';
include_once dirname(__FILE__) . '/classes/PaytpvCustomer.php';
include_once dirname(__FILE__) . '/classes/PaytpvSuscription.php';
include_once dirname(__FILE__) . '/classes/PaytpvRefund.php';

class Paytpv extends PaymentModule
{
    private $html = '';

    private $postErrors = array();

    public function __construct()
    {
        $this->name = 'paytpv';
        $this->tab = 'payments_gateways';
        $this->author = 'Paycomet';
        $this->version = '6.5.4';
        $this->module_key = '73836a23c42bbcdec111cc1309a41776';

        //$this->bootstrap = true;
        // Array config:  configuration values
        $config = $this->getConfigValues();


        $this->url_paytpv = "https://api.paycomet.com/gateway/ifr-bankstore";
        $this->endpoint_paytpv = "https://api.paycomet.com/gateway/xml-bankstore";
        $this->jet_paytpv = "https://api.paycomet.com/gateway/jet-paytpv.js";


        if (array_key_exists('PAYTPV_INTEGRATION', $config)) {
            $this->integration = $config['PAYTPV_INTEGRATION'];
        }


        if (array_key_exists('PAYTPV_CLIENTCODE', $config)) {
            $this->clientcode = $config['PAYTPV_CLIENTCODE'];
        }


        if (array_key_exists('PAYTPV_COMMERCEPASSWORD', $config)) {
            $this->commerce_password = $config['PAYTPV_COMMERCEPASSWORD'];
        }
        if (array_key_exists('PAYTPV_NEWPAGEPAYMENT', $config)) {
            $this->newpage_payment = $config['PAYTPV_NEWPAGEPAYMENT'];
        }
        if (array_key_exists('PAYTPV_SUSCRIPTIONS', $config)) {
            $this->suscriptions = $config['PAYTPV_SUSCRIPTIONS'];
        }
        if (array_key_exists('PAYTPV_REG_ESTADO', $config)) {
            $this->reg_estado = $config['PAYTPV_REG_ESTADO'];
        }


        if (array_key_exists('PAYTPV_FIRSTPURCHASE_SCORING', $config)) {
            $this->firstpurchase_scoring = $config['PAYTPV_FIRSTPURCHASE_SCORING'];
        }
        if (array_key_exists('PAYTPV_FIRSTPURCHASE_SCORING_SCO', $config)) {
            $this->firstpurchase_scoring_score = $config['PAYTPV_FIRSTPURCHASE_SCORING_SCO'];
        }
        if (array_key_exists('PAYTPV_SESSIONTIME_SCORING', $config)) {
            $this->sessiontime_scoring = $config['PAYTPV_SESSIONTIME_SCORING'];
        }
        if (array_key_exists('PAYTPV_SESSIONTIME_SCORING_VAL', $config)) {
            $this->sessiontime_scoring_val = $config['PAYTPV_SESSIONTIME_SCORING_VAL'];
        }
        if (array_key_exists('PAYTPV_SESSIONTIME_SCORING_SCORE', $config)) {
            $this->sessiontime_scoring_score = $config['PAYTPV_SESSIONTIME_SCORING_SCORE'];
        }
        if (array_key_exists('PAYTPV_DCOUNTRY_SCORING', $config)) {
            $this->dcountry_scoring = $config['PAYTPV_DCOUNTRY_SCORING'];
        }
        if (array_key_exists('PAYTPV_DCOUNTRY_SCORING_VAL', $config)) {
            $this->dcountry_scoring_val = $config['PAYTPV_DCOUNTRY_SCORING_VAL'];
        }
        if (array_key_exists('PAYTPV_DCOUNTRY_SCORING_SCORE', $config)) {
            $this->dcountry_scoring_score = $config['PAYTPV_DCOUNTRY_SCORING_SCORE'];
        }
        if (array_key_exists('PAYTPV_IPCHANGE_SCORING', $config)) {
            $this->ip_change_scoring = $config['PAYTPV_IPCHANGE_SCORING'];
        }
        if (array_key_exists('PAYTPV_IPCHANGE_SCORING_SCORE', $config)) {
            $this->ip_change_scoring_score = $config['PAYTPV_IPCHANGE_SCORING_SCORE'];
        }
        if (array_key_exists('PAYTPV_BROWSER_SCORING', $config)) {
            $this->browser_scoring = $config['PAYTPV_BROWSER_SCORING'];
        }
        if (array_key_exists('PAYTPV_BROWSER_SCORING_SCORE', $config)) {
            $this->browser_scoring_score = $config['PAYTPV_BROWSER_SCORING_SCORE'];
        }
        if (array_key_exists('PAYTPV_SO_SCORING', $config)) {
            $this->so_scoring = $config['PAYTPV_SO_SCORING'];
        }
        if (array_key_exists('PAYTPV_SO_SCORING_SCORE', $config)) {
            $this->so_scoring_score = $config['PAYTPV_SO_SCORING_SCORE'];
        }

        if (array_key_exists('PAYTPV_DISABLEOFFERSAVECARD', $config)) {
            $this->disableoffersavecard = $config['PAYTPV_DISABLEOFFERSAVECARD'];
        }


        if (array_key_exists('PAYTPV_REMEMBERCARDUNSELECTED', $config)) {
            $this->remembercardunselected = $config['PAYTPV_REMEMBERCARDUNSELECTED'];
        }

        parent::__construct();
        $this->page = basename(__FILE__, '.php');

        $this->displayName = $this->l('Paycomet');
        $this->description = $this->l('This module allows you to accept card payments via www.paycomet.com');

        try {
            if (!isset($this->clientcode) or $this->clientcode=="" or !PaytpvTerminal::existTerminal()) {
                $this->warning = $this->l('Missing data when configuring the module PAYCOMET');
            }
        } catch (exception $e) {
        }
    }


    public function runUpgradeModule()
    {
        parent::runUpgradeModule();
    }


    public function install()
    {
        include_once(_PS_MODULE_DIR_ . '/' . $this->name . '/paytpv_install.php');
        $paypal_install = new PayTpvInstall();
        $res = $paypal_install->createTables();
        if (!$res) {
            $this->error = $this->l('Missing data when configuring the module PAYCOMET');
            return false;
        }

        $paypal_install->updateConfiguration();

        // Valores por defecto al instalar el módulo
        if (!parent::install() ||
            !$this->registerHook('displayPayment') ||
            !$this->registerHook('displayPaymentTop') ||
            !$this->registerHook('displayPaymentReturn') ||
            !$this->registerHook('displayMyAccountBlock') ||
            !$this->registerHook('displayAdminOrder') ||
            !$this->registerHook('displayCustomerAccount') ||
            !$this->registerHook('actionProductCancel') ||
            !$this->registerHook('displayShoppingCart')
        ) {
            return false;
        }


        return true;
    }
    public function uninstall()
    {
        include_once(_PS_MODULE_DIR_ . '/' . $this->name . '/paytpv_install.php');
        $paypal_install = new PayTpvInstall();
        $paypal_install->deleteConfiguration();
        return parent::uninstall();
    }

    public function getPath()
    {
        return $this->_path;
    }

    private function postValidation()
    {

        // Show error when required fields.
        if (Tools::getIsset('btnSubmit')) {
            if (empty(Tools::getValue('clientcode'))) {
                $this->postErrors[] = $this->l('Client Code required');
            }
            if (empty(Tools::getValue('pass'))) {
                $this->postErrors[] = $this->l('User Password required');
            }


            // Check Terminal empty fields SECURE
            foreach (Tools::getValue('term') as $key => $term) {
                if ((Tools::getValue("terminales")[$key] == 0 || Tools::getValue("terminales")[$key] == 2)
                    && ($term == "" || !is_numeric($term))
                ) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. [3D SECURE] "
                        . $this->l('Terminal number invalid');
                }

                if ((Tools::getValue("terminales")[$key] == 0 || Tools::getValue("terminales")[$key] == 2)
                    && Tools::getValue("pass")[$key] == ""
                ) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. [3D SECURE] "
                        . $this->l('Password invalid');
                }

                if ((Tools::getValue("terminales")[$key] == 0 || Tools::getValue("terminales")[$key] == 2)
                    && Tools::getValue("jetid")[$key] == "" && Tools::getValue("integration") == 1
                ) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. [3D SECURE] "
                        . $this->l('JET ID number invalid');
                }
            }

            // Check Terminal empty fields NO SECURE
            foreach (Tools::getValue('term_ns') as $key => $term_ns) {
                if ((Tools::getValue("terminales")[$key] == 1 || Tools::getValue("terminales")[$key] == 2)
                    && ($term_ns == "" || !is_numeric($term_ns))
                ) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. [NO 3D SECURE] "
                        . $this->l('Terminal number invalid');
                }

                if ((Tools::getValue("terminales")[$key] == 1 || Tools::getValue("terminales")[$key] == 2)
                    && Tools::getValue("pass_ns")[$key] == ""
                ) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. [NO 3D SECURE] "
                        . $this->l('Password invalid');
                }

                if ((Tools::getValue("terminales")[$key] == 1 || Tools::getValue("terminales")[$key] == 2)
                    && Tools::getValue("jetid_ns")[$key] == "" && Tools::getValue("integration") == 1
                ) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. [NO 3D SECURE] "
                        . $this->l('JET ID number invalid');
                }
            }

            // Check 3Dmin and Currency
            foreach (Tools::getValue('term_ns') as $key => $term_ns) {
                if (Tools::getValue("terminales")[$key] == 2 && (Tools::getValue("tdmin")[$key] != "" &&
                    !is_numeric(Tools::getValue("tdmin")[$key]))) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. "
                        . $this->l('Use 3D Secure on purchases over invalid');
                }

                if (empty(Tools::getValue('moneda')[$key])) {
                    $this->postErrors[] = $this->l('Terminal') . " " . ($key + 1) . "º. "
                    . $this->l('Currency required');
                }
            }

            // Check Duplicate Terms
            $arrTerminales = array_unique(Tools::getValue('term'));
            if (sizeof($arrTerminales) != sizeof(Tools::getValue('term'))) {
                $this->postErrors[] = $this->l('Duplicate Terminals');
            }

            // Check Duplicate Currency
            $arrMonedas = array_unique(Tools::getValue('moneda'));
            if (sizeof($arrMonedas) != sizeof(Tools::getValue('moneda'))) {
                $this->postErrors[] = $this->l('Duplicate Currency. Specify a different currency for each terminal');
            }

            // Si no hay errores previos se contrastan los datos
            if (!sizeof($this->postErrors)) {
                $arrValidatePaycomet = $this->validatePaycomet();
                if ($arrValidatePaycomet["error"] != 0) {
                    $this->postErrors[] = $arrValidatePaycomet["error_txt"];
                }
            }
        }
    }

    private function validatePaycomet()
    {
        include_once(_PS_MODULE_DIR_ . '/paytpv/classes/PaytpvApi.php');

        $api = new PaytpvApi();

        $arrDatos = array();
        $arrDatos["error"] = 0;
        

        // Validación de los datos en Paycomet
        foreach (array_keys(Tools::getValue("term")) as $key) {
            $term = (Tools::getValue('term')[$key] == '') ? "" : Tools::getValue('term')[$key];
            $term_ns = (Tools::getValue('term_ns')[$key] == '') ? "" : Tools::getValue('term_ns')[$key];

            switch (Tools::getValue("terminales")[$key]) {
                case 0:  // Seguro
                    $terminales_txt = $this->l('Secure');
                    $resp = $api->validatePaycomet(
                        Tools::getValue('clientcode'),
                        $term,
                        Tools::getValue("pass")[$key],
                        "CES"
                    );
                    
                    break;
                case 1: // No Seguro
                    $terminales_txt = $this->l('Non-Secure');
                    $resp = $api->validatePaycomet(
                        Tools::getValue('clientcode'),
                        $term_ns,
                        Tools::getValue("pass_ns")[$key],
                        "NO-CES"
                    );
                    break;
                case 2: // Ambos
                    $terminales_txt = $this->l('Both');
                    $resp = $api->validatePaycomet(
                        Tools::getValue('clientcode'),
                        $term,
                        Tools::getValue("pass")[$key],
                        "BOTH"
                    );
                    break;
            }

            
            if ($resp["DS_RESPONSE"] != 1) {
                $arrDatos["error"] = 1;
                switch ($resp["DS_ERROR_ID"]) {
                    case 1121:  // No se encuentra el cliente
                    case 1130:  // No se encuentra el producto
                    case 1003:  // Credenciales inválidas
                    case 127:   // Parámetro no válido.
                        $arrDatos["error_txt"] = $this->l('Check that the Client Code, Terminal and Password are correct.');
                        break;
                    case 1337:  // Ruta de notificación no configurada
                        $arrDatos["error_txt"] = $this->l('Notification URL is not defined in the product configuration of your account PAYCOMET account.');
                        break;
                    case 28:    // Curl
                    case 1338:  // Ruta de notificación no responde correctamente
                        $ssl = Configuration::get('PS_SSL_ENABLED');
                        $arrDatos["error_txt"] = $this->l('The notification URL defined in the product configuration of your PAYCOMET account does not respond correctly. Verify that it has been defined as: ')
                         . Context::getContext()->link->getModuleLink($this->name, 'url', array(), $ssl);
                        break;
                    case 1339:  // Configuración de terminales incorrecta
                        $arrDatos["error_txt"] = $this->l('Your Product in PAYCOMET account is not set up with the Available Terminals option: ') . $terminales_txt;
                        break;
                }
                return $arrDatos;
            }
        }

        return $arrDatos;
    }


    private function postProcess()
    {

        // Update databse configuration
        if (Tools::getIsset('btnSubmit')) {
            Configuration::updateValue('PAYTPV_CLIENTCODE', trim(Tools::getValue('clientcode')));

            Configuration::updateValue('PAYTPV_COMMERCEPASSWORD', Tools::getValue('commerce_password'));
            Configuration::updateValue('PAYTPV_NEWPAGEPAYMENT', Tools::getValue('newpage_payment'));
            Configuration::updateValue('PAYTPV_SUSCRIPTIONS', Tools::getValue('suscriptions'));

            Configuration::updateValue('PAYTPV_INTEGRATION', Tools::getValue('integration'));

            // Save Paytpv Terminals
            PaytpvTerminal::removeTerminals();

            foreach (array_keys(Tools::getValue("term")) as $key) {
                $aux_tdmin = (Tools::getValue('tdmin')[$key] == ''|| Tools::getValue("terminales")[$key] != 2) ?
                0 : Tools::getValue('tdmin')[$key];
                $aux_term = (Tools::getValue('term')[$key] == '') ? "" : Tools::getValue('term')[$key];
                $aux_term_ns = (Tools::getValue('term_ns')[$key] == '') ? "" : Tools::getValue('term_ns')[$key];
                PaytpvTerminal::addTerminal(
                    $key + 1,
                    trim($aux_term),
                    trim($aux_term_ns),
                    trim(Tools::getValue("pass")[$key]),
                    trim(Tools::getValue("pass_ns")[$key]),
                    trim(Tools::getValue("jetid")[$key]),
                    trim(Tools::getValue("jetid_ns")[$key]),
                    Tools::getValue("moneda")[$key],
                    Tools::getValue("terminales")[$key],
                    Tools::getValue("tdfirst")[$key],
                    $aux_tdmin
                );
            }

            // Datos Scoring

            Configuration::updateValue('PAYTPV_FIRSTPURCHASE_SCORING', Tools::getValue('firstpurchase_scoring'));
            Configuration::updateValue(
                'PAYTPV_FIRSTPURCHASE_SCORING_SCO',
                Tools::getValue('firstpurchase_scoring_score')
            );
            Configuration::updateValue('PAYTPV_SESSIONTIME_SCORING', Tools::getValue('sessiontime_scoring'));
            Configuration::updateValue('PAYTPV_SESSIONTIME_SCORING_VAL', Tools::getValue('sessiontime_scoring_val'));
            Configuration::updateValue(
                'PAYTPV_SESSIONTIME_SCORING_SCORE',
                Tools::getValue('sessiontime_scoring_score')
            );
            Configuration::updateValue('PAYTPV_DCOUNTRY_SCORING', Tools::getValue('dcountry_scoring'));
            Configuration::updateValue(
                'PAYTPV_DCOUNTRY_SCORING_VAL',
                Tools::getIsset('dcountry_scoring_val') ? implode(",", Tools::getValue('dcountry_scoring_val')) : ''
            );
            Configuration::updateValue('PAYTPV_DCOUNTRY_SCORING_SCORE', Tools::getValue('dcountry_scoring_score'));
            Configuration::updateValue('PAYTPV_IPCHANGE_SCORING', Tools::getValue('ip_change_scoring'));
            Configuration::updateValue('PAYTPV_IPCHANGE_SCORING_SCORE', Tools::getValue('ip_change_scoring_score'));
            Configuration::updateValue('PAYTPV_BROWSER_SCORING', Tools::getValue('browser_scoring'));
            Configuration::updateValue('PAYTPV_BROWSER_SCORING_SCORE', Tools::getValue('browser_scoring_score'));
            Configuration::updateValue('PAYTPV_SO_SCORING', Tools::getValue('so_scoring'));
            Configuration::updateValue('PAYTPV_SO_SCORING_SCORE', Tools::getValue('so_scoring_score'));

            Configuration::updateValue('PAYTPV_DISABLEOFFERSAVECARD', Tools::getValue('disableoffersavecard'));
            Configuration::updateValue('PAYTPV_REMEMBERCARDUNSELECTED', Tools::getValue('remembercardunselected'));


            return '<div class="bootstrap"><div class="alert alert-success">' . $this->l('Configuration updated')
                . '</div></div>';
        }
    }


    public function transactionScore($cart)
    {
        include_once(_PS_MODULE_DIR_ . '/paytpv/classes/PaytpvApi.php');

        $api = new PaytpvApi();

        $config = $this->getConfigValues();

        // Initialize array Score
        $arrScore = array();
        $arrScore["score"] = null;
        $arrScore["scoreCalc"] = null;

        $shipping_address_country = "";

        $shippingAddressData = new Address($cart->id_address_delivery);
        if ($shippingAddressData) {
            $address_country = new Country($shippingAddressData->id_country);
            $shipping_address_country = $address_country->iso_code;
        }

        // First Purchase
        if ($config["PAYTPV_FIRSTPURCHASE_SCORING"]) {
            $firstpurchase_scoring_score = $config["PAYTPV_FIRSTPURCHASE_SCORING_SCO"];
            if (PaytpvOrder::isFirstPurchaseCustomer($this->context->customer->id)) {
                $arrScore["scoreCalc"]["firstpurchase"] = $firstpurchase_scoring_score;
            }
        }

        // Complete Session Time
        if ($config["PAYTPV_SESSIONTIME_SCORING"]) {
            $sessiontime_scoring_val = $config["PAYTPV_SESSIONTIME_SCORING_VAL"];
            $sessiontime_scoring_score = $config["PAYTPV_SESSIONTIME_SCORING_SCORE"];

            $cookie = $this->context->cookie;
            if ($cookie && $cookie->id_connections) {
                $connection = new Connection($cookie->id_connections);
                $first_visit_at = $connection->date_add;

                $now = date('Y-m-d H:i:s');

                $time_ss = strtotime($now) - strtotime($first_visit_at);
                $time_mm = floor($time_ss / 60);

                if ($time_mm > $sessiontime_scoring_val) {
                    $arrScore["scoreCalc"]["completesessiontime"] = $sessiontime_scoring_score;
                }
            }
        }

        // Destination
        if ($config["PAYTPV_DCOUNTRY_SCORING"]) {
            $dcountry_scoring_val = explode(",", $config["PAYTPV_DCOUNTRY_SCORING_VAL"]);
            $dcountry_scoring_score = $config["PAYTPV_DCOUNTRY_SCORING_SCORE"];

            if (in_array($shipping_address_country, $dcountry_scoring_val)) {
                $arrScore["scoreCalc"]["destination"] = $dcountry_scoring_score;
            }
        }

        // Ip Change
        if ($config["PAYTPV_IPCHANGE_SCORING"]) {
            $connection = new Connection($cookie->id_connections);
            $ip_change_scoring = $config["PAYTPV_IPCHANGE_SCORING_SCORE"];
            $ip = Tools::getRemoteAddr() ? (int) ip2long(Tools::getRemoteAddr()) : '';
            $ip_session = $connection->ip_address ? (int) ip2long($connection->ip_address) : '';

            if ($ip != $ip_session) {
                $arrScore["scoreCalc"]["ipchange"] = $ip_change_scoring;
            }
        }

        // Browser Unidentified
        if ($config["PAYTPV_BROWSER_SCORING"]) {
            $browser_scoring_score = $config["PAYTPV_BROWSER_SCORING_SCORE"];
            if ($api->browserDetection('browser_name') == "") {
                $arrScore["scoreCalc"]["browser_unidentified"] = $browser_scoring_score;
            }
        }

        // Operating System Unidentified
        if ($config["PAYTPV_SO_SCORING"]) {
            $so_scoring_score = $config["PAYTPV_SO_SCORING_SCORE"];
            if ($api->browserDetection('os') == "") {
                $arrScore["scoreCalc"]["operating_system_unidentified"] = $so_scoring_score;
            }
        }

        // CALC ORDER SCORE
        if (sizeof($arrScore["scoreCalc"]) > 0) {
            //$score = floor(array_sum($arrScore["scoreCalc"]) / sizeof($arrScore["scoreCalc"]));   // Media
            $score = floor(array_sum($arrScore["scoreCalc"])); // Suma de valores. Si es superior a 100 asignamos 100
            if ($score > 100) {
                $score = 100;
            }
            $arrScore["score"] = $score;
        }

        return $arrScore;
    }


    public function threeDSRequestorAuthenticationInfo()
    {
        $customerStats = $this->context->customer->getStats();

        $threeDSReqAuthTimestamp = strftime('%Y%m%d%H%M', strtotime($customerStats['last_visit']));

        $threeDSRequestorAuthenticationInfo = array();
        $threeDSRequestorAuthenticationInfo["threeDSReqAuthData"] = "";
        $logged = $this->context->customer->isLogged();
        $threeDSRequestorAuthenticationInfo["threeDSReqAuthMethod"] = ($logged) ? "02" : "01";
        $threeDSRequestorAuthenticationInfo["threeDSReqAuthTimestamp"] = $threeDSReqAuthTimestamp;

        return $threeDSRequestorAuthenticationInfo;
    }


    public function acctInfo($cart)
    {
        $acctInfoData = array();
        $date_now = new DateTime("now");

        $isGuest = $this->context->customer->isGuest();
        if ($isGuest) {
            $acctInfoData["chAccAgeInd"] = "01";
        } else {
            $date_customer = new DateTime(strftime('%Y%m%d', strtotime($this->context->customer->date_add)));

            $diff = $date_now->diff($date_customer);
            $dias = $diff->days;

            if ($dias == 0) {
                $acctInfoData["chAccAgeInd"] = "02";
            } elseif ($dias < 30) {
                $acctInfoData["chAccAgeInd"] = "03";
            } elseif ($dias < 60) {
                $acctInfoData["chAccAgeInd"] = "04";
            } else {
                $acctInfoData["chAccAgeInd"] = "05";
            }
        }
        $acctInfoData["chAccChange"] = strftime('%Y%m%d', strtotime($this->context->customer->date_upd));

        $date_customer_upd = new DateTime(strftime('%Y%m%d', strtotime($this->context->customer->date_upd)));
        $diff = $date_now->diff($date_customer_upd);
        $dias_upd = $diff->days;

        if ($dias_upd == 0) {
            $acctInfoData["chAccChangeInd"] = "01";
        } elseif ($dias_upd < 30) {
            $acctInfoData["chAccChangeInd"] = "02";
        } elseif ($dias_upd < 60) {
            $acctInfoData["chAccChangeInd"] = "03";
        } else {
            $acctInfoData["chAccChangeInd"] = "04";
        }

        $acctInfoData["chAccDate"] = strftime('%Y%m%d', strtotime($this->context->customer->date_upd));
        //$acctInfoData["chAccPwChange"] = "";
        //$acctInfoData["chAccPwChangeInd"] = "";

        $acctInfoData["nbPurchaseAccount"] =
            PaytpvOrder::numPurchaseCustomer($this->context->customer->id, 1, 6, "MONTH");
        //$acctInfoData["provisionAttemptsDay"] = "";

        $acctInfoData["txnActivityDay"] = PaytpvOrder::numPurchaseCustomer($this->context->customer->id, 0, 1, "DAY");
        $acctInfoData["txnActivityYear"] =
            PaytpvOrder::numPurchaseCustomer($this->context->customer->id, 0, 1, "YEAR");

        //$acctInfoData["paymentAccAge"] = "";
        //$acctInfoData["paymentAccInd"] = "";

        $firstAddressDelivery =
            PaytpvOrder::firstAddressDelivery($this->context->customer->id, $cart->id_address_delivery);

        if ($firstAddressDelivery != "") {
            $acctInfoData["shipAddressUsage"] = date("Ymd", strtotime($firstAddressDelivery));

            $date_firstAddressDelivery = new DateTime(strftime('%Y%m%d', strtotime($firstAddressDelivery)));
            $diff = $date_now->diff($date_firstAddressDelivery);
            $dias_firstAddressDelivery = $diff->days;
            if ($dias_firstAddressDelivery == 0) {
                $acctInfoData["shipAddressUsageInd"] = "01";
            } elseif ($dias_upd < 30) {
                $acctInfoData["shipAddressUsageInd"] = "02";
            } elseif ($dias_upd < 60) {
                $acctInfoData["shipAddressUsageInd"] = "03";
            } else {
                $acctInfoData["shipAddressUsageInd"] = "04";
            }
        }

        // Shiping info
        $shipping = new Address($cart->id_address_delivery);

        if (($this->context->customer->firstname != $shipping->firstname)
        || ($this->context->customer->lastname != $shipping->lastname)
        ) {
            $acctInfoData["shipNameIndicator"] = "02";
        } else {
            $acctInfoData["shipNameIndicator"] = "01";
        }

        $acctInfoData["suspiciousAccActivity"] = "01";


        return $acctInfoData;
    }

    public function getShoppingCart($cart)
    {
        $shoppingCartData = array();

        foreach ($cart->getProducts() as $key => $product) {
            $shoppingCartData[$key]["sku"] = $product["reference"];
            $shoppingCartData[$key]["quantity"] = $product["cart_quantity"];
            $shoppingCartData[$key]["unitPrice"] = number_format($product["price"] * 100, 0, '.', '');
            $shoppingCartData[$key]["name"] = $product["name"];
            $shoppingCartData[$key]["category"] = $product["category"];
        }

        return array("shoppingCart" => array_values($shoppingCartData));
    }


    public function isoCodeToNumber($code)
    {
        $arrCode = array(
            "AF" => "004", "AX" => "248", "AL" => "008", "DE" => "276", "AD" => "020", "AO" => "024",
            "AI" => "660", "AQ" => "010", "AG" => "028", "SA" => "682", "DZ" => "012", "AR" => "032", "AM" => "051",
            "AW" => "533", "AU" => "036", "AT" => "040", "AZ" => "031", "BS" => "044", "BD" => "050", "BB" => "052",
            "BH" => "048", "BE" => "056", "BZ" => "084", "BJ" => "204", "BM" => "060", "BY" => "112", "BO" => "068",
            "BQ" => "535", "BA" => "070", "BW" => "072", "BR" => "076", "BN" => "096", "BG" => "100", "BF" => "854",
            "BI" => "108", "BT" => "064", "CV" => "132", "KH" => "116", "CM" => "120", "CA" => "124", "QA" => "634",
            "TD" => "148", "CL" => "52", "CN" => "156", "CY" => "196", "CO" => "170", "KM" => "174", "KP" => "408",
            "KR" => "410", "CI" => "384", "CR" => "188", "HR" => "191", "CU" => "192", "CW" => "531", "DK" => "208",
            "DM" => "212", "EC" => "218", "EG" => "818", "SV" => "222", "AE" => "784", "ER" => "232", "SK" => "703",
            "SI" => "705", "ES" => "724", "US" => "840", "EE" => "233", "ET" => "231", "PH" => "608", "FI" => "246",
            "FJ" => "242", "FR" => "250", "GA" => "266", "GM" => "270", "GE" => "268", "GH" => "288", "GI" => "292",
            "GD" => "308", "GR" => "300", "GL" => "304", "GP" => "312", "GU" => "316", "GT" => "320", "GF" => "254",
            "GG" => "831", "GN" => "324", "GW" => "624", "GQ" => "226", "GY" => "328", "HT" => "332", "HN" => "340",
            "HK" => "344", "HU" => "348", "IN" => "356", "ID" => "360", "IQ" => "368", "IR" => "364", "IE" => "372",
            "BV" => "074", "IM" => "833", "CX" => "162", "IS" => "352", "KY" => "136", "CC" => "166", "CK" => "184",
            "FO" => "234", "GS" => "239", "HM" => "334", "FK" => "238", "MP" => "580", "MH" => "584", "PN" => "612",
            "SB" => "090", "TC" => "796", "UM" => "581", "VG" => "092", "VI" => "850", "IL" => "376", "IT" => "380",
            "JM" => "388", "JP" => "392", "JE" => "832", "JO" => "400", "KZ" => "398", "KE" => "404", "KG" => "417",
            "KI" => "296", "KW" => "414", "LA" => "418", "LS" => "426", "LV" => "428", "LB" => "422", "LR" => "430",
            "LY" => "434", "LI" => "438", "LT" => "440", "LU" => "442", "MO" => "446", "MK" => "807", "MG" => "450",
            "MY" => "458", "MW" => "454", "MV" => "462", "ML" => "466", "MT" => "470", "MA" => "504", "MQ" => "474",
            "MU" => "480", "MR" => "478", "YT" => "175", "MX" => "484", "FM" => "583", "MD" => "498", "MC" => "492",
            "MN" => "496", "ME" => "499", "MS" => "500", "MZ" => "508", "MM" => "104", "NA" => "516", "NR" => "520",
            "NP" => "524", "NI" => "558", "NE" => "562", "NG" => "566", "NU" => "570", "NF" => "574", "NO" => "578",
            "NC" => "540", "NZ" => "554", "OM" => "512", "NL" => "528", "PK" => "586", "PW" => "585", "PS" => "275",
            "PA" => "591", "PG" => "598", "PY" => "600", "PE" => "604", "PF" => "258", "PL" => "616", "PT" => "620",
            "PR" => "630", "GB" => "826", "EH" => "732", "CF" => "140", "CZ" => "203", "CG" => "178", "CD" => "180",
            "DO" => "214", "RE" => "638", "RW" => "646", "RO" => "642", "RU" => "643", "WS" => "882", "AS" => "016",
            "BL" => "652", "KN" => "659", "SM" => "674", "MF" => "663", "PM" => "666", "VC" => "670", "SH" => "654",
            "LC" => "662", "ST" => "678", "SN" => "686", "RS" => "688", "SC" => "690", "SL" => "694", "SG" => "702",
            "SX" => "534", "SY" => "760", "SO" => "706", "LK" => "144", "SZ" => "748", "ZA" => "710", "SD" => "729",
            "SS" => "728", "SE" => "752", "CH" => "756", "SR" => "740", "SJ" => "744", "TH" => "764", "TW" => "158",
            "TZ" => "834", "TJ" => "762", "IO" => "086", "TF" => "260", "TL" => "626", "TG" => "768", "TK" => "772",
            "TO" => "776", "TT" => "780", "TN" => "788", "TM" => "795", "TR" => "792", "TV" => "798", "UA" => "804",
            "UG" => "800", "UY" => "858", "UZ" => "860", "VU" => "548", "VA" => "336", "VE" => "862", "VN" => "704",
            "WF" => "876", "YE" => "887", "DJ" => "262", "ZM" => "894", "ZW" => "716"
        );

        return $arrCode[$code];
    }

    public function getEMV3DS($cart)
    {
        $Merchant_EMV3DS = array();

        $Merchant_EMV3DS["customer"]["id"] = $this->context->customer->id;
        $Merchant_EMV3DS["customer"]["name"] = $this->context->customer->firstname;
        $Merchant_EMV3DS["customer"]["surname"] = $this->context->customer->lastname;
        $Merchant_EMV3DS["customer"]["email"] = $this->context->customer->email;


        // Billing info
        $billing = new Address((int) ($cart->id_address_invoice));

        if ($billing) {
            $billing_address_country = new Country($billing->id_country);
            $billing_address_state = new State($billing->id_state);


            $Merchant_EMV3DS["billing"]["billAddrCity"] = ($billing) ? $billing->city : "";
            $Merchant_EMV3DS["billing"]["billAddrCountry"] = ($billing) ? $billing_address_country->iso_code : "";
            if ($Merchant_EMV3DS["billing"]["billAddrCountry"] != "") {
                $Merchant_EMV3DS["billing"]["billAddrCountry"] =
                    $this->isoCodeToNumber($Merchant_EMV3DS["billing"]["billAddrCountry"]);
            }
            $Merchant_EMV3DS["billing"]["billAddrLine1"] = ($billing) ? $billing->address1 : "";
            $Merchant_EMV3DS["billing"]["billAddrLine2"] = ($billing) ? $billing->address2 : "";
            //$Merchant_EMV3DS["billing"]["billAddrLine3"] = "";
            $Merchant_EMV3DS["billing"]["billAddrPostCode"] = ($billing) ? $billing->postcode : "";

            if ($billing_address_state->iso_code != "") {
                $billAddState = explode("-", $billing_address_state->iso_code);
                $billAddState = end($billAddState);
                $Merchant_EMV3DS["billing"]["billAddrState"] = $billAddState;
            }



            if ($billing->phone) {
                $arrDatosHomePhone = array();

                $arrDatosHomePhone["cc"] = $billing_address_country->call_prefix;
                $arrDatosHomePhone["subscriber"] = $billing->phone;

                $Merchant_EMV3DS["customer"]["homePhone"] = $arrDatosHomePhone;
            }

            if ($billing->phone_mobile) {
                $arrDatosMobilePhone = array();

                $arrDatosMobilePhone["cc"] = $billing_address_country->call_prefix;
                $arrDatosMobilePhone["subscriber"] = $billing->phone_mobile;

                $Merchant_EMV3DS["customer"]["mobilePhone"] = $arrDatosMobilePhone;
            }
        }


        // Shiping info
        $shipping = new Address($cart->id_address_delivery);

        if ($shipping) {
            $shipping_address_country = new Country($shipping->id_country);
            $shipping_address_state = new State($shipping->id_state);

            $Merchant_EMV3DS["shipping"]["shipAddrCity"] = ($shipping) ? $shipping->city : "";
            $Merchant_EMV3DS["shipping"]["shipAddrCountry"] = ($shipping) ? $shipping_address_country->iso_code : "";
            if ($Merchant_EMV3DS["shipping"]["shipAddrCountry"] != "") {
                $Merchant_EMV3DS["shipping"]["shipAddrCountry"] =
                    $this->isoCodeToNumber($Merchant_EMV3DS["shipping"]["shipAddrCountry"]);
            }
            $Merchant_EMV3DS["shipping"]["shipAddrLine1"] = ($shipping) ? $shipping->address1 : "";
            $Merchant_EMV3DS["shipping"]["shipAddrLine2"] = ($shipping) ? $shipping->address2 : "";
            //$Merchant_EMV3DS["shipping"]["shipAddrLine3"] = "";
            $Merchant_EMV3DS["shipping"]["shipAddrPostCode"] = ($shipping) ? $shipping->postcode : "";

            if ($shipping_address_state->iso_code != "") {
                $shipAddrState = explode("-", $shipping_address_state->iso_code);
                $shipAddrState = end($shipAddrState);
                $Merchant_EMV3DS["shipping"]["shipAddrState"] = $shipAddrState;
            }

            if ($shipping->phone) {
                $arrDatosWorkPhone = array();

                $arrDatosWorkPhone["cc"] = $billing_address_country->call_prefix;
                $arrDatosWorkPhone["subscriber"] = $shipping->phone;

                $Merchant_EMV3DS["customer"]["workPhone"] = $arrDatosWorkPhone;
            }
        }

        // acctInfo
        $Merchant_EMV3DS["acctInfo"] = $this->acctInfo($cart);

        // threeDSRequestorAuthenticationInfo
        $Merchant_EMV3DS["threeDSRequestorAuthenticationInfo"] = $this->threeDSRequestorAuthenticationInfo();

        // AddrMatch
        $Merchant_EMV3DS["addrMatch"] = ($cart->id_address_invoice == $cart->id_address_delivery) ? "Y" : "N";

        $Merchant_EMV3DS["challengeWindowSize"] = 05;

        return $Merchant_EMV3DS;
    }



    public function getContent()
    {
        $errorMessage = '';
        if (!empty($_POST)) {
            $this->postValidation();
            if (!sizeof($this->postErrors)) {
                $errorMessage = $this->postProcess();
            } else {
                $errorMessage .=
                    '<div class="bootstrap"><div class="alert alert-danger"><strong>'
                    . $this->l('Error') . '</strong><ol>';
                foreach ($this->postErrors as $err) {
                    $errorMessage .= '<li>' . $err . '</li>';
                }
                $errorMessage .= '</ol></div></div>';
            }
        } else {
            $errorMessage = '';
        }

        $conf_values = $this->getConfigValues();

        if (Tools::isSubmit('id_cart')) {
            $this->validateOrder(
                Tools::getValue('id_cart'),
                _PS_OS_PAYMENT_,
                Tools::getValue('amount'),
                $this->displayName,
                null
            );
        }

        if (Tools::isSubmit('id_registro')) {
            ClassRegistro::remove(Tools::getValue('id_registro'));
        }

        $carritos = ClassRegistro::select();

        $id_currency = (int) (Configuration::get('PS_CURRENCY_DEFAULT'));
        $currency_array =   Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);

        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }

        $firstpurchase_scoring = Tools::getIsset("firstpurchase_scoring") ?
            Tools::getValue("firstpurchase_scoring") : $conf_values['PAYTPV_FIRSTPURCHASE_SCORING'];
        $firstpurchase_scoring_score = Tools::getIsset("firstpurchase_scoring_score") ?
            Tools::getValue("firstpurchase_scoring_score") : $conf_values['PAYTPV_FIRSTPURCHASE_SCORING_SCO'];

        $sessiontime_scoring = Tools::getIsset("sessiontime_scoring") ?
            Tools::getValue("sessiontime_scoring") : $conf_values['PAYTPV_SESSIONTIME_SCORING'];

        $sessiontime_scoring_val =
            Tools::getIsset("sessiontime_scoring_val") ?
            Tools::getValue("sessiontime_scoring_val") : $conf_values['PAYTPV_SESSIONTIME_SCORING_VAL'];

        $sessiontime_scoring_score = Tools::getIsset("sessiontime_scoring_score") ?
            Tools::getValue("sessiontime_scoring_score") : $conf_values['PAYTPV_SESSIONTIME_SCORING_SCORE'];

        $dcountry_scoring = Tools::getIsset("dcountry_scoring") ?
            Tools::getValue("dcountry_scoring") : $conf_values['PAYTPV_DCOUNTRY_SCORING'];

        $dcountry_scoring_val = Tools::getIsset("dcountry_scoring_val") ?
            implode(",", Tools::getValue("dcountry_scoring_val")) : $conf_values['PAYTPV_DCOUNTRY_SCORING_VAL'];

        $arr_dcountry_scoring_val = explode(",", $dcountry_scoring_val);

        $dcountry_scoring_score = Tools::getIsset("dcountry_scoring_score") ?
            Tools::getValue("dcountry_scoring_score") : $conf_values['PAYTPV_DCOUNTRY_SCORING_SCORE'];

        $ip_change_scoring = Tools::getIsset("ip_change_scoring") ?
            Tools::getValue("ip_change_scoring") : $conf_values['PAYTPV_IPCHANGE_SCORING'];

        $ip_change_scoring_score = Tools::getIsset("ip_change_scoring_score") ?
            Tools::getValue("ip_change_scoring_score") : $conf_values['PAYTPV_IPCHANGE_SCORING_SCORE'];

        $browser_scoring = Tools::getIsset("browser_scoring") ?
            Tools::getValue("browser_scoring") : $conf_values['PAYTPV_BROWSER_SCORING'];

        $browser_scoring_score = Tools::getIsset("browser_scoring_score") ?
            Tools::getValue("browser_scoring_score") : $conf_values['PAYTPV_BROWSER_SCORING_SCORE'];

        $so_scoring = Tools::getIsset("so_scoring") ?
            Tools::getValue("so_scoring") : $conf_values['PAYTPV_SO_SCORING'];
        $so_scoring_score = Tools::getIsset("so_scoring_score") ?
            Tools::getValue("so_scoring_score") : $conf_values['PAYTPV_SO_SCORING_SCORE'];


        $disableoffersavecard = Tools::getIsset("disableoffersavecard") ?
            Tools::getValue("disableoffersavecard") : $conf_values['PAYTPV_DISABLEOFFERSAVECARD'];

        $remembercardunselected = Tools::getIsset("remembercardunselected") ?
            Tools::getValue("remembercardunselected") : $conf_values['PAYTPV_REMEMBERCARDUNSELECTED'];



        $ssl = Configuration::get('PS_SSL_ENABLED');
        // Set the smarty env
        $this->context->smarty->assign('serverRequestUri', Tools::safeOutput($_SERVER['REQUEST_URI']));
        $this->context->smarty->assign('displayName', Tools::safeOutput($this->displayName));
        $this->context->smarty->assign('description', Tools::safeOutput($this->description));
        $this->context->smarty->assign('currentindex', AdminController::$currentIndex);
        $this->context->smarty->assign('token', Tools::getValue('token'));
        $this->context->smarty->assign('name', $this->name);
        $this->context->smarty->assign('reg_estado', $conf_values['PAYTPV_REG_ESTADO']);
        $this->context->smarty->assign('carritos', $carritos);
        $this->context->smarty->assign('errorMessage', $errorMessage);

        $this->context->smarty->assign(
            'integration',
            (Tools::getIsset("integration")) ? Tools::getValue("integration") : $conf_values['PAYTPV_INTEGRATION']
        );
        $this->context->smarty->assign(
            'clientcode',
            (Tools::getIsset("clientcode")) ? Tools::getValue("clientcode") : $conf_values['PAYTPV_CLIENTCODE']
        );

        $this->context->smarty->assign('terminales_paytpv', $this->obtenerTerminalesConfigurados($_POST));

        $this->context->smarty->assign(
            'commerce_password',
            (Tools::getIsset("commerce_password")) ?
                Tools::getValue("commerce_password") : $conf_values['PAYTPV_COMMERCEPASSWORD']
        );
        $this->context->smarty->assign(
            'newpage_payment',
            (Tools::getIsset("newpage_payment")) ?
                Tools::getValue("newpage_payment") : $conf_values['PAYTPV_NEWPAGEPAYMENT']
        );
        $this->context->smarty->assign(
            'suscriptions',
            (Tools::getIsset("suscriptions")) ? Tools::getValue("suscriptions") : $conf_values['PAYTPV_SUSCRIPTIONS']
        );
        $this->context->smarty->assign('currency_array', $currency_array);
        $this->context->smarty->assign('default_currency', $id_currency);
        $this->context->smarty->assign(
            'OK',
            Context::getContext()->link->getModuleLink($this->name, 'urlok', array(), $ssl)
        );
        $this->context->smarty->assign(
            'KO',
            Context::getContext()->link->getModuleLink($this->name, 'urlko', array(), $ssl)
        );
        $this->context->smarty->assign(
            'NOTIFICACION',
            Context::getContext()->link->getModuleLink($this->name, 'url', array(), $ssl)
        );
        $this->context->smarty->assign('base_dir', __PS_BASE_URI__);

        // Scoring Data.

        $this->context->smarty->assign('countries', $countries);

        $this->context->smarty->assign('firstpurchase_scoring', $firstpurchase_scoring);
        $this->context->smarty->assign('firstpurchase_scoring_score', $firstpurchase_scoring_score);
        $this->context->smarty->assign('sessiontime_scoring', $sessiontime_scoring);
        $this->context->smarty->assign('sessiontime_scoring_val', $sessiontime_scoring_val);
        $this->context->smarty->assign('sessiontime_scoring_score', $sessiontime_scoring_score);
        $this->context->smarty->assign('dcountry_scoring', $dcountry_scoring);
        $this->context->smarty->assign('arr_dcountry_scoring_val', $arr_dcountry_scoring_val);
        $this->context->smarty->assign('dcountry_scoring_score', $dcountry_scoring_score);
        $this->context->smarty->assign('ip_change_scoring', $ip_change_scoring);
        $this->context->smarty->assign('ip_change_scoring_score', $ip_change_scoring_score);
        $this->context->smarty->assign('browser_scoring', $browser_scoring);
        $this->context->smarty->assign('browser_scoring_score', $browser_scoring_score);
        $this->context->smarty->assign('so_scoring', $so_scoring);
        $this->context->smarty->assign('so_scoring_score', $so_scoring_score);

        $this->context->smarty->assign('disableoffersavecard', $disableoffersavecard);
        $this->context->smarty->assign('remembercardunselected', $remembercardunselected);


        $this->context->controller->addCSS($this->_path . 'views/css/admin.css', 'all');
        return $this->display(__FILE__, 'views/templates/admin.tpl');
    }


    public function obtenerTerminalesConfigurados($params)
    {
        if (array_key_exists("term", $params)) {
            $terminales = array();

            foreach (array_keys($params["term"]) as $key) {
                $terminales[$key]["idterminal"] = $params["term"][$key];
                $terminales[$key]["password"] = $params["pass"][$key];
                $terminales[$key]["jetid"] = $params["jetid"][$key];
                $terminales[$key]["idterminal_ns"] = $params["term_ns"][$key];
                $terminales[$key]["password_ns"] = $params["pass_ns"][$key];
                $terminales[$key]["jetid_ns"] = $params["jetid_ns"][$key];
                $terminales[$key]["terminales"] = $params["terminales"][$key];
                $terminales[$key]["tdfirst"] = $params["tdfirst"][$key];
                $terminales[$key]["tdmin"] = $params["tdmin"][$key];
                $terminales[$key]["currency_iso_code"] = $params["moneda"][$key];
            }
        } else {
            $terminales = PaytpvTerminal::getTerminals();
            if (sizeof($terminales) == 0) {
                $id_currency = (int) (Configuration::get('PS_CURRENCY_DEFAULT'));
                $currency = new Currency((int) ($id_currency));

                $terminales[0]["idterminal"] = "";
                $terminales[0]["password"] = "";
                $terminales[0]["jetid"] = "";
                $terminales[0]["idterminal_ns"] = "";
                $terminales[0]["password_ns"] = "";
                $terminales[0]["jetid_ns"] = "";
                $terminales[0]["terminales"] = 0;
                $terminales[0]["tdfirst"] = 1;
                $terminales[0]["tdmin"] = 0;
                $terminales[0]["currency_iso_code"] = $currency->iso_code;
            }
        }
        return $terminales;
    }

    public function hookDisplayShoppingCart()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/payment.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/fullscreen.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/paytpv.js');
    }



    public function hookDisplayPaymentTop($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/payment.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/fullscreen.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/paytpv.js');
    }

    public function hookDisplayPayment($params)
    {

        // Check New Page payment
        $newpage_payment = (int) (Configuration::get('PAYTPV_NEWPAGEPAYMENT'));
        $paytpv_integration = (int) (Configuration::get('PAYTPV_INTEGRATION'));

        $disableoffersavecard = Configuration::get('PAYTPV_DISABLEOFFERSAVECARD');
        $remembercardunselected = Configuration::get('PAYTPV_REMEMBERCARDUNSELECTED');

        $saved_card = PaytpvCustomer::getCardsCustomer((int) $this->context->customer->id);

        // Pago en nueva pagina dentro del comercio
        if ($newpage_payment == 1) {
            $this->context->smarty->assign('this_path', $this->_path);
            return $this->display(__FILE__, 'payment_newpage.tpl');
            // Pago en nueva pagina fullscreen si no tiene tarjetas almacenadas
        } elseif ($newpage_payment == 2 && empty($saved_card) && $disableoffersavecard) {
            $this->context->smarty->assign('this_path', $this->_path);
            $this->context->smarty->assign('paytpv_iframe', $this->paytpvIframeURL());
            return $this->display(__FILE__, 'payment_newpage2.tpl');
            // Pago integrado
        } else {
            $cart = Context::getContext()->cart;
            $datos_pedido = $this->terminalCurrency($cart);
            $idterminal = $datos_pedido["idterminal"];
            $idterminal_ns = $datos_pedido["idterminal_ns"];
            $jetid = $datos_pedido["jetid"];
            $jetid_ns = $datos_pedido["jetid_ns"];

            $importe_tienda = $cart->getOrderTotal(true, Cart::BOTH);

            if ($idterminal > 0) {
                $secure_pay = $this->isSecureTransaction($idterminal, $importe_tienda, 0) ? 1 : 0;
            } else {
                $secure_pay = $this->isSecureTransaction($idterminal_ns, $importe_tienda, 0) ? 1 : 0;
            }

            // Miramos a ver por que terminal enviamos la operacion
            if ($secure_pay) {
                $jetid_sel = $jetid;
            } else {
                $jetid_sel = $jetid_ns;
            }

            $this->context->smarty->assign('msg_paytpv', "");

            $msg_paytpv = "";

            $this->context->smarty->assign('msg_paytpv', $msg_paytpv);


            // Valor de compra
            // $id_currency = (int)(Configuration::get('PS_CURRENCY_DEFAULT'));

            // $currency = new Currency((int)($id_currency));

            // $importe = number_format($params['cart']->getOrderTotal(true, Cart::BOTH)*100, 0, '.', '');

            // $paytpv_order_ref = str_pad($params['cart']->id, 8, "0", STR_PAD_LEFT);
            $ssl = Configuration::get('PS_SSL_ENABLED');
            $values = array(
                'id_cart' => (int) $params['cart']->id,
                'key' => Context::getContext()->customer->secure_key
            );

            $active_suscriptions = (int) (Configuration::get('PAYTPV_SUSCRIPTIONS'));

            $index = 0;
            foreach ($saved_card as $key => $val) {
                $values_aux = array_merge($values, array("TOKEN_USER" => $val["TOKEN_USER"]));
                $saved_card[$key]['url'] =
                    Context::getContext()->link->getModuleLink($this->name, 'capture', $values_aux, $ssl);

                $index++;
            }
            $saved_card[$index]['url'] = 0;

            $tmpl_vars = array();
            $tmpl_vars['capture_url'] =
                Context::getContext()->link->getModuleLink($this->name, 'capture', $values, $ssl);

            $this->context->smarty->assign('active_suscriptions', $active_suscriptions);
            $this->context->smarty->assign('saved_card', $saved_card);
            $this->context->smarty->assign('commerce_password', $this->commerce_password);
            $this->context->smarty->assign('id_cart', $params['cart']->id);

            $this->context->smarty->assign('paytpv_iframe', $this->paytpvIframeURL());

            $this->context->smarty->assign('newpage_payment', $newpage_payment);
            $this->context->smarty->assign('paytpv_integration', $paytpv_integration);

            $this->context->smarty->assign('jet_id', $jetid_sel);

            $language_data = explode("-", $this->context->language->language_code);
            $language = $language_data[0];

            $this->context->smarty->assign('jet_paytpv', $this->jet_paytpv);
            $this->context->smarty->assign('jet_lang', $language);

            $this->context->smarty->assign(
                'paytpv_jetid_url',
                Context::getContext()->link->getModuleLink($this->name, 'capture', array(), $ssl)
            );

            $this->context->smarty->assign('disableoffersavecard', $disableoffersavecard);
            $this->context->smarty->assign('remembercardunselected', $remembercardunselected);


            $this->context->smarty->assign('base_dir', __PS_BASE_URI__);


            $tmpl_vars = array_merge(
                array(
                    'this_path' => $this->_path
                )
            );
            $this->context->smarty->assign($tmpl_vars);

            // Bankstore JET
            if ($paytpv_integration == 1) {
                $this->context->smarty->assign('js_code', $this->jsMinimizedJet());
            }

            return $this->display(__FILE__, 'payment_bsiframe.tpl');
        }
    }


    public function jsMinimizedJet()
    {
        include_once(_PS_MODULE_DIR_ . '/paytpv/lib/Minifier.php');

        $js_code = "function buildED() {
		    var t = document.getElementById('expiry_date').value,
		        n = t.substr(0, 2),
		        a = t.substr(3, 2);
		    $('[data-paytpv=\'dateMonth\']').val(n), $('[data-paytpv=\'dateYear\']').val(a)
		}

		(function() {
				(function() {
					var $,
                    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if
                        (i in this && this[i] === item) return i; } return -1; };

					$ = jQuery;

		$.fn.validateCreditCard = function(callback, options) {
            var bind, card, card_type, card_types, get_card_type, is_valid_length, is_valid_luhn, normalize, validate,
            validate_number, _i, _len, _ref;
	    	card_types = [
	      {
	        name: 'amex',
	        pattern: /^3[47]/,
	        valid_length: [15]
	      }, {
	        name: 'diners_club_carte_blanche',
	        pattern: /^30[0-5]/,
	        valid_length: [14]
	      }, {
	        name: 'diners_club_international',
	        pattern: /^36/,
	        valid_length: [14]
	      }, {
	        name: 'jcb',
	        pattern: /^35(2[89]|[3-8][0-9])/,
	        valid_length: [16]
	      }, {
	        name: 'laser',
	        pattern: /^(6304|670[69]|6771)/,
	        valid_length: [16, 17, 18, 19]
	      }, {
	        name: 'visa_electron',
	        pattern: /^(4026|417500|4508|4844|491(3|7))/,
	        valid_length: [16]
	      }, {
	        name: 'visa',
	        pattern: /^4/,
	        valid_length: [16]
	      }, {
	        name: 'mastercard',
	        // 20160603 2U7-GQS-M6X3 Cambiamos el patern ya que MC ha incluido nuevos rangos de bines
	        pattern: /^(5[1-5]|222|2[3-6]|27[0-1]|2720)/,
	        // 20160603 2U7-GQS-M6X3 Fin
	        valid_length: [16]
	      }, {
	        name: 'maestro',
	        pattern: /^(5018|5020|5038|6304|6759|676[1-3])/,
	        valid_length: [12, 13, 14, 15, 16, 17, 18, 19]
	      }, {
	        name: 'discover',
	        pattern: /^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,
	        valid_length: [16]
	      }
	    ];
	    bind = false;
	    if (callback) {
	      if (typeof callback === 'object') {
	        options = callback;
	        bind = false;
	        callback = null;
	      } else if (typeof callback === 'function') {
	        bind = true;
	      }
	    }
	    if (options === null) {
	      options = {};
	    }
	    if (options.accept === null) {
	      options.accept = (function() {
	        var _i, _len, _results;
	        _results = [];
	        for (_i = 0, _len = card_types.length; _i < _len; _i++) {
	          card = card_types[_i];
	          _results.push(card.name);
	        }
	        return _results;
	      })();
	    }
	    _ref = options.accept;
	    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
	      card_type = _ref[_i];
	      if (__indexOf.call((function() {
	        var _j, _len1, _results;
	        _results = [];
	        for (_j = 0, _len1 = card_types.length; _j < _len1; _j++) {
	          card = card_types[_j];
	          _results.push(card.name);
	        }
	        return _results;
	      })(), card_type) < 0) {
	        throw '" . $this->l('Credit Card Not Valid') . "';
	      }
	    }
	    get_card_type = function(number) {
	      var _j, _len1, _ref1;
	      _ref1 = (function() {
	        var _k, _len1, _ref1, _results;
	        _results = [];
	        for (_k = 0, _len1 = card_types.length; _k < _len1; _k++) {
	          card = card_types[_k];
	          if (_ref1 = card.name, __indexOf.call(options.accept, _ref1) >= 0) {
	            _results.push(card);
	          }
	        }
	        return _results;
	      })();
	      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
	        card_type = _ref1[_j];
	        if (number.match(card_type.pattern)) {
	          return card_type;
	        }
	      }
	      return null;
	    };
	    is_valid_luhn = function(number) {
	      var digit, n, sum, _j, _len1, _ref1;
	      sum = 0;
	      _ref1 = number.split('').reverse();
	      for (n = _j = 0, _len1 = _ref1.length; _j < _len1; n = ++_j) {
	        digit = _ref1[n];
	        digit = +digit;
	        if (n % 2) {
	          digit *= 2;
	          if (digit < 10) {
	            sum += digit;
	          } else {
	            sum += digit - 9;
	          }
	        } else {
	          sum += digit;
	        }
	      }
	      return sum % 10 === 0;
	    };
	    is_valid_length = function(number, card_type) {
	      var _ref1;
	      return _ref1 = number.length, __indexOf.call(card_type.valid_length, _ref1) >= 0;
	    };
	    validate_number = (function(_this) {
	      return function(number) {
	        var length_valid, luhn_valid;
	        card_type = get_card_type(number);
	        luhn_valid = false;
	        length_valid = false;
	        if (card_type !== null) {
	          luhn_valid = is_valid_luhn(number);
	          length_valid = is_valid_length(number, card_type);
	        }
	        return {
	          card_type: card_type,
	          valid: luhn_valid && length_valid,
	          luhn_valid: luhn_valid,
	          length_valid: length_valid
	        };
	      };
	    })(this);
	    validate = (function(_this) {
	      return function() {
	        var number;
	        number = normalize($(_this).val());
	        return validate_number(number);
	      };
	    })(this);
	    normalize = function(number) {
	      return number.replace(/[ -]/g, '');
	    };
	    if (!bind) {
	      return validate();
	    }
	    this.on('input.jccv', (function(_this) {
	      return function() {
	        $(_this).off('keyup.jccv');
	        return callback.call(_this, validate());
	      };
	    })(this));
	    this.on('keyup.jccv', (function(_this) {
	      return function() {
	        return callback.call(_this, validate());
	      };
	    })(this));
		    callback.call(this, validate());
		    return this;
		  };

		}).call(this);
			$(function() {
				return $('[data-paytpv=\'paNumber\']').validateCreditCard(function(result) {
		    	$(this).removeClass().addClass('paytpv_merchant_pan');
					if (result.card_type === null) {
						return;
					}
					$(this).addClass(result.card_type.name);
					if (result.valid) {
						return $(this).addClass('valid');
					} else {
						return $(this).removeClass('valid');
					}
				}, {
				accept: ['visa', 'visa_electron', 'mastercard', 'maestro', 'discover', 'amex']
				});
			});
		}).call(this);

		$(document).ready(function() {
			var oldLength = 0;
			$('#expiry_date').on('input',function(){
				var curLength = $(this).val().length;
				if(!$(this).val().match(/[/]/)) {
					if((curLength === 2) && (oldLength<curLength) ){
						var newInput = $(this).val();
						newInput += '/';
						$(this).val(newInput);
					}
				}
				oldLength = curLength;
			});
		})";
        return Minifier::minify($js_code);
    }

    public function getMerchantData($cart)
    {
        $resp = $cart;
        $resp = null;
        return $resp;

        // $MERCHANT_EMV3DS = $this->getEMV3DS($cart);
        // $SHOPPING_CART = $this->getShoppingCart($cart);

        // $datos = array_merge($MERCHANT_EMV3DS,$SHOPPING_CART);

        // return urlencode(base64_encode(json_encode($datos)));
    }

    public function paytpvIframeURL()
    {
        $cart = Context::getContext()->cart;

        // if not exist Cart -> Redirect to home
        if (!property_exists($cart, 'id') || $cart->id <= 0) {
            Tools::redirect('index');
        }

        $total_pedido = $cart->getOrderTotal(true, Cart::BOTH);

        $datos_pedido = $this->terminalCurrency($cart);
        $importe = $datos_pedido["importe"];
        $currency_iso_code = $datos_pedido["currency_iso_code"];
        $idterminal = $datos_pedido["idterminal"];
        $idterminal_ns = $datos_pedido["idterminal_ns"];
        $pass = $datos_pedido["password"];
        $pass_ns = $datos_pedido["password_ns"];


        $values = array(
            'id_cart' => $cart->id,
            'key' => Context::getContext()->customer->secure_key
        );


        $ssl = Configuration::get('PS_SSL_ENABLED');

        $URLOK = Context::getContext()->link->getModuleLink($this->name, 'urlok', $values, $ssl);
        $URLKO = Context::getContext()->link->getModuleLink($this->name, 'urlko', $values, $ssl);

        $paytpv_order_ref = str_pad($cart->id, 8, "0", STR_PAD_LEFT);

        if ($idterminal > 0) {
            $secure_pay = $this->isSecureTransaction($idterminal, $total_pedido, 0) ? 1 : 0;
        } else {
            $secure_pay = $this->isSecureTransaction($idterminal_ns, $total_pedido, 0) ? 1 : 0;
        }

        // Miramos a ver por que terminal enviamos la operacion
        if ($secure_pay) {
            $idterminal_sel = $idterminal;
            $pass_sel = $pass;
        } else {
            $idterminal_sel = $idterminal_ns;
            $pass_sel = $pass_ns;
        }


        $language_data = explode("-", $this->context->language->language_code);
        $language = $language_data[0];

        $score = $this->transactionScore($cart);
        $MERCHANT_SCORING = $score["score"];
        $MERCHANT_DATA = $this->getMerchantData($cart);


        $OPERATION = "1";
        // Cálculo Firma
        $signature = hash(
            'sha512',
            $this->clientcode . $idterminal_sel . $OPERATION . $paytpv_order_ref
            . $importe . $currency_iso_code . md5($pass_sel)
        );

        $fields = array(
            'MERCHANT_MERCHANTCODE' => $this->clientcode,
            'MERCHANT_TERMINAL' => $idterminal_sel,
            'OPERATION' => $OPERATION,
            'LANGUAGE' => $language,
            'MERCHANT_MERCHANTSIGNATURE' => $signature,
            'MERCHANT_ORDER' => $paytpv_order_ref,
            'MERCHANT_AMOUNT' => $importe,
            'MERCHANT_CURRENCY' => $currency_iso_code,
            'URLOK' => $URLOK,
            'URLKO' => $URLKO,
            '3DSECURE' => $secure_pay
        );

        if ($MERCHANT_SCORING != null) {
            $fields["MERCHANT_SCORING"] = $MERCHANT_SCORING;
        }
        if ($MERCHANT_DATA != null) {
            $fields["MERCHANT_DATA"] = $MERCHANT_DATA;
        }

        $query = http_build_query($fields);

        $vhash = hash('sha512', md5($query . md5($pass_sel)));

        $url_paytpv = $this->url_paytpv . "?" . $query . "&VHASH=" . $vhash;

        return $url_paytpv;
    }

    /**
     * return array Term,Currency,amount
     */
    public function terminalCurrency($cart)
    {

        // Si hay un terminal definido para la moneda del usuario devolvemos ese.
        $result = PaytpvTerminal::getterminalCurrency($this->context->currency->iso_code, $cart->id_shop);
        // Not exists terminal in user currency
        if (empty($result) === true) {
            // Search for terminal in merchant default currency
            $id_currency = (int) (Configuration::get('PS_CURRENCY_DEFAULT'));
            $currency = new Currency($id_currency);
            $result = PaytpvTerminal::getterminalCurrency($currency->iso_code, $cart->id_shop);

            // If not exists terminal in default currency. Select first terminal defined
            if (empty($result) === true) {
                $result = PaytpvTerminal::getFirstTerminal();
            }
        }
        $arrDatos = array();

        $arrDatos["idterminal"] = $result["idterminal"];
        $arrDatos["idterminal_ns"] = $result["idterminal_ns"];
        $arrDatos["password"] = $result["password"];
        $arrDatos["password_ns"] = $result["password_ns"];
        $arrDatos["jetid"] = $result["jetid"];
        $arrDatos["jetid_ns"] = $result["jetid_ns"];
        $arrDatos["currency_iso_code"] = $this->context->currency->iso_code;
        $arrDatos["importe"] = number_format($cart->getOrderTotal(true, Cart::BOTH) * 100, 0, '.', '');

        return $arrDatos;
    }


    public function isSecureTransaction($idterminal, $importe, $card)
    {
        
        $arrTerminal = PaytpvTerminal::getTerminalByIdTerminal($idterminal);

        $terminales = $arrTerminal["terminales"];
        $tdfirst = $arrTerminal["tdfirst"];
        $tdmin = $arrTerminal["tdmin"];
        // Transaccion Segura:

        // Si solo tiene Terminal Seguro
        if ($terminales == 0) {
            return true;
        }

        // Si esta definido que el pago es 3d secure y no estamos usando una tarjeta tokenizada
        if ($tdfirst && $card == 0) {
            return true;
        }

        // Si se supera el importe maximo para compra segura
        if ($terminales == 2 && ($tdmin > 0 && $tdmin < $importe)) {
            return true;
        }

        // Si esta definido como que la primera compra es Segura y es la primera compra aunque este tokenizada
        if ($terminales == 2 && $tdfirst && $card > 0 &&
            PaytpvOrder::isFirstPurchaseToken($this->context->customer->id, $card)
        ) {
            return true;
        }


        return false;
    }


    public function isSecurePay($importe)
    {
        // Terminal NO Seguro
        if ($this->terminales == 1) {
            return false;
        }
        // Ambos Terminales, Usar 3D False e Importe < Importe Min 3d secure
        if ($this->terminales == 2 && $this->tdfirst == 0 && ($this->tdmin == 0 || $importe <= $this->tdmin)) {
            return false;
        }
        return true;
    }


    public function hookDisplayPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        $this->context->smarty->assign(array(
            'this_path' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
        ));

        $id_order = Order::getOrderByCartId((int) ($params["objOrder"]->id_cart));
        $order = new Order($id_order);

        $this->context->smarty->assign('reference', $order->reference);
        $this->context->smarty->assign('base_dir', __PS_BASE_URI__);

        $this->html .= $this->display(__FILE__, 'payment_return.tpl');


        $result = PaytpvSuscription::getSuscriptionOrderPayments($id_order);
        if ($order->module == $this->name && !empty($result)) {
            $id_currency = $order->id_currency;
            $currency = new Currency((int) ($id_currency));

            $suscription_type = $this->l('This order is a Subscription');

            // $id_suscription = $result["id_suscription"];
            $id_customer = $result["id_customer"];
            $periodicity = $result["periodicity"];
            $cycles = ($result['cycles'] != 0) ? $result['cycles'] : $this->l('N');
            $status = $result["status"];
            // $date = $result["date"];
            $price = number_format($result['price'], 2, '.', '') . " " . $currency->sign;
            $num_pagos = $result['pagos'];

            if ($status == 0) {
                $status = $this->l('ACTIVE');
            } elseif ($status == 1) {
                $status = $this->l('CANCELLED');
            } elseif ($num_pagos == $result['cycles'] && $result['cycles'] > 0) {
                $status = $this->l('ENDED');
            }

            $language_data = explode("-", $this->context->language->language_code);
            $language = $language_data[0];

            $date_YYYYMMDD = ($language == "es") ?
                date("d-m-Y", strtotime($result['date'])) : date("Y-m-d", strtotime($result['date']));


            $this->context->smarty->assign('suscription_type', $suscription_type);
            $this->context->smarty->assign('id_customer', $id_customer);
            $this->context->smarty->assign('periodicity', $periodicity);
            $this->context->smarty->assign('cycles', $cycles);
            $this->context->smarty->assign('status', $status);
            $this->context->smarty->assign('date_yyyymmdd', $date_YYYYMMDD);
            $this->context->smarty->assign('price', $price);

            $this->html .= $this->display(__FILE__, 'order_suscription_customer_info.tpl');
        }


        return $this->html;
    }
    private function getConfigValues()
    {
        return Configuration::getMultiple(
            array(
                'PAYTPV_CLIENTCODE', 'PAYTPV_INTEGRATION', 'PAYTPV_COMMERCEPASSWORD', 'PAYTPV_NEWPAGEPAYMENT',
                'PAYTPV_SUSCRIPTIONS', 'PAYTPV_REG_ESTADO', 'PAYTPV_FIRSTPURCHASE_SCORING',
                'PAYTPV_FIRSTPURCHASE_SCORING_SCO', 'PAYTPV_SESSIONTIME_SCORING', 'PAYTPV_SESSIONTIME_SCORING_VAL',
                'PAYTPV_SESSIONTIME_SCORING_SCORE', 'PAYTPV_DCOUNTRY_SCORING', 'PAYTPV_DCOUNTRY_SCORING_VAL',
                'PAYTPV_DCOUNTRY_SCORING_SCORE', 'PAYTPV_IPCHANGE_SCORING', 'PAYTPV_IPCHANGE_SCORING_SCORE',
                'PAYTPV_BROWSER_SCORING', 'PAYTPV_BROWSER_SCORING_SCORE', 'PAYTPV_SO_SCORING',
                'PAYTPV_SO_SCORING_SCORE', 'PAYTPV_DISABLEOFFERSAVECARD', 'PAYTPV_REMEMBERCARDUNSELECTED'
            )
        );
    }

    public function saveCard($id_customer, $paytpv_iduser, $paytpv_tokenuser, $paytpv_cc, $paytpv_brand)
    {
        $paytpv_cc = '************' . Tools::substr($paytpv_cc, -4);


        PaytpvCustomer::addCustomer($paytpv_iduser, $paytpv_tokenuser, $paytpv_cc, $paytpv_brand, $id_customer);

        $result = array();

        $result["paytpv_iduser"] = $paytpv_iduser;
        $result["paytpv_tokenuser"] = $paytpv_tokenuser;

        return $result;
    }


    public function removeUser($paytpv_iduser, $paytpv_tokenuser)
    {
        $arrTerminal =
            PaytpvTerminal::getTerminalByCurrency($this->context->currency->iso_code, $this->context->shop->id);

        $idterminal = $arrTerminal["idterminal"];
        $idterminal_ns = $arrTerminal["idterminal_ns"];
        $pass = $arrTerminal["password"];
        $pass_ns = $arrTerminal["password_ns"];
        if ($idterminal > 0) {
            $idterminal_sel = $idterminal;
            $pass_sel = $pass;
        } else {
            $idterminal_sel = $idterminal_ns;
            $pass_sel = $pass_ns;
        }

        $client = new WSClient(
            array(
                'endpoint_paytpv' => $this->endpoint_paytpv,
                'clientcode' => $this->clientcode,
                'term' => $idterminal_sel,
                'pass' => $pass_sel
            )
        );

        $result = $client->removeUser($paytpv_iduser, $paytpv_tokenuser);
        return $result;
    }


    public function removeCard($paytpv_iduser)
    {
        $arrTerminal =
            PaytpvTerminal::getTerminalByCurrency($this->context->currency->iso_code, $this->context->shop->id);

        $idterminal = $arrTerminal["idterminal"];
        $idterminal_ns = $arrTerminal["idterminal_ns"];
        $pass = $arrTerminal["password"];
        $pass_ns = $arrTerminal["password_ns"];
        if ($idterminal > 0) {
            $idterminal_sel = $idterminal;
            $pass_sel = $pass;
        } else {
            $idterminal_sel = $idterminal_ns;
            $pass_sel = $pass_ns;
        }

        include_once(_PS_MODULE_DIR_ . '/paytpv/classes/WSClient.php');

        $client = new WSClient(
            array(
                'endpoint_paytpv' => $this->endpoint_paytpv,
                'clientcode' => $this->clientcode,
                'term' => $idterminal_sel,
                'pass' => $pass_sel
            )
        );

        $result = PaytpvCustomer::getCustomerIduser($paytpv_iduser);
        if (empty($result) === true) {
            return false;
        } else {
            $paytpv_iduser = $result["paytpv_iduser"];
            $paytpv_tokenuser = $result["paytpv_tokenuser"];

            $result = $client->removeUser($paytpv_iduser, $paytpv_tokenuser);
            PaytpvCustomer::removeCustomerIduser((int) $this->context->customer->id, $paytpv_iduser);


            return true;
        }
    }


    public function removeSuscription($id_suscription)
    {
        $arrTerminal =
            PaytpvTerminal::getTerminalByCurrency($this->context->currency->iso_code, $this->context->shop->id);

        $idterminal = $arrTerminal["idterminal"];
        $idterminal_ns = $arrTerminal["idterminal_ns"];
        $pass = $arrTerminal["password"];
        $pass_ns = $arrTerminal["password_ns"];
        if ($idterminal > 0) {
            $idterminal_sel = $idterminal;
            $pass_sel = $pass;
        } else {
            $idterminal_sel = $idterminal_ns;
            $pass_sel = $pass_ns;
        }

        include_once(_PS_MODULE_DIR_ . '/paytpv/classes/WSClient.php');

        $client = new WSClient(
            array(
                'endpoint_paytpv' => $this->endpoint_paytpv,
                'clientcode' => $this->clientcode,
                'term' => $idterminal_sel,
                'pass' => $pass_sel
            )
        );

        // Datos usuario

        $result = PaytpvSuscription::getSuscriptionId((int) $this->context->customer->id, $id_suscription);

        if (empty($result) === true) {
            return false;
        } else {
            $paytpv_iduser = $result["paytpv_iduser"];
            $paytpv_tokenuser = $result["paytpv_tokenuser"];

            $result = $client->removeSubscription($paytpv_iduser, $paytpv_tokenuser);

            if ((int) $result['DS_RESPONSE'] != 1 && $arrTerminal["idterminal_ns"] > 0) {
                $client = new WSClient(
                    array(
                        'endpoint_paytpv' => $this->endpoint_paytpv,
                        'clientcode' => $this->clientcode,
                        'term' => $arrTerminal["idterminal_ns"],
                        'pass' => $arrTerminal["password_ns"]
                    )
                );
                $result = $client->removeSubscription($paytpv_iduser, $paytpv_tokenuser);
            }

            if ((int) $result['DS_RESPONSE'] == 1) {
                PaytpvSuscription::removeSuscription((int) $this->context->customer->id, $id_suscription);

                return true;
            }
            return false;
        }
    }

    public function cancelSuscription($id_suscription)
    {
        $arrTerminal =
            PaytpvTerminal::getTerminalByCurrency($this->context->currency->iso_code, $this->context->shop->id);

        $idterminal = $arrTerminal["idterminal"];
        $idterminal_ns = $arrTerminal["idterminal_ns"];
        $pass = $arrTerminal["password"];
        $pass_ns = $arrTerminal["password_ns"];
        if ($idterminal > 0) {
            $idterminal_sel = $idterminal;
            $pass_sel = $pass;
        } else {
            $idterminal_sel = $idterminal_ns;
            $pass_sel = $pass_ns;
        }

        include_once(_PS_MODULE_DIR_ . '/paytpv/classes/WSClient.php');

        $client = new WSClient(
            array(
                'endpoint_paytpv' => $this->endpoint_paytpv,
                'clientcode' => $this->clientcode,
                'term' => $idterminal_sel,
                'pass' => $pass_sel
            )
        );
        // Datos usuario
        $result = PaytpvSuscription::getSuscriptionId((int) $this->context->customer->id, $id_suscription);
        if (empty($result) === true) {
            return false;
        } else {
            $paytpv_iduser = $result["paytpv_iduser"];
            $paytpv_tokenuser = $result["paytpv_tokenuser"];

            $result = $client->removeSubscription($paytpv_iduser, $paytpv_tokenuser);
            if ((int) $result['DS_RESPONSE'] != 1 && $arrTerminal["idterminal_ns"] > 0) {
                $client = new WSClient(
                    array(
                        'endpoint_paytpv' => $this->endpoint_paytpv,
                        'clientcode' => $this->clientcode,
                        'term' => $arrTerminal["idterminal_ns"],
                        'pass' => $arrTerminal["password_ns"]
                    )
                );
                $result = $client->removeSubscription($paytpv_iduser, $paytpv_tokenuser);
            }
            $response = array();

            if ((int) $result['DS_RESPONSE'] == 1) {
                PaytpvSuscription::cancelSuscription((int) $this->context->customer->id, $id_suscription);
                $response["error"] = 0;
            } else {
                $response["error"] = 1;
            }
            return $response;
        }
    }

    public function validPassword($id_customer, $passwd)
    {
        $sql = 'select * from ' . _DB_PREFIX_ . 'customer where id_customer = ' . pSQL($id_customer) . ' and passwd="' .
            md5(pSQL(_COOKIE_KEY_ . $passwd)) . '"';
        $result = Db::getInstance()->getRow($sql);
        return (empty($result) === true) ? false : true;
    }


    /*
        Refund
    */

    public function hookActionProductCancel($params)
    {
        if (Tools::isSubmit('generateDiscount')) {
            return false;
        } elseif ($params['order']->module != $this->name || !($order = $params['order']) ||
            !Validate::isLoadedObject($order)
        ) {
            return false;
        } elseif (!$order->hasBeenPaid()) {
            return false;
        }

        $order_detail = new OrderDetail((int) $params['id_order_detail']);
        if (!$order_detail || !Validate::isLoadedObject($order_detail)) {
            return false;
        }

        $paytpv_order = PaytpvOrder::getOrder((int) $order->id);
        if (empty($paytpv_order)) {
            die('error');
            // return false;
        }

        $paytpv_date = date("Ymd", strtotime($paytpv_order['date']));
        $paytpv_iduser = $paytpv_order["paytpv_iduser"];
        $paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

        $id_currency = $order->id_currency;
        $currency = new Currency((int) ($id_currency));

        $orderPayment = $order->getOrderPaymentCollection()->getFirst();
        $authcode = $orderPayment->transaction_id;

        $products = $order->getProducts();
        $cancel_quantity = Tools::getValue('cancelQuantity');

        $amt = (float) ($products[(int) $order_detail->id]['product_price_wt'] *
            (int) $cancel_quantity[(int) $order_detail->id]);

        $amount = number_format($amt * 100, 0, '.', '');

        $paytpv_order_ref = str_pad((int) $order->id_cart, 8, "0", STR_PAD_LEFT);

        $response = $this->makeRefund(
            $params['order'],
            $paytpv_iduser,
            $paytpv_tokenuser,
            $order->id,
            $paytpv_order_ref,
            $paytpv_date,
            $currency->iso_code,
            $authcode,
            $amount,
            1
        );
        $refund_txt = $response["txt"];

        $message = $this->l('PAYCOMET Refund ') .  ", " . $amt . " " . $currency->sign . " [" . $refund_txt . "]" .
            '<br>';
        $this->addNewPrivateMessage((int) $order->id, $message);
    }

    private function makeRefund(
        $order,
        $paytpv_iduser,
        $paytpv_tokenuser,
        $order_id,
        $paytpv_order_ref,
        $paytpv_date,
        $currency_iso_code,
        $authcode,
        $amount,
        $type
    ) {
        $arrTerminal = PaytpvTerminal::getTerminalByCurrency($currency_iso_code, $order->id_shop);

        // Refund amount
        include_once(_PS_MODULE_DIR_ . '/paytpv/classes/WSClient.php');
        $client = new WSClient(
            array(
                'endpoint_paytpv' => $this->endpoint_paytpv,
                'clientcode' => Configuration::get('PAYTPV_CLIENTCODE', null, null, $order->id_shop),
                'term' => $arrTerminal["idterminal"],
                'pass' => $arrTerminal["password"]
            )
        );

        // Refund amount of transaction
        $result = $client->executeRefund(
            $paytpv_iduser,
            $paytpv_tokenuser,
            $paytpv_order_ref,
            $currency_iso_code,
            $authcode,
            $amount
        );

        // $refund_txt = $this->l('OK');
        $response = array();

        $response["error"] = 0;
        $response["txt"] = $this->l('OK');

        // If idterminal_ns is not null make refund by other terminal
        if ($result['DS_ERROR_ID'] == 130 && $arrTerminal["idterminal_ns"] > 0) {
            $client = new WSClient(
                array(
                    'endpoint_paytpv' => $this->endpoint_paytpv,
                    'clientcode' => $this->clientcode,
                    'term' => $arrTerminal["idterminal_ns"],
                    'pass' => $arrTerminal["password_ns"]
                )
            );

            $result = $client->executeRefund(
                $paytpv_iduser,
                $paytpv_tokenuser,
                $paytpv_order_ref,
                $currency_iso_code,
                $authcode,
                $amount
            );
            // $refund_txt = $this->l('OK');
            $response["error"] = 0;
            $response["txt"] = $this->l('OK');
        }

        // If is a subscription and error y initial refund.
        if ($result['DS_ERROR_ID'] == 130) {
            $paytpv_order_ref .= "[" . $paytpv_iduser . "]" . $paytpv_date;
            // Refund amount of transaction
            $result = $client->executeRefund(
                $paytpv_iduser,
                $paytpv_tokenuser,
                $paytpv_order_ref,
                $currency_iso_code,
                $authcode,
                $amount
            );
            // $refund_txt = $this->l('OK');
            $response["error"] = 0;
            $response["txt"] = $this->l('OK');
        }

        if ((int) $result['DS_RESPONSE'] != 1) {
            $response["txt"] = $this->l('ERROR') . " " . $result['DS_ERROR_ID'];
            $response["error"] = 1;
        } else {
            $amount = number_format($amount / 100, 2, '.', '');
            PaytpvRefund::addRefund($order_id, $amount, $type);
        }
        return $response;
    }

    public function addNewPrivateMessage($id_order, $message)
    {
        if (!(bool) $id_order) {
            return false;
        }

        $new_message = new Message();
        $message = strip_tags($message, '<br>');

        if (!Validate::isCleanHtml($message)) {
            $message = $this->l('Payments messages are invalid, please check the module.');
        }

        $new_message->message = $message;
        $new_message->id_order = (int) $id_order;
        $new_message->private = 1;

        return $new_message->add();
    }

    /*

    Datos cuenta
    */

    public function hookDisplayCustomerAccount($params)
    {
        // If not disableoffersavecard
        if (!$this->disableoffersavecard == 1) {
            $this->smarty->assign('in_footer', false);
            return $this->display(__FILE__, 'my-account.tpl');
        }
    }


    /*

    Datos cuenta
    */

    public function hookDisplayAdminOrder($params)
    {
        if (Tools::isSubmit('submitPayTpvRefund')) {
            $this->doTotalRefund($params['id_order']);
        }

        if (Tools::isSubmit('submitPayTpvPartialRefund')) {
            $this->doPartialRefund($params['id_order']);
        }

        $order = new Order((int) $params['id_order']);
        $result = PaytpvSuscription::getSuscriptionOrderPayments($params["id_order"]);
        if ($order->module == $this->name && !empty($result)) {
            $id_currency = $order->id_currency;
            $currency = new Currency((int) ($id_currency));


            $suscription = $result["suscription"];
            if ($suscription == 1) {
                $suscription_type = $this->l('This order is a Subscription');
            } else {
                $suscription_type = $this->l('This order is a payment for Subscription');
            }
            // $id_suscription = $result["id_suscription"];
            $id_customer = $result["id_customer"];
            $periodicity = $result["periodicity"];
            $cycles = ($result['cycles'] != 0) ? $result['cycles'] : $this->l('N');
            $status = $result["status"];
            // $date = $result["date"];
            $price = number_format($result['price'], 2, '.', '') . " " . $currency->sign;
            $num_pagos = $result['pagos'];

            if ($status == 0) {
                $status = $this->l('ACTIVE');
            } elseif ($status == 1) {
                $status = $this->l('CANCELLED');
            } elseif ($num_pagos == $result['cycles'] && $result['cycles'] > 0) {
                $status = $this->l('ENDED');
            }

            $language_data = explode("-", $this->context->language->language_code);
            $language = $language_data[0];

            $date_YYYYMMDD = ($language == "es") ?
                date("d-m-Y", strtotime($result['date'])) : date("Y-m-d", strtotime($result['date']));


            $this->context->smarty->assign('suscription_type', $suscription_type);
            $this->context->smarty->assign('id_customer', $id_customer);
            $this->context->smarty->assign('periodicity', $periodicity);
            $this->context->smarty->assign('cycles', $cycles);
            $this->context->smarty->assign('status', $status);
            $this->context->smarty->assign('date_yyyymmdd', $date_YYYYMMDD);
            $this->context->smarty->assign('price', $price);

            $this->html .= $this->display(__FILE__, 'order_suscription_info.tpl');
        }

        // Total Refund Template
        if ($order->module == $this->name && $this->canRefund($order->id)) {
            if (version_compare(_PS_VERSION_, '1.5', '>=')) {
                $order_state = $order->current_state;
            } else {
                $order_state = OrderHistory::getLastOrderState($order->id);
            }

            $total_amount = $order->total_paid;

            $amount_returned =  PaytpvRefund::getTotalRefund($order->id);
            $amount_returned = number_format($amount_returned, 2, '.', '');


            $total_pending = $total_amount - $amount_returned;
            $total_pending =  number_format($total_pending, 2, '.', '');

            $currency = new Currency((int) $order->id_currency);

            $amt_sign = $total_pending . " " . $currency->sign;

            $error_msg = "";
            if (Tools::getValue('paytpPartialRefundAmount')) {
                $amt_refund = str_replace(",", ".", Tools::getValue('paytpPartialRefundAmount'));
                if (is_numeric($amt_refund)) {
                    $amt_refund = number_format($amt_refund, 2, '.', '');
                }

                if (Tools::getValue('paytpPartialRefundAmount') && ($amt_refund > $total_pending || $amt_refund == "" ||
                    !is_numeric($amt_refund))) {
                    $error_msg =
                        Tools::displayError($this->l('The partial amount should be less than the outstanding amount'));
                }
            }

            $arrRefunds = array();
            if ($amount_returned > 0) {
                $arrRefunds = PaytpvRefund::getRefund($order->id);
            }


            $this->context->smarty->assign(
                array(
                    'base_url' => _PS_BASE_URL_ . __PS_BASE_URI__,
                    'module_name' => $this->name,
                    'order_state' => $order_state,
                    'params' => $params,
                    'total_amount' => $total_amount,
                    'amount_returned' => $amount_returned,
                    'arrRefunds' => $arrRefunds,
                    'amount' => $amt_sign,
                    'sign' => $currency->sign,
                    'error_msg' => $error_msg,
                    'ps_version' => _PS_VERSION_
                )
            );



            $template_refund = 'views/templates/admin/admin_order/refund.tpl';
            $this->html .=  $this->display(__FILE__, $template_refund);
            $this->postProcess();
        }

        return $this->html;
    }

    private function doPartialRefund($id_order)
    {
        $paytpv_order = PaytpvOrder::getOrder((int) $id_order);
        if (empty($paytpv_order)) {
            return false;
        }

        $order = new Order((int) $id_order);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        // $products = $order->getProducts();
        $currency = new Currency((int) $order->id_currency);
        if (!Validate::isLoadedObject($currency)) {
            $this->_errors[] = $this->l('Invalid Currency');
        }

        if (count($this->_errors)) {
            return false;
        }

        // $decimals = (is_array($currency) ? (int)$currency['decimals'] : (int)$currency->decimals) *
        // _PS_PRICE_DISPLAY_PRECISION_;

        $total_amount = $order->total_paid;

        $total_pending = $total_amount - PaytpvRefund::getTotalRefund($order->id);
        $total_pending =  number_format($total_pending, 2, '.', '');

        $amt_refund  = str_replace(",", ".", Tools::getValue('paytpPartialRefundAmount'));
        if (is_numeric($amt_refund)) {
            $amt_refund = number_format($amt_refund, 2, '.', '');
        }

        if ($amt_refund > $total_pending || $amt_refund == "" || !is_numeric($amt_refund)) {
            $this->errors[] =
                Tools::displayError($this->l('The partial amount should be less than the outstanding amount'));
        } else {
            $amt = $amt_refund;

            $paytpv_date = date("Ymd", strtotime($paytpv_order['date']));
            $paytpv_iduser = $paytpv_order["paytpv_iduser"];
            $paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

            $id_currency = $order->id_currency;
            $currency = new Currency((int) ($id_currency));

            $orderPayment = $order->getOrderPaymentCollection()->getFirst();
            $authcode = $orderPayment->transaction_id;

            $amount = number_format($amt * 100, 0, '.', '');

            $paytpv_order_ref = str_pad((int) $order->id_cart, 8, "0", STR_PAD_LEFT);

            $response = $this->makeRefund(
                $order,
                $paytpv_iduser,
                $paytpv_tokenuser,
                $order->id,
                $paytpv_order_ref,
                $paytpv_date,
                $currency->iso_code,
                $authcode,
                $amount,
                1
            );
            $refund_txt = $response["txt"];
            $message = $this->l('PAYCOMET Refund ') .  ", " . $amt . " " . $currency->sign . " [" . $refund_txt
                . "]" .  '<br>';

            $this->addNewPrivateMessage((int) $id_order, $message);

            Tools::redirect($_SERVER['HTTP_REFERER']);
        }
    }

    private function doTotalRefund($id_order)
    {
        $paytpv_order = PaytpvOrder::getOrder((int) $id_order);
        if (empty($paytpv_order)) {
            return false;
        }

        $order = new Order((int) $id_order);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        // $products = $order->getProducts();
        $currency = new Currency((int) $order->id_currency);
        if (!Validate::isLoadedObject($currency)) {
            $this->_errors[] = $this->l('Invalid Currency');
        }

        if (count($this->_errors)) {
            return false;
        }

        // $decimals = (is_array($currency) ? (int)$currency['decimals'] : (int)$currency->decimals) *
        // _PS_PRICE_DISPLAY_PRECISION_;

        $total_amount = $order->total_paid;

        $total_pending = $total_amount - PaytpvRefund::getTotalRefund($order->id);
        $total_pending =  number_format($total_pending, 2, '.', '');

        $paytpv_date = date("Ymd", strtotime($paytpv_order['date']));
        $paytpv_iduser = $paytpv_order["paytpv_iduser"];
        $paytpv_tokenuser = $paytpv_order["paytpv_tokenuser"];

        $id_currency = $order->id_currency;
        $currency = new Currency((int) ($id_currency));

        $orderPayment = $order->getOrderPaymentCollection()->getFirst();
        $authcode = $orderPayment->transaction_id;

        // $products = $order->getProducts();
        // $cancel_quantity = Tools::getValue('cancelQuantity');

        $amount = number_format($total_pending * 100, 0, '.', '');

        $paytpv_order_ref = str_pad((int) $order->id_cart, 8, "0", STR_PAD_LEFT);


        $response = $this->makeRefund(
            $order,
            $paytpv_iduser,
            $paytpv_tokenuser,
            $order->id,
            $paytpv_order_ref,
            $paytpv_date,
            $currency->iso_code,
            $authcode,
            $amount,
            0
        );
        $refund_txt = $response["txt"];
        $message = $this->l('PAYCOMET Total Refund ') .  ", " . $total_pending . " " . $currency->sign . " ["
            . $refund_txt . "]" .  '<br>';
        if ($response['error'] == 0) {
            if (!PaytpvOrder::setOrderRefunded($id_order)) {
                die(Tools::displayError('Error when updating PAYCOMET database'));
            }

            $history = new OrderHistory();
            $history->id_order = (int) $id_order;
            $history->changeIdOrderState((int) Configuration::get('PS_OS_REFUND'), $history->id_order);
            $history->addWithemail();
            $history->save();
        }

        $this->addNewPrivateMessage((int) $id_order, $message);

        Tools::redirect($_SERVER['HTTP_REFERER']);
    }


    private function canRefund($id_order)
    {
        if (!(bool) $id_order) {
            return false;
        }

        $paytpv_order = PaytpvOrder::getOrder((int) $id_order);

        return $paytpv_order; //&& $paytpv_order['payment_status'] != 'Refunded';
    }
}
