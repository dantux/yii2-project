<?php

use \Yii;

namespace dantux\project\helpers;

class Unit
{
    public static function lb_to_kg($lb = 1)
    {
        return $lb * 0.453592;
    }

    public static function kg_to_lb($kg = 1)
    {
        return $kg * 2.20462;
    }
}
