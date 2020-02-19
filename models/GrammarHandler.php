<?php


namespace app\models;


class GrammarHandler
{

    public const COTTAGE_PERSONALS_PRESET = '%ИМЯ-ОТЧЕСТВО%';
    public const COTTAGE_FULL_PERSONALS_PRESET = '%FULLUSERNAME%';
    public const TEXT_PRESET = '%ТЕКСТ%';

    public static function clearWhitespaces($string){
        // заменю встречающиеся несколько пробелов подряд на один и обрежу пробелы в начале и конце
        $regexp = '/ {2,}/';
        $string = preg_replace($regexp, ' ', $string);
        return trim($string);
    }


    public static function personalsToArray($string){
        // извлекаю имя и отчество из персональных данных
        $result = explode(' ', $string);
        if(count($result) === 3){
            return ['lname' => $result[0], 'name' => $result[1], 'fname' => $result[2]];
        }
        return $string;
    }

    public static function getPersonInitials($cottageOwnerPersonals)
    {
        $personalsArray = self::personalsToArray($cottageOwnerPersonals);
        if(is_array($personalsArray)){
            return $personalsArray['lname'] . ' ' . substr($personalsArray['name'],0, 2) . '. ' . substr($personalsArray['fname'],0, 2) . '.';
        }
        return $personalsArray;
    }


    public static function handlePersonals($name):string
    {
        if($data = self::personalsToArray($name)){
            if (is_array($data)){
                return "{$data['name']} {$data['fname']}";
            }

            return $data;

        }
        return $name;
    }

    public static function handleMailText($template, $name, $text)
    {
        if(strpos($template, self::COTTAGE_PERSONALS_PRESET)){
            $template = str_replace(self::COTTAGE_PERSONALS_PRESET, $name, $template);
        }
        if(strpos($template, self::TEXT_PRESET)){
            $template = str_replace(self::TEXT_PRESET, $text, $template);
        }
        return $template;
    }
}