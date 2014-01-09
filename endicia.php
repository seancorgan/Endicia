<?php require 'vendor/autoload.php';
use Guzzle\Http\Client;

class Endicia { 
	/**
	 * Client Object from Guzzle
	 * @var [type]
	 */
	private $client; 
	/**
	 * Requester ID from Endicia
	 * @var [type]
	 */
	private $RequesterID;
	/**
	 * Account ID from Endicia
	 * @var [type]
	 */
	private $AccountID;
	/**
	 * Passphrase from Endicia
	 * @var string
	 */
	private $PassPhrase;

	/**
	 * Unique Transaction ID 
	 * @var string
	 */
	public  $PartnerTransactionID; 

	/**
	 * Constructor 
	 * @param string $RequesterID 
	 * @param string $AccountID   
	 * @param string $PassPhrase 
	 */
	function __construct($RequesterID, $AccountID, $PassPhrase)
	{ 
		$this->client = new Client('https://www.envmgr.com');
		$this->RequesterID = $RequesterID;
		$this->AccountID = $AccountID; 
		$this->PassPhrase = $PassPhrase;
	}

	/**
	 * Sends A requst for shipping label and info to endicia
	 * @param  array $data Array of data to be formated as XML see Endicia documentation
	 * @return array       The Response data from Endicia as an array , endica actually retuns XML 
	 */
	function request_shipping_label($data) { 
		// Note you may want to associate this in some other way, like with a database ID.  
		$this->PartnerTransactionID = substr(uniqid(rand(), true), 0, 10); 

		$xml = '<LabelRequest Test="YES" LabelType="Default" LabelSize="4X6" ImageFormat="GIF">
					<RequesterID>'.$this->RequesterID.'</RequesterID>
					<AccountID>'.$this->AccountID.'</AccountID> 
					<PassPhrase>'.$this->PassPhrase.'</PassPhrase>
					<PartnerTransactionID>'.$this->PartnerTransactionID.'</PartnerTransactionID>'; 

		if(!empty($data)) { 
			foreach($data as $node_key => $node_value) { 
				$xml .= '<'.$node_key.'>'.$node_value.'</'.$node_key.'>'; 
			}
		} 
					
		$xml .=	'<ResponseOptions PostagePrice="TRUE"/> 
				</LabelRequest>'; 

		$data = array("labelRequestXML" => $xml); 
		$request = $this->client->post('/LabelService/EwsLabelService.asmx/GetPostageLabelXML', array(), $data);
		return $this->send_request($request);
	}


	/**
	 * Send HTTP Request using Guzzle
	 * @param  object $request THe Request Object to send using guzzle
	 */
	function send_request($request) { 
		$response = $request->send();
		return $this->parse_response($response);
	}

	/**
	 * Parses the response from the request using guzzle client
	 * @todo  
	 * @param  object $response the response object from guzzle
	 * @return array  returns data from response
	 */	
	function parse_response($response) { 
		$data = $response->getBody();

		// Note we could not use Guzzle XML method becuase Endicia does not return valid XML it seems
		$sxe = new SimpleXMLElement($data);

		if($sxe->status == 0) { 
			$return_data = array(); 
			$return_data['Status'] = (string) $sxe->Status;
			$return_data['Base64LabelImage'] = (string) $sxe->Base64LabelImage;
			$return_data['TrackingNumber'] = (string) $sxe->TrackingNumber;
			$return_data['FinalPostage'] = (string) $sxe->FinalPostage;
			$return_data['TransactionID'] = (string) $sxe->TransactionID;
			$return_data['PostmarkDate'] = (string) $sxe->PostmarkDate;
			$return_data['DeliveryTimeDays'] = (string) $sxe->PostagePrice->DeliveryTimeDays; 
			return $return_data; 
		} else { 
			return array('status' => 'error', 'message' => $sxe->ErrorMessage); 
		}
	} 

} 
?>