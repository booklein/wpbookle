<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/*
* Creates a cache file from a text
* 
* @author Ernest Marcinko <ernest.marcinko@wp-dreams.com>
* @version 1.0
* @link http://wp-dreams.com, http://codecanyon.net/user/anago/portfolio
* @copyright Copyright (c) 2012, Ernest Marcinko
*/
if (!class_exists('wpdreamsTextCache')) {
  class wpdreamsTextCache {
   
    function __construct($file, $time) {
       $this->file = $file;
       $this->time = $time;
       $this->method = $this->can_get_file();
    }
    
    function getCache($file="") {
       if ($file!="") $this->file = $file;
       if ($this->method==false) return false;
       if (file_exists($this->file)) {
        $filetime = filemtime($this->file);
       } else {
        return false;
       }
       $current_time = time();
       if (($current_time-$filetime)>$this->time) return false;
       return file_get_contents($this->file);
    }
    
    function setCache($content, $file="") {
       if ($file!="") $this->file = $file;
       if ($this->method===false) return false;
       if (file_exists($this->file)) {
        $filetime = filemtime($this->file);
       } else {
        $filetime = 0;
       }
       $current_time = time();
       if (($current_time-$filetime)>$this->time) {
          @wpd_put_file($this->file, $content);  
       }
    }
    
    function _ckdir($fn) {
        if (strpos($fn,"/") !== false) {
            $p=substr($fn,0,strrpos($fn,"/"));
            if (!is_dir($p)) {
                //_o("Mkdir: ".$p);
                mkdir($p,0777,true);
            }
        }
    }  
    
    function can_get_file() {
      if (function_exists('curl_init')){
        return 1;
      } else if (ini_get('allow_url_fopen')==true) {
        return 2;
      }
      return false;
    } 
    
    function url_get_contents($Url, $method) {
        if ($method==2) {
          return file_get_contents($Url);
        } else if ($method==1) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $Url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $output = curl_exec($ch);
          curl_close($ch);
          return $output;
        }
    }    
  }
}