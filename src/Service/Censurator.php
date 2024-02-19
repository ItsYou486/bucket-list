<?php

namespace App\Service;

class Censurator
{

    private $wordList = [
        'merde',
        'putain',
        'bordel',
        'connard',
        'salope',
        'enculé',
        'bâtard',
        'foutre',
        'cul',
        'bite',
        'nique',
        'trou du cul',
        'chié',
        'salaud',
        'enfoiré',
        'connasse',
        'crétin',
        'débile',
        'pétasse',
        'taré',
        'zut',
        'saperlipopette',
    ];


    public function purify(string $string)
    {

        foreach ($this->wordList as $word) {
            $replaceText = str_repeat('*', mb_strlen($word));
            $string = str_replace($word, $replaceText, $string);
        }
        return $string;
    }
}