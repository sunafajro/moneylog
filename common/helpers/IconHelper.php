<?php

namespace common\helpers;

use yii\helpers\Html;

/**
 * Class IconHelper
 * @package common\components\helpers
 */
class IconHelper {
    /**
     * @param string $name
     * @param string|null $label
     *
     * @return string
     */
    public static function icon(string $name, string $label = null) : string
    {
        return Html::tag('i', '', ['class' => "fas fa-{$name}", 'aria-hidden' => 'true']) . ($label ? " {$label}" : '');
    }
}