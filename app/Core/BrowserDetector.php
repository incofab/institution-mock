<?php
namespace App\Core;

class BrowserDetector
{
    private static $userAgent = null;
    private static $detect = null;
    
    private static function getObj()
    {
        if(self::$detect) return self::$detect;
        
        self::$detect = new \Detection\MobileDetect();
        
        return self::$detect;
    }
    
    private static function getUserAgent() 
    {
        if(self::$userAgent) return self::$userAgent;
        
        self::$userAgent = array_get($_SERVER, 'HTTP_USER_AGENT', null);
        
        return self::$userAgent;
    }
    
    static function isOperamini() 
    { 
//      if(stristr(self::getUserAgent(), 'Opera Mini')) return true;
        return self::getObj()->version('Opera Mini') ? true : false;
    }
    
    static function isLiteBrowser() 
    {
        if(self::isOperamini()) return true;
        
        return false;
    }
    
    
}