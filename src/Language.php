<?php

namespace LegionLab\Utils;

use LegionLab\Troubadour\Collections\Session;
use LegionLab\Troubadour\Collections\Settings;

/**
 * Controller Language
 *
 * Created by PhpStorm.
 * User: Leonardo Vilarinho
 * Date: 07/07/2016
 * Time: 22:02
 */
class Language
{
    /**
     * @var string - diretório definido para ter arquivos de idiomas
     */
    private static $dir = 'languages/';

    /**
     * Resgata o idioma atual sendo utilizado.
     *
     * @return mixed - string contendo a linguagem ou false
     */
    public static function current()
    {
        return Session::get('current_lang');
    }

    /**
     * Define uma linguagem padrão para o site dependendo do idioma do navegador do usuário, caso não encontre
     * arquivo de linguagem para o idioma nativo, pega o primeiro idioma que encontrar.
     */
    public static function init($home)
    {
        self::$dir = $home;
        if(Session::get('current_lang') === false) {
           $lang = mb_strtolower(mb_substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2));
           if(file_exists(self::$dir .  $lang. Settings::get('langExtension')))
               Session::set('current_lang', $lang);
           else
               Session::set('current_lang', self::all()[0]);
        }
    }


    public static function get($index, $var, $section = true)
    {
        $file = self::$dir .  Session::get('current_lang'). Settings::get('langExtension');
        if(file_exists($file)) {
            $archive = parse_ini_file($file, $section);
            if(key_exists($index, $archive))
                return $archive[$index][$var];
        }
        return false;
    }

    /**
     * Troca a linguagem atual do sistema.
     *
     * @param $lang - nova linguagem a ser estabelecida
     */
    public static function set($lang)
    {
        Session::set('current_lang', in_array($lang, self::all()) ? $lang : null);
    }

    /**
     * Resgata todas linguagens que possuem um arquivo de configuração proprio, retorno pode
     * ser um array ou false indicando que nada foi encontrado.
     *
     * @return array|bool - array com linguagens encontradas ou false
     */
    public static function all()
    {
        if(is_dir(self::$dir))
        {
            $directory = dir(self::$dir);
            $archives = array();
            while(($archive = $directory->read()) !== false)
                if(file_exists(self::$dir . $archive) and !is_dir(self::$dir . $archive))
                    array_push($archives, str_replace(Settings::get('langExtension'), "", $archive));

            $directory->close();
            return $archives;
        }
        return false;
    }
}
