<?php

namespace Finetune\Finetune\Repositories\Helper;

interface HelperInterface
{
    public function buildTag($tag, $replace = array(), $delimiter = '-');

    public function limitWords($string, $wordLimit);

    public function limitCharacters($string, $length);

    public function reorderArray($unsorted, $typeOrder);

    public function reorderDate($unsorted, $typeOrder);

    // No breaking space to remove widows
    public function widow($title);

    public function dateFormatter($date, $formatString = null);

    public function getRouteName();
}