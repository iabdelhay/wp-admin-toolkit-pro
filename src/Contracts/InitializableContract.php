<?php 
namespace WPAdminToolkitPro\Contracts;

interface InitializableContract
{
    public static function init(...$args): self;
    public static function instance(...$args): self;
}