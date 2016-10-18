<?php
/**
 * Created by PhpStorm.
 * User: leonardo
 * Date: 28/09/16
 * Time: 13:32
 */

namespace LegionLab\Utils;


class SocialShare
{
    public static function whatsapp($msg, $useFontAwesome = false)
    {
        if(!$useFontAwesome) {
            return "whatsapp://send?text={$msg}";
        } else {
            return "<a target='_blank' href='whatsapp://send?text={$msg}'><i class='fa fa-whatsapp fa-2x'></i></a>";
        }
    }

    public static function facebook($msg, $useFontAwesome = false)
    {
        if(!$useFontAwesome) {
            return "http://www.facebook.com/sharer.php?u={$msg}";
        } else {
            return "<a target='_blank' class='fa fa-facebook fa-2x' href='http://www.facebook.com/sharer.php?u={$msg}'></a>";
        }
    }

    public static function twitter($msg, $url = null, $useFontAwesome = false)
    {
        if(!$useFontAwesome) {
            return "https://twitter.com/intent/tweet?source=tweetbutton&text={$msg}&url={$url}";
        } else {
            return "<a target='_blank' class='fa fa-twitter fa-2x' href='https://twitter.com/intent/tweet?source=tweetbutton&text={$msg}&url={$url}'></a>";
        }
    }

    public static function linkedin($url, $useFontAwesome = false)
    {
        if(!$useFontAwesome) {
            return "https://www.linkedin.com/cws/share?url={$url}";
        } else {
            return "<a target='_blank' class='fa fa-linkedin fa-2x' href='https://www.linkedin.com/cws/share?url={$url}'></a>";
        }
    }
}
