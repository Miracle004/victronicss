<?php
require 'C:/xampp/htdocs/victronics/SendinBlue/lib/Configuration.php';
require 'C:/xampp/htdocs/victronics/SendinBlue/lib/ObjectSerializer.php';
require 'C:/xampp/htdocs/victronics/SendinBlue/lib/Model/ModelInterface.php';
require 'C:/xampp/htdocs/victronics/SendinBlue/lib/Model/SendSmtpEmail.php';
require 'C:/xampp/htdocs/victronics/SendinBlue/lib/Api/TransactionalEmailsApi.php';

echo "All files included successfully!";

use SendinBlue\Client\Model\ModelInterface;

$test = new class implements ModelInterface {
    public function getModelName() {
        return "TestModel";
    }

    public static function swaggerTypes() {
        return [];
    }

    public static function swaggerFormats() {
        return [];
    }

    public static function attributeMap() {
        return [];
    }

    public static function setters() {
        return [];
    }

    public static function getters() {
        return [];
    }

    public function listInvalidProperties() {
        return [];
    }

    public function valid() {
        return true;
    }
};

echo "Interface works!";
?>
