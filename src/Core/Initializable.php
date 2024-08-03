<?php 
namespace WPAdminToolkitPro\core;

trait Initializable
{    
    public static function init(...$args): static
    {
        return static::instance(...$args);
    }
}