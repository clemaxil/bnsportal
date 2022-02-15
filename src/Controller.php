<?php

declare(strict_types=1);

namespace App;

/**
 * Main Controller
 * @package MainController
 */
class Controller
{

    /**
     * 
     * @param string $mini 
     * @return string 
     */
    public function phpVersionCheck($mini = '7.3')
    {
        $versionChek = 'CORE OK';
        if (version_compare(PHP_VERSION, $mini, '<')) {
            $versionChek = 'CORE ERROR: php version must be higher than 7.3';
        }
        return $versionChek;
    }


    /**
     * function security clean input var
     *
     * @param string $var
     * @return string
     */
    public function cleanInputString($var)
    {
        return (strip_tags($var));
    }


    /**
     * load alls helpers
     * @return bool
     */
    public function loadHelpers(): bool
    {
        $helpersLoaded = false;
        foreach (\array_slice(scandir(__DIR__ . '/../helpers/'), 2) as $file) {
            require_once __DIR__ . '/../helpers/' . $file;
            $helpersLoaded = true;
        }

        return $helpersLoaded;
    }



    /**
     * generate strong password
     *
     * @param integer $length
     * @param string $available_sets
     * @return string password
     */
    public function generatePassword($length = 10, $available_sets = 'luds')
    {
        $sets = array();
        if (strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if (strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if (strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if (strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);
        return $password;
    }
}
