<?php 
namespace WPAdminToolkitPro\core;

trait Singleton
{
    private static $instance;

    public static function instance(...$args): static
    {
       if(static::$instance){
            return static::$instance;
       }

       return static::$instance = new static(...$args);
    }
}