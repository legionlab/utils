<?php

namespace LegionLab\Utils;

use LegionLab\Troubadour\Settings;
use LegionLab\Troubadour\Development\Log;

class PayPalAPI
{
    private $sandbox = true;
    private $url = '', $returnlink = '', $cancellink = '', $token = '', $payer = '';

    public function __construct()
    {
        $this->sandbox = (Settings::get('deployment')) ? true : false;

        if ($this->sandbox)
            $this->url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        else
            $this->url = 'https://www.paypal.com/cgi-bin/webscr';
    }

    /**
     * Envia uma requisição NVP para uma API PayPal.
     *
     * @param array $requestNvp Define os campos da requisição.
     *
     * @return array Campos retornados pela operação da API. O array de retorno poderá
     *               ser vazio, caso a operação não seja bem sucedida. Nesse caso, os
     *               logs de erro deverão ser verificados.
     */
    private function sendNvpRequest(array $requestNvp)
    {
        $apiEndpoint  = 'https://api-3t.' . ($this->sandbox? 'sandbox.': null);
        $apiEndpoint .= 'paypal.com/nvp';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestNvp));

        $response = urldecode(curl_exec($curl));
        curl_close($curl);

        $responseNvp = array();
        if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches))
            foreach ($matches['name'] as $offset => $name)
                $responseNvp[$name] = $matches['value'][$offset];

        if (isset($responseNvp['ACK']) and mb_strtoupper($responseNvp['ACK']) != 'SUCCESS' and mb_strtoupper($responseNvp['ACK']) != 'SUCCESSWITHWARNING') {
            for ($i = 0; isset($responseNvp['L_ERRORCODE' . $i]); ++$i) {
                $message = sprintf(   "PayPal NVP %s[%d]: %s\n",
                    $responseNvp['L_SEVERITYCODE' . $i],
                    $responseNvp['L_ERRORCODE' . $i],
                    $responseNvp['L_LONGMESSAGE' . $i]
                );
                Log::register($message,'paypal_errors');
            }

            return $responseNvp['L_ERRORCODE0'];
        }
        else {
            if(isset($responseNvp['PAYERID']))
                $this->payer = $responseNvp['PAYERID'];
            if(isset($responseNvp['TOKEN']))
                $this->token = $responseNvp['TOKEN'];
            return $responseNvp;
        }

    }

    /**
     * @return string
     */
    public function currentPayer()
    {
        return $this->payer;
    }

    /**
     * @return string
     */
    public function currentToken()
    {
        return $this->token;
    }

    public function buyLink()
    {
        return $this->url.'?cmd=_express-checkout&token='.$this->token.'';
    }

    /**
     * @return string
     */
    public function getReturnlink()
    {
        return $this->returnlink;
    }

    /**
     * @param string $returnlink
     * @return $this
     */
    public function setReturnlink($returnlink)
    {
        $this->returnlink = $returnlink;
        return $this;
    }

    /**
     * @return string
     */
    public function getCancellink()
    {
        return $this->cancellink;
    }

    /**
     * @param string $cancellink
     * @return $this
     */
    public function setCancellink($cancellink)
    {
        $this->cancellink = $cancellink;
        return $this;
    }



    /**
     * Tenta realizar uma requisição de compra, se concluir irá para o site do paypal
     * na página de realizar a compra, se não retorna falso.
     *
     * @param $sale - ID da compra
     * @param $itens - array com nome, descricao, preço e quantidade dos artigos comprados.
     * Exemplo:
     * array
     * (
     *      0 => array('name' => 'item1', 'description' => 'descrva aqui', 'price' => 10.99, 'quantity' => 1),
     *      1 => array('name' => 'item2', 'description' => 'descrva aqui', 'price' => 99.99, 'quantity' => 2)
     * )
     * @return bool
     */
    function setExpressCheckout($sale, $itens)
    {
        $redirectURL = $this->dispenser($sale, $itens, 'SetExpressCheckout');
        return $this->sendNvpRequest($redirectURL);
    }

    private function dispenser($sale, $itens, $method)
    {
        $redirectURL = $this->credentials($method);
        if(is_array($itens)) {
            $cont = 0;
            $totally = 0;
            foreach ($itens as $item)
                $totally += ($item['price'] * $item['quantity']);


            $redirectURL['PAYMENTREQUEST_0_PAYMENTACTION'] = 'SALE';
            $redirectURL['PAYMENTREQUEST_0_AMT'] = $totally;
            $redirectURL['PAYMENTREQUEST_0_CURRENCYCODE'] = 'BRL';
            $redirectURL['PAYMENTREQUEST_0_ITEMAMT'] = $totally;
            $redirectURL['PAYMENTREQUEST_0_INVNUM'] = $sale;

            foreach ($itens as $item) {
                $redirectURL['L_PAYMENTREQUEST_0_NAME'.$cont] = $item['name'];
                $redirectURL['L_PAYMENTREQUEST_0_DESC'.$cont] = $item['description'];
                $redirectURL['L_PAYMENTREQUEST_0_AMT'.$cont] =  $item['price'];
                $redirectURL['L_PAYMENTREQUEST_0_QTY'.$cont] = $item['quantity'];
                $totally += ($item['price'] * $item['quantity']);
                $cont ++;
            }

            $redirectURL['RETURNURL'] = $this->returnlink;
            $redirectURL['CANCELURL'] = $this->cancellink;
        }
        return $redirectURL;
    }

    function doExpressCheckoutPayment($sale, $itens, $payerid, $token)
    {
        $redirectURL = $this->dispenser($sale, $itens, 'DoExpressCheckoutPayment');
        $redirectURL['TOKEN'] = $token;
        $redirectURL['PAYERID'] = $payerid;

        if(!empty($payerid))
            return $this->sendNvpRequest($redirectURL);
        else
            return false;
    }

    public function isPay($do)
    {
        if(isset($do['PAYMENTINFO_0_PAYMENTSTATUS']) and mb_strtolower($do['PAYMENTINFO_0_PAYMENTSTATUS']) == 'completed')
            return true;
        else
            return false;
    }

    function getExpressCheckoutDetails($token)
    {
        $redirectURL = $this->credentials('GetExpressCheckoutDetails');
        $redirectURL['TOKEN'] = $token;

        $redirectURL['RETURNURL'] = 'http://PayPalPartner.com.br/VendeFrete?return=1';
        $redirectURL['CANCELURL'] = 'http://PayPalPartner.com.br/CancelaFrete';
        $redirectURL['BUTTONSOURCE'] = 'BR_EC_EMPRESA';

        return $this->sendNvpRequest($redirectURL);
    }

    private function credentials($method)
    {
        if ($this->sandbox) {
            $user = Settings::get("paypal_s_user");
            $pswd = Settings::get("paypal_s_password");
            $signature = Settings::get("paypal_s_signature");
        }
        else {
            $user = Settings::get("paypal_p_user");
            $pswd = Settings::get("paypal_p_password");
            $signature = Settings::get("paypal_p_signature");
        }

        return  array(
            'USER' => $user,
            'PWD' => $pswd,
            'SIGNATURE' => $signature,

            'VERSION' => '108.0',
            'METHOD'=> $method);
    }
}
