# Paymes Api
Yazdığınız özel yazılımlarınıza Paymes ile Ödeme Alma metodu ekleyebilirsiniz.

Paymes Api Entegrasyonu ile çok kolay bir şekilde entegrasyon sağlayın.

```php
require_once 'paymes.class.php';
$paymes = new paymes('Secret Key');
$paymes->setBuyer([
    'id' => 1,
    'billing_firstname' => 'İsim',
    'billing_lastname' => 'Soyisim',
    'email' => 'E-Posta Adresi',
    'phone' => '05555555555'
]);
$paymes->setOrderBilling([
   'billing_address' => 'Adres',
   'billing_city' => 'Şehir',
   'billing_country' => 'TR'
]);
$paymes->setOrderDelivery([
    'delivery_firstname' => 'İsim',
    'delivery_lastname' => 'Soyisim',
    'delivery_city' => 'Şehir'
]);
$paymes->setOrderPayment([
    'product_name' => 'Test',
    'product_comment' => 'Test Ürünü Açıklama',
    'card_owner' => 'İsim Soyisim',
    'card_number' => '5555555555555555',
    'card_month' => '00',
    'card_year' => '00',
    'card_cvv' => '000'
]);
die($paymes->run('Fiyat'));

//Herhangi bir sorunda belirlemiş olduğunuz callback adresine dönüş olacak. Bu sepebten dolayı işlem yaptırmak için $_POST['message']'den dönen veri "AUTHORIZED"e eşit mi diye kontrol ettirin.
```
# Destek
E-Posta Adresi: [contact@bugraozkan.com.tr](http://contact@bugraozkan.com.tr "contact@bugraozkan.com.tr")

Geri dönüşte size gönderilecek verilere pdf'i indirerek ulaşabilirsiniz.
