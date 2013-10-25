<?php

namespace web\widgets\qa;

/**
 * Comments renderer widget
 */
class ListComments extends \web\ext\Widget
{
    public $comments;
    public $entity;

    public function run()
    {
        $this->render('listComments');
    }
}
