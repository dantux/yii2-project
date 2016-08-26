<?php

namespace dantux\controllers;

use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 */
class ConsoleController extends Controller
{

    public static function makeSlug($string)
    {
        $badChars = ['/[^a-zA-Z0-9-]/'];
        $first_iteration = preg_replace($badChars, '-', $string);
        $second_iteration = preg_replace('/-+/', '-', $first_iteration);
        echo strtolower($second_iteration);
    }


}

