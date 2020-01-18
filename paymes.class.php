<?php
class paymes {

    private $payment_url = 'https://web.paym.es/api/authorize';
    private
        $secret,
        $buyer = [],
        $currency = 'TL';

    public function __construct($secret) {

        $this->secret = $secret;

    }

    public function setBuyer(array $fields = []) {

        $this->buyerValidateAndLoad($this->buyerFields(), $fields);

    }

    public function setOrderBilling(array $fields = []) {

        $this->buyerValidateAndLoad($this->orderBillingFields(), $fields);

    }

    public function setOrderPayment(array $fields = []) {

        $this->buyerValidateAndLoad($this->orderPaymentFields(), $fields);

    }

    public function setOrderDelivery(array $fields = []) {

        $this->buyerValidateAndLoad($this->orderDeliveryFields(), $fields);

    }

    public function getIP() {

        if(getenv("HTTP_CLIENT_IP")) {

            $ip = getenv("HTTP_CLIENT_IP");

        } elseif(getenv("HTTP_X_FORWARDED_FOR")) {

            $ip = getenv("HTTP_X_FORWARDED_FOR");

            if (strstr($ip, ',')) {

                $tmp = explode (',', $ip);
                $ip = trim($tmp[0]);

            }

        } else {

            $ip = getenv("REMOTE_ADDR");

        }

        return $ip;

    }

    private function buyerValidateAndLoad($validationFields, $fields) {

        $diff = array_diff_key($validationFields, $fields);

        if (count($diff) > 0)
            throw new Exception(implode(',', array_keys($diff)) . ' alanlar zorunludur');

        foreach ($validationFields as $key => $buyerField) {

            $this->buyer[$key] = $fields[$key];

        }

    }

    public function run($order_total) {

        $diff = array_diff_key($this->buyerFields(), $this->buyer);

        if (count($diff) > 0)
            throw new Exception(implode(',', array_keys($diff)) . ' alanların doldurulması zorunludur, lütfen "setBuyer()" yöntemini kullanın ');

        $diff = array_diff_key($this->orderBillingFields(), $this->buyer);

        if (count($diff) > 0)
            throw new Exception(implode(',', array_keys($diff)) . ' alanların doldurulması zorunludur, lütfen "setOrderBilling()" yöntemini kullanın ');

        $diff = array_diff_key($this->orderDeliveryFields(), $this->buyer);

        if (count($diff) > 0)
            throw new Exception(implode(',', array_keys($diff)) . ' alanların doldurulması zorunludur, lütfen "setOrderDelivery()" yöntemini kullanın ');

        $diff = array_diff_key($this->orderPaymentFields(), $this->buyer);

        if (count($diff) > 0)
            throw new Exception(implode(',', array_keys($diff)) . ' alanların doldurulması zorunludur, lütfen "setOrderPayment()" yöntemini kullanın ');

        $params = [
            "secret" => $this->secret,
            "productName" => $this->buyer['product_name'],
            "comment" => $this->buyer['product_comment'],
            "billingFirstname" => $this->buyer['billing_firstname'],
            "billingLastname" => $this->buyer['billing_lastname'],
            "billingEmail" => $this->buyer['email'],
            "billingPhone" => $this->buyer['phone'],
            "billingAddressline1" => $this->buyer['billing_address'],
            "billingCity" => $this->buyer['billing_city'],
            "billingCountrycode" => $this->buyer['billing_country'],
            "deliveryFirstname" => $this->buyer['delivery_firstname'],
            "deliveryLastname" => $this->buyer['delivery_lastname'],
            "deliveryCity" => $this->buyer['delivery_city'],
            "currency" => "TL",
            "owner" => $this->buyer['card_owner'],
            "number" => $this->buyer['card_number'],
            "expiryMonth" => $this->buyer['card_month'],
            "expiryYear" => $this->buyer['card_year'],
            "cvv" => $this->buyer['card_cvv'],
            "clientIp" => $this->getIP(),
            "installmentsNumber" => "1",
            "operationId" => $this->buyer['id'],
            "productPrice" => $order_total,
            "productQuantity" => "1",
            "productSku" => "1"
        ];

        $postData = '';

        foreach($params as $k => $v) {

            $postData .= $k . '='.$v.'&';

        }

        rtrim($postData, '&');

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $this->payment_url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, $postData);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        curl_close($ch);

        $decode = json_decode($output, true);

        return header('Refresh: 0; URL =' . $decode['paymentResult']['url']);

    }

    private function buyerFields() {

        return [
            'id' => true,
            'billing_firstname' => true,
            'billing_lastname' => true,
            'email' => true,
            'phone' => true,
        ];

    }

    private function orderPaymentFields() {

        return [
            'product_name' => true,
            'product_comment' => true,
            'card_owner' => true,
            'card_number' => true,
            'card_month' => true,
            'card_year' => true,
            'card_cvv' => true,
        ];

    }

    private function orderBillingFields() {

        return [
            'billing_address' => true,
            'billing_city' => true,
            'billing_country' => true,
        ];

    }

    private function orderDeliveryFields() {

        return [
            'delivery_firstname' => true,
            'delivery_lastname' => true,
            'delivery_city' => true,
        ];

    }

}
?>
