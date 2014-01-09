Endicia
=======

A simple PHP wrapper for Endicia's API 

## Depends
1. Composer
2. Guzzle

## Installation 
1. Run Composer Install

## Usage 

### Request a Shipping Label 

 ``` php

$RequesterID = "TESTREQ"; 
$AccountID = "123456"; 
$PassPhrase = "PASSWORD"; 

 
$e = new Endicia($RequesterID, $AccountID, $PassPhrase);
	// See Endicia Documentation for correct values.  Array Keys must match XML node name 
	$data = array(
		'MailClass' => 'Priority', 
		'WeightOz' => 15,
		'MailpieceShape' => 'Parcel', 
		'Description' => 'Electronics', 
		'FromName' => 'Bilbo Bagins',  
		'ReturnAddress1' => '777 E Hobbit Lane', 
		'FromCity' => 'Middle Earth', 
		'FromState' => 'AZ', 
		'FromPostalCode' => '85296', 
		'FromZIP4' => '0004', 
		'ToName' => 'Gandalf Grey',
		'ToCompany' => 'The White Wizard',
		'ToAddress1' => 'White Tower',
		'ToCity' => 'Morder',
		'ToState' => 'Az',
		'ToPostalCode' => '85296',
		'ToZIP4' => '0004', 
		'ToDeliveryPoint' => '00',
		'ToPhone' => '2125551234' 
	); 

	$res = $e->request_shipping_label($data);

	<pre>
	 var_dump($res);
	</pre>

  ```
