<?php

namespace Mouf\MVC\BCE\Classes\Formatters;

use Mouf\Utils\Common\Formatters\BijectiveFormatterInterface;

/**
 * This very special formatter is used to remove potentially dangerous HTML tags
 * from the output.
 * Purifying is applied during the "unformat" and not during the "format" phase.
 * 
 */
class HtmlPurifierFormatter implements BijectiveFormatterInterface {

    

    public function __construct() {
       
    }

    /**
     * Formats the value.
     *
     * @param string $value
     */
    public function format($value) {
        return $value;
    }

    /**
     * (non-PHPdoc)
     * @see BijectiveFormatterInterface::unformat()
     */
    public function unformat($value) {
    	$config = \HTMLPurifier_Config::createDefault();
    	$purifier = new \HTMLPurifier($config);
    	return $purifier->purify($value);
    }

}

