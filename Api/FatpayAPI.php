<?php

namespace Fatchip\FATPay\Api;

class FatpayApi
{
    public $sServer = 'mysql.localhost';
    public $sUser = 'root';
    public $sPassword = 'dockerroot';
    public $sDb = 'fatpay';
    public $sTable = 'transactions';

    /**
     * Logs transaction then evaluates payment status and echoes it
     *
     * @return void
     */
    public function validatePayment()
    {
        $this->createDb();
        $this->createTable();

        $aData = $this->getPostData();
        $sTransactionId = $this->getTransactionId();

        $aStatus = ['status' => 'APPROVED'];
        $sPaymentStatus = 'APPROVED';
        if ($aData['payment_type'] == 'fatredirect') {
            //setting status REDIRECT when paying with fatredirect
            $aStatus['status'] = 'REDIRECT';
            $sPaymentStatus = 'PENDING';
            $aStatus['redirectUrl'] = $this->getRedirectUrl($aData['redirectUrl'], $sTransactionId);

        } else if (strtolower($aData['billing_lastname']) == 'failed' || strtolower($aData['shipping_lastname']) == 'failed') {
            //setting status ERROR when lastname is 'failed'
            $aStatus['status'] = 'ERROR';
            $sPaymentStatus = 'DENIED';
            if ($aData['language'] == 'de') {
                $aStatus['errormessage'] = 'Keine failures erlaubt!';
            } else {
                $aStatus['errormessage'] = 'no failures allowed';
            }
        }

        $this->logTransaction($aData,$sTransactionId,$sPaymentStatus);

        echo json_encode($aStatus);
    }

    public function getRedirectUrl($sRedirectUrl, $sTransactionId)
    {
        $sBase = str_replace('FatpayAPI', 'fatredirect', $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        return $sBase.'?transaction='.$sTransactionId.'&redirectUrl='.urlencode($sRedirectUrl);
    }

    /**
     * Returns contents of php://input as array
     *
     * @return mixed
     */
    public function getPostData()
    {
        return json_decode(file_get_contents("php://input"),true);
    }

    /**
     * Creates db if it doesnt exist
     *
     * @return void
     */
    protected function createDb()
    {
        $oConn = $this->getMysqliConnection($this->sServer, $this->sUser, $this->sPassword);
        $sQuery = 'CREATE DATABASE IF NOT EXISTS '.$this->sDb;
        $oConn->query($sQuery);
        $oConn->close();
    }

    /**
     * Creates db table if it doesnt exist
     *
     * @return void
     */
    protected function createTable()
    {
        $oConn = $this->getMysqliConnection($this->sServer, $this->sUser, $this->sPassword, $this->sDb);

        $sQuery = 'CREATE TABLE IF NOT EXISTS '.$this->sTable.' (transactionId VARCHAR(255) PRIMARY KEY)';
        $oConn->query($sQuery);
        $oConn->close();

        $this->addColumnfIfnotExists('transaction_timestamp', 'DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addColumnfIfnotExists('shop', 'VARCHAR(255) NOT NULL');
        $this->addColumnfIfnotExists('shop_version', 'VARCHAR(10) NOT NULL');
        $this->addColumnfIfnotExists('fatpay_version', 'VARCHAR(10) NOT NULL');
        $this->addColumnfIfnotExists('payment_type', 'VARCHAR(255) NOT NULL');
        $this->addColumnfIfnotExists('language', 'VARCHAR(4) NOT NULL');
        $this->addColumnfIfnotExists('billing_fname', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('billing_lname', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('billing_street', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('billing_zip', 'VARCHAR(10)');
        $this->addColumnfIfnotExists('billing_city', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('billing_country', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('shipping_fname', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('shipping_lname', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('shipping_street', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('shipping_zip', 'VARCHAR(10)');
        $this->addColumnfIfnotExists('shipping_city', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('shipping_country', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('email', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('customer_nr', 'VARCHAR(255)');
        $this->addColumnfIfnotExists('amount', 'DECIMAL(8,2)');
        $this->addColumnfIfnotExists('currency', 'VARCHAR(3)');
        $this->addColumnfIfnotExists('payment_status', 'VARCHAR(255)');
    }

    protected function addColumnfIfnotExists($sColumnName,$sColumnParams)
    {
        $oConn = $this->getMysqliConnection($this->sServer, $this->sUser, $this->sPassword, $this->sDb);

        $oResult = $oConn->query("SHOW COLUMNS FROM ".$this->sTable." LIKE '{$sColumnName}'");
        if (!empty($oResult->num_rows)) {
            $oConn->query("ALTER TABLE ".$this->sTable." ADD {$sColumnName} {$sColumnParams}");
        }
        $oConn->close();
    }

    protected function getTransactionId()
    {
        return md5(uniqid('', true) . '|' . microtime());
    }

    /**
     * Logs transaction to db
     *
     * @param $aData
     * @return void
     */
    protected function logTransaction($aData,$sTransactionId,$sPaymentStatus)
    {
        $oConn = $this->getMysqliConnection($this->sServer, $this->sUser, $this->sPassword, $this->sDb);
        $sQuery = 'INSERT INTO '.$this->sTable.' (
transactionId,
shop, 
shop_version, 
fatpay_version,
payment_type, 
language, 
billing_fname, 
billing_lname,
billing_street,
billing_zip,
billing_city,
billing_country,
shipping_fname, 
shipping_lname,
shipping_street,
shipping_zip,
shipping_city,
shipping_country,
email,
customer_nr,
amount,
currency,
payment_status
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $oStmt = $oConn->prepare($sQuery);

        $oStmt->bind_param(
            'ssssssssssssssssssssdss',
            $sTransactionId,
            $aData['shopsystem'],
            $aData['shopversion'],
            $aData['moduleversion'],
            $aData['payment_type'],
            $aData['language'],
            $aData['billing_firstname'],
            $aData['billing_lastname'],
            $aData['billing_street'],
            $aData['billing_zip'],
            $aData['billing_city'],
            $aData['billing_country'],
            $aData['shipping_firstname'],
            $aData['shipping_lastname'],
            $aData['shipping_street'],
            $aData['shipping_zip'],
            $aData['shipping_city'],
            $aData['shipping_country'],
            $aData['email'],
            $aData['customer_nr'],
            $aData['order_sum'],
            $aData['currency'],
            $sPaymentStatus
        );
        $oStmt->execute();
        $oConn->close();
    }

    /**
     * Returns mysqli connection object
     *
     * @param $sServer
     * @param $sUser
     * @param $sPassword
     * @param $sDb
     * @return \mysqli|void
     */
    protected function getMysqliConnection($sServer, $sUser, $sPassword, $sDb = null) {
        $oConn = new \mysqli($sServer, $sUser, $sPassword, $sDb);
        if ($oConn->connect_error) {
            die(json_encode(['MySQL connection error: '.$oConn->connect_error]));
        }
        return $oConn;
    }
}

if (!defined('PHP_UNIT')) {
    $oApi = new FatpayApi();
    $oApi->validatePayment();
}

