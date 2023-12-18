<?php

class FatpayApi
{
    public $sServer = 'mysql.localhost';
    public $sUser = 'root';
    public $sPassword = 'dockerroot';
    public $sDb = 'fatpay';
    public $sTable = 'transactions';

    public function validatePayment()
    {
        $this->createDb();
        $this->createTable();

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        $aData = json_decode($_POST['data'],true);
        $this->logTransaction($aData);

        $aStatus = ['status' => 'APPROVED'];
        if ($aData['payment_type'] == 'fatredirect') {
            $aStatus['status'] = 'REDIRECT';
            $aStatus['url'] = str_replace('fatpayAPI.php', 'fatredirectVerify.php','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        } else if (strtolower($aData['billing_lastname']) == 'failed' || strtolower($aData['shipping_lastname']) == 'failed') {
            $aStatus['status'] = 'ERROR';
            $aStatus['errormessage'] = 'no failures allowed';
        }

        echo json_encode($aStatus);
    }

    protected function createDb()
    {
        $oConn = $this->getMysqliConnection($this->sServer, $this->sUser, $this->sPassword);
        $sQuery = 'CREATE DATABASE IF NOT EXISTS '.$this->sDb;
        $oConn->query($sQuery);
        $oConn->close();
    }

    protected function createTable()
    {
        $oConn = $this->getMysqliConnection($this->sServer, $this->sUser, $this->sPassword, $this->sDb);

        $sQuery = 'CREATE TABLE IF NOT EXISTS '.$this->sTable.' (
id int AUTO_INCREMENT PRIMARY KEY,
transaction_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
shop VARCHAR(255) NOT NULL,
shop_version VARCHAR(10) NOT NULL,
fatpay_version VARCHAR(10) NOT NULL,
payment_type VARCHAR(255) NOT NULL,
language VARCHAR(4) NOT NULL,
billing_fname VARCHAR(255),
billing_lname VARCHAR(255),
billing_street VARCHAR(255),
billing_zip VARCHAR(10),
billing_city VARCHAR(255),
billing_country VARCHAR(255),
shipping_fname VARCHAR(255),
shipping_lname VARCHAR(255),
shipping_street VARCHAR(255),
shipping_zip VARCHAR(10),
shipping_city VARCHAR(255),
shipping_country VARCHAR(255),
email VARCHAR(255),
customer_nr VARCHAR(255),
amount DECIMAL(8,2),
currency VARCHAR(3)
)';
        $oConn->query($sQuery);
        $oConn->close();
    }

    protected function logTransaction($aData)
    {
        $oConn = $this->getMysqliConnection($this->sServer, $this->sUser, $this->sPassword, $this->sDb);
        $sQuery = 'INSERT INTO '.$this->sTable.' (
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
currency
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $oStmt = $oConn->prepare($sQuery);

        $oStmt->bind_param(
            'sssssssssssssssssssds',
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
            $aData['currency']
        );
        $oStmt->execute();
        $oConn->close();
    }

    protected function getMysqliConnection($sServer, $sUser, $sPassword, $sDb = null) {
        $oConn = new mysqli($sServer, $sUser, $sPassword, $sDb);
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

