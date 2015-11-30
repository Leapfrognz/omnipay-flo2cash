<?php

namespace Omnipay\Flo2cash\Message;

use Omnipay\Tests\TestCase;
use SimpleXMLElement;

class PurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $this->request->initialize(array(
            'amount' => '10.00',
            'merchantReferenceCode' => 'TestSuite',
            'card' => $this->getValidCard(),
        ));
        $response = $this->request->getData();
        $data = $response['Data'];
        $this->assertInstanceOf('SimpleXMLElement', $data);
        $this->assertSame('10.00', (string) $data->{'Amount'});
        $this->assertSame('TestSuite', (string) $data->{'Reference'});
        $this->assertEquals('ProcessPurchase', (string) $response['Transaction']);
    }
    
    public function testGetDataToken()
    {
        $this->request->initialize(array(
            'amount' => '10.00',
            'merchantReferenceCode' => 'TestSuite',
            'cardReference' => '11111111',
        ));
        $response = $this->request->getData();
        $data = $response['Data'];
        $this->assertSame('10.00', (string) $data->Amount);
        $this->assertSame('TestSuite', (string) $data->Reference);
        $this->assertSame('11111111', (string) $data->CardToken);
        $this->assertEquals('ProcessPurchaseByToken', (string) $response['Transaction']);

    }
}
