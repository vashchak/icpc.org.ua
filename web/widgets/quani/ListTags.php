<?php

namespace web\widgets\quani;

/**
 * Renders Application Environment label for development and production
 */
class ListTags extends \web\ext\Widget
{
    public $tags;
    public $effects = true;
    protected $styles = array(
        'default',
        'primary',
        'success',
        'info',
        'warning',
        'danger',
    );

    public function run()
    {
        $this->render('listTags');
    }

    protected function colorize()
    {
        return $this->effects ? $this->styles[rand(0, 5)] : $this->styles[0];
    }
}