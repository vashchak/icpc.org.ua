<?php

namespace web\widgets\qa;

/**
 * Renders Application Environment label for development and production
 */
class ListTags extends \web\ext\Widget
{
    public $tags;
    public $effects = true;
    public $mode = 'inline';
    public $tableRows = 4;
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
        switch ($this->mode) {
            case 'inline':
                $this->render('listTags_inline');
                break;
            case 'table':
                $this->render('listTags_table');
                break;
            default:
                $this->render('listTags_inline');
        }
    }

    protected function colorize()
    {
        return $this->effects ? $this->styles[rand(0, 5)] : $this->styles[0];
    }
}