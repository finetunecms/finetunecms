<?php

namespace Finetune\Finetune\Services\Helper;

use \Illuminate\Routing\Router;

class HelperService
{
    public function limitWords($string, $wordLimit)
    {
        $words = preg_split('/\s+/', $string);
        return implode(" ", array_splice($words, 0, $wordLimit));
    }

    public function limitCharacters($string, $length)
    {
        if (strlen($string) <= $length) {
            return $string;
        } else {
            $y = substr($string, 0, $length) . '...';
            return $y;
        }
    }

    // No breaking space to remove widows
    public function widow($title)
    {
        $title = preg_replace('|([^\s])\s+([^\s]+)\s*$|', '$1&nbsp;$2', $title);
        return $title;
    }

    public function dateFormatter($date, $formatString = null)
    {
        if (!empty($formatString)) {
            $format = $formatString;
        } else {
            $format = config('finetune.date');
        }
        return \Carbon\Carbon::parse($date)->format($format);
    }

    public function addGaData($string)
    {
        $doc = new \DOMDocument();
        $doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $string);
        $links = $doc->getElementsByTagName('a');
        foreach ($links as $item) {
            if (!$item->hasAttribute('ga-on'))
                $item->setAttribute('ga-on', 'click');
            $item->setAttribute('ga-event-category', 'link');
            $item->setAttribute('ga-event-action', 'click');
            $item->setAttribute('ga-event-label', $item->getAttribute('href'));
        }
        $content = $doc->saveHTML($doc->documentElement);;

        return $content;
    }

}