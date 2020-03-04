<?php

namespace Omnipay\Flo2cash;

use Omnipay\Common\AbstractGateway;
use Omnipay\Flo2cash\Message\AuthorizeRequest;

/**
 * Flo2cash Gateway
 *
 * This gateway is to process payments against the gateway provided by Flo2cash.
 * (https://www.flo2cash.co.nz/)
 *
 *
 * Any card number which passes the Luhn algorithm and ends in an odd number is declined,
 * for example: 4111111111111111
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Flo2cash Payments WS';
    }

    public function getDefaultParameters()
    {
        return array(
            'AccountId' => 'myAccountId',
            'Username' => 'myUsername',
            'Password' => 'myPassword',
            'storeCard' => 'false',
            'email' => '',
            'testMode' => 'true',
        );
    }

    public function getUsername()
    {
         return $this->getParameter('Username');
    }
    
    public function setUsername($value)
    {
         return $this->setParameter('Username', $value);
    }

    public function getPassword()
    {
         return $this->getParameter('Password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Get the AccountId.
     *
     * @returns string AccountId for this charge
     */
    public function getAccountId()
    {
         return $this->getParameter('AccountId');
    }

    /**
     * Set the AccountId.
     *
     * @param string AccountId for this charge
     */
    public function setAccountId($value)
    {
         return $this->setParameter('AccountId', $value);
    }

    /**
     * Set the Email.
     *
     * @param string $Email for this charge
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get the Email.
     *
     * @returns string $Email for this charge.
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    // this is the main type of purchase
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Flo2cash\Message\PurchaseRequest', $parameters);
    }

    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Flo2cash\Message\CreateCardRequest', $parameters);
    }

    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Flo2cash\Message\DeleteCardRequest', $parameters);
    }
    
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Flo2cash\Message\RefundRequest', $parameters);
    }

    /**
     * Set the storeCard.
     *
     * @param string $StoreCard for this charge
     */
    public function setStoreCard($value)
    {
        return $this->setParameter('storeCard', $value);
    }

    /**
     * Get the storeCard.
     *
     * @returns string $Email for this charge.
     */
    public function getStoreCard()
    {
        return $this->getParameter('storeCard');
    }
}
