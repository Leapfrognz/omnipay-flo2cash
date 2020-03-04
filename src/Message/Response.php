<?php

namespace Omnipay\Flo2cash\Message;

use Omnipay\Common\Message\AbstractResponse;
use SimpleXMLElement;

/**
 * Flo2cash Response
 */
class Response extends AbstractResponse
{
    private $status = false;
    
    private $cardReference;
    
    private $message;
    
    private $responsexml; /* Response XML from Gateway */

    /**
     * Constructor
     *
     * @param $request the initiating request.
     * @param mixed $data
     */
    public function __construct($request, $data)
    {
        $this->request = $request;
        $this->response = $data;
        $this->processResponse($this->response);
    }

    /**
     *
     * Process the response
     *
     * @param $responseObj response returned from request
     *
     */
    
    private function processResponse($data)
    {
        // Strip soap:Body tags so can be parsed
        // by SimpleXMLElement

        $replacements = array('<soap:Body>','</soap:Body>','<soap:Fault>','</soap:Fault>');
        $data = str_replace($replacements, '', $data);
        $xml = new SimpleXMLElement($data);

        //  Data from the request can now be processed.
        if (isset($xml->AddCardResponse)) {
            // Response from AddCard returned
            $this->responsexml = (array) $xml->AddCardResponse; # Cast the result as array
            
            if (isset($this->responsexml['AddCardResult'])
                && strlen($this->responsexml['AddCardResult']) > 0) {
                $this->status = true;
                $this->cardReference = $this->responsexml['AddCardResult'];
                $this->message = 'Successfully Added Card';
            }

        } else if (isset($xml->RemoveCardResponse)) {
            // Response from RemoveCard returned
            $this->responsexml = (array) $xml->RemoveCardResponse; // Cast the result as array
            
            if (isset($this->responsexml['RemoveCardResult'])) {
                $this->status = true;
                $this->message = 'Successfully Removed Card';
            }
        } else if (isset($xml->ProcessPurchaseResponse)
                || isset($xml->ProcessPurchaseByTokenResponse)) {
            // Response from ProcessPurchase returned
            $this->responsexml = isset($xml->ProcessPurchaseResponse) ?
                (array) $xml->ProcessPurchaseResponse->transactionresult :
                (array) $xml->ProcessPurchaseByTokenResponse->transactionresult;
            
                // SOAP response is identical between two types so we can process alike.
            $this->message = $this->responsexml['Message'];
            
            if ($this->responsexml['Status'] == 'SUCCESSFUL') {
                $this->status = true;
            }
        } else if (isset($xml->ProcessRefundResponse)) {
            // Response from ProcessRefund returned
            $this->responsexml = (array) $xml->ProcessRefundResponse->transactionresult;
            
            if ($this->responsexml['Status'] == 'SUCCESSFUL') {
                $this->status = true;
            }
        } else if (isset($xml->faultactor)) {
            // This is a SOAP fault returning
            $responsexml = (array) $xml->detail->error;
            $this->status = false;
            $this->message = $responsexml['errormessage'];
        }
    }
    
    

    /**
     *
     * Return whether the response is successful or not
     *
     * @returns bool $status
     */
    public function isSuccessful()
    {
        return $this->status;
    }
    /**
     *
     * Return the transaction reference from the gateway
     *
     * @returns string $transactionReference
     */
    public function getTransactionId()
    {
        return isset($this->responsexml['TransactionId']) ? $this->responsexml['TransactionId'] : null;
    }
    /**
     *
     * Return the message from the gateway
     *
     * @returns string $gatewayMessage
     */
    public function getMessage()
    {
        return isset($this->responsexml['Message']) ?
               $this->responsexml['Message'] : $this->message;
    }
    /**
     *
     * Return the card reference (token) from the gateway
     *
     * @returns string $cardReference
     */
    public function getCardReference()
    {
        return isset($this->cardReference) &&
                     strlen($this->cardReference)
                     ? $this->cardReference
                     : null;
    }
    /**
     *
     * Return the transaction details as (array) from the gateway
     * 
     * @returns array $this->responsexml
     */
    public function getDetailsArray()
    {
        if (isset($this->message)) {
            # Add the message to the responsexml
            $this->responsexml['Message'] = $this->message;
        }
        return isset($this->responsexml)
                     ? $this->responsexml
                     : null;
    }
}
