<?php
/**
 * @package Equitel PHP Library
 * @subpackage Equitel Core Class
 * @version 0.18.01
 * @author Mauko Maunde < hi@mauko.co.ke >
 * @link https://developers.equitybankgroup.com/
 */
namespace Equity;

class Equitel
{
	public $senderName;
	public $accountNumber;
	public $bicCode;
	public $mobileNumber;
	public $walletName;
	public $bankCode;
	public $branchCode;
	public $amount;
	public $countryCode = "254";
	public $currencyCode = "KSH";

	// Sandbox app consumer key
	private $key;

	// Sandbox app consumer secret
	private $secret;

	// Merchant ID
	private $id;

	// Merchant Username, provided by Equity Bank
	public $username;

	// Merchant Password, provided by Equity Bank
	public $password;

	// Whether in production or not
	protected $live;

	public function __construct( $live = true )
	{
		$this -> bicCode = EQUITY_BIC_CODE;
		$this -> walletName = EQUITY_WALLET_NAME;
		$this -> bankCode = EQUITY_BANK_CODE;
		$this -> branchCode = EQUITY_BRANCH_CODE;
		$this -> countryCode EQUITY_COUNTRY_CODE;
		$this -> currencyCode = EQUITY_CURRENCY_CODE;

		$this -> key = EQUITY_APP_KEY;
		$this -> secret = EQUITY_APP_SECRET;

		$this -> id = EQUITY_MERCHANT_ID;
		$this -> username = EQUITY_MERCHANT_USERNAME;
		$this -> password = EQUITY_MERCHANT_PASSWORD;

		$this -> live = $live;
	}

	public function sandBoxToken()
	{
		$url = "https://api.equitybankgroup.com/identity/v1-sandbox/token";
		$curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Content-Type:application/json' ]);
        $curl_post_data = array(
		    "username" => $this -> username,
		    "password" => $this -> password,
		    "grant_type" => 'password'
		    )
		);
        $data_string = json_encode( $curl_post_data );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );

        $curl_response = curl_exec( $curl );

        return json_decode( $curl_response )['token'];	
    }

	public function liveToken()
	{
		$url = "https://api.equitybankgroup.com/identity/v1/token";
		$curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Content-Type:application/json' ]);
        $curl_post_data = array(
		    "username" => $this -> username,
		    "password" => $this -> password,
		    "grant_type" => 'password'
		    )
		);
        $data_string = json_encode( $curl_post_data );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );

        $curl_response = curl_exec( $curl );

        return json_decode( $curl_response )['token'];
	}

	public function changePassword( $old, $new )
	{
		$url = ( $this -> live === true ) ? "https://api.equitybankgroup.com/identity/v1/merchants/".$this -> id ."/changePassword" : "https://api.equitybankgroup.com/identity/v1-sandbox/merchants/".$this -> id ."/changePassword";
		$token = ( $this -> live === true ) ? liveToken() : sandBoxToken();
		
		$curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));
        $curl_post_data = array( 
        	"currentPassword" => $old,
        	"newPassword" => $new
		);
        $data_string = json_encode( $curl_post_data );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );

        $curl_response = curl_exec( $curl );

        return json_decode( $curl_response );
	}

	public function airtime( $mobileNumber, $amount, $reference, $telco = "equitel" )
	{
		$url = ( $this -> live === true ) ? "https://api.equitybankgroup.com/transaction/v1/airtime" : "https://api.equitybankgroup.com/transaction/v1-sandbox/airtime" ;
		$token = ( $this -> live === true ) ? liveToken() : sandBoxToken();

		$curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));
        $curl_post_data = array(
		    "customer" => array( "mobileNumber" => $mobileNumber ),
		    "airtime" => array (
		        "amount" => $amount,
		        "reference" => $reference,
		        "telco" => $telco
		    )
		);
        $data_string = json_encode( $curl_post_data );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );

        $curl_response = curl_exec( $curl );

        return json_decode( $curl_response );
	}

	//Allows a Remittance Agent to transfer money to a Recipient Account in real-time.
	public function remit( $transactionReference, $senderName, $accountNumber, $bicCode, $mobileNumber, $walletName, $bankCode, $branchCode, $amount, $countryCode = "254", $currencyCode = "KSH", $paymentType = "", $remarks = "Remittance" )
	{
		$url = ( $this -> live === true ) ? "https://api.equitybankgroup.com/transaction/v1/remittance" : "https://api.equitybankgroup.com/transaction/v1-sandbox/remittance";
		$token = ( $this -> live === true ) ? liveToken() : sandBoxToken();

		$curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));
        $curl_post_data = array (
		    "transactionReference" => $transactionReference,
		    "source" => array (
		        "senderName" => $senderName
		    ),
		    "destination" => array (
		        "accountNumber" => $accountNumber,
		        "bicCode" => $this -> bicCode,
		        "mobileNumber" => $mobileNumber,
		        "walletName" => $walletName,
		        "bankCode" => $bankCode,
		        "branchCode" => $branchCode
		    ),
		    "transfer" => array (
		        "countryCode" => $countryCode,
		        "currencyCode" => $currencyCode,
		        "amount" => $amount,
		        "paymentType" => $paymentType,
		        "paymentReferences" => [ "" ],
		        "remarks" => $remarks
		    )
		);
	}

	//A merchant can view the latest status of a transaction being processed
	public function status( $transactionId )
	{
		$url = ( $this -> live === true ) ? "https://api.equitybankgroup.com/transaction/v1/payments/".$transactionId : "https://api.equitybankgroup.com/transaction/v1-sandbox/payments/".$transactionId;
		$token = ( $this -> live === true ) ? liveToken() : sandBoxToken();

		$curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

        $curl_response = curl_exec( $curl );

        return json_decode( $curl_response );
	}

	public function pay( $mobileNumber, $amount, $description, $type, $auditNumber )
	{
		$url = ( $this -> live === true ) ? "https://api.equitybankgroup.com/transaction/v1/payments" : "https://api.equitybankgroup.com/transaction/v1-sandbox/payments";
		$token = ( $this -> live === true ) ? liveToken() : sandBoxToken();

		$curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url);
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));
        $curl_post_data = array (
		    "customer" => array (
		        "mobileNumber" => $mobileNumber
		    ),
		    "transaction" => array (
		        "amount" => $amount,
		        "description" => $description,
		        "type" => $type,
		        "auditNumber" => $auditNumber
		    )
		);
        $data_string = json_encode( $curl_post_data );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );

        $curl_response = curl_exec( $curl );

        return json_decode( $curl_response );
	}
}
