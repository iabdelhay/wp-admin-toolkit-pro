<?php 
namespace WPAdminToolkitPro\Core;

trait Initializable
{    
    public static function init(...$args): static
    {
        return static::instance(...$args);
    }
}