<?php
namespace App\Traits;

trait ModuleSessionManager
{
    /**
     * To set the module in session
     *
     * @param $module
    */
    public static function setModule($module, $performAction = true)
    {
        return session([
            'module' => $module,
            'performAction' => $performAction
        ]);
    }

    /**
     * To remove the module in session
    */
    public static function removeModule()
    {
        return \session()->forget(['module', 'performAction']);
    }

    /**
     * To get the module in session
    */
    public static function getModule()
    {
        if (!session('module')) {
            session(['module' => 'retail']);
        }
        return session('module');
    }
}
