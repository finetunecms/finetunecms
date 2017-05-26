<?php

namespace Finetune\Finetune\Repositories\Helper;

class HelperRepository implements HelperInterface
{
    public function buildTag($tag, $replace = array(), $delimiter = '-')
    {
        if (!empty($replace)) {
            $tag = str_replace((array)$replace, ' ', $tag);
        }
        $tag = iconv('UTF-8', 'ASCII//TRANSLIT', $tag);
        $tag = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $tag);
        $tag = strtolower(trim($tag, '-'));
        $tag = preg_replace("/[\/_|+ -]+/", $delimiter, $tag);
        return $tag;
    }

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

    public function reorderArray($unsorted, $typeOrder)
    {
        $order = explode(':', $typeOrder);
        if ($order[1] == 'asc') {
            $arraySorted = $unsorted->sortBy($order[0]);
        } else {
            $arraySorted = $unsorted->sortByDesc($order[0]);
        }
        return $arraySorted;
    }

    public function reorderDate($unsorted, $typeOrder)
    {
        $order = explode(':', $typeOrder);
        if ($order[1] == 'asc') {
            usort($unsorted, function($a, $b) use($order){
                return \Carbon\Carbon::parse($a->publish_on)->timestamp - \Carbon\Carbon::parse($b->publish_on)->timestamp;
            });
        } else {
            usort($unsorted, function($a, $b) use($order){
                return \Carbon\Carbon::parse($b->publish_on)->timestamp - \Carbon\Carbon::parse($a->publish_on)->timestamp;
            });
        }
        return $unsorted;
    }

    // No breaking space to remove widows
    public function widow($title)
    {
        $title = preg_replace( '|([^\s])\s+([^\s]+)\s*$|', '$1&nbsp;$2', $title);
        return $title;
    }

    public function dateFormatter($date, $formatString = null){
        if(!empty($formatString)){
            $format = $formatString;
        }else{
            $format = config('finetune.date');
        }
        return \Carbon\Carbon::parse($date)->format($format);
    }

    public function getRouteName(){
        return explode('.',Route::currentRouteName())[0];
    }
}