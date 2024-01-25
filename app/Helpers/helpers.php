<?php

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Arr;

if (! function_exists('destructuring')) {
    function destructuring($array, $keys = [])
    {
        $keys = Arr::wrap($keys);
        $results = [];
        foreach ($keys as $key) {
            if (is_array($key)) {
                $results[$key] = Arr::only($array, $key);
            } else {
                $results[$key] = array_key_exists($key, $array) ? $array[$key] : null;
            }
        }
        // $results[] = Arr::except($array, Arr::flatten($keys));
        return $results;
    }
}

if (! function_exists('fullDataFromArray')) {
    function fullDataFromArray($array = [], $separator = ', ')
    {
        return implode($separator, collect($array)->filter(function ($field) {
            return strlen(trim($field)) > 0;
        })->toArray());
    }
}

if (! function_exists('generateInitialsColor')) {
    function generateInitialsColor()
    {
        $bg_colors = [
            'primary',
            'secondary',
            'warning',
            'danger',
            'success',
            'info',
            'azure',
            'blue',
            'pink',
            'indigo',
            'dark',
            'gray',
            'orange',
            'teal',
            'purple',
            'lighter',
        ];
        return Arr::random($bg_colors);
    }
}

if (! function_exists('makeInitialsFromSingleWord')) {
    function makeInitialsFromSingleWord(string $name)
    {
        preg_match_all('#([A-Z]+)#', $name, $capitals);
        if (count($capitals[1]) >= 2) {
            return mb_substr(implode('', $capitals[1]), 0, 2);
        }

        return strtoupper(mb_substr($name, 0, 2));
    }
}

if (! function_exists('generateInitials')) {
    function generateInitials(string $name)
    {
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            $words[0] = preg_replace('/[^A-Za-z0-9\-]/', '', $words[0]);
            $words[1] = preg_replace('/[^A-Za-z0-9\-]/', '', $words[1]);

            return strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
        }

        return makeInitialsFromSingleWord($name);
    }
}
if (! function_exists('folioGenerator')) {
    function folioGenerator($table, $field = 'folio')
    {
        return IdGenerator::generate(['table' => $table, 'length' => 10, 'field' => $field, 'prefix' => 'VENT-']);
    }
}
if (! function_exists('convertToReadableSize')) {
    function convertToReadableSize($size)
    {
      $base = log($size) / log(1024);
      $suffix = array("B", "KB", "MB", "GB", "TB");
      $f_base = floor($base);
      return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }
}
if (! function_exists('getMonthName')) {
    function getMonthName($n)
    {
        $months = [
            '',
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];
      return $months[$n];
    }
}