<?php 
namespace WPAdminToolkitPro\Contracts;

interface SingletonContract
{
    public static function instance(...$args): self;
}