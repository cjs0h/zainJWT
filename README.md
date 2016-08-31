# zainJWT
A php class For Using ZainCash API
#How-TO
* include the class file into you project
```php
	include"zainJWT.php";
```
* Make an object to ref. the class and send the parameters via it

```php
	$Obj = new zainJWT($Data_Array,$Api_Secret);
```
* the $Data_Array Must Contain the {sell_price as amount , the name of the item you want to sell as serviceType , the customer wallet Number as msisdn , the redirect Url as redirectUrl}
	and the $Api_Secret is the api Secret code !
* then Just call the function SendData
```php
	    // for live
	    $obj->sendData($MERCHANT_ID,$Api_NUMBER,live);
	    // for testing 
	    $obj->sendData($MERCHANT_ID,$Api_NUMBER,test);
	    $obj->sendData($MERCHANT_ID,$Api_NUMBER);
```

* when you call this method the class will redirect your customer to zain payment site
	to comlplete the transaction 
