<?php

namespace web\widgets\qa;

/**
 * Sets up the environment to include comments
 */
class RequireComments extends \web\ext\Widget
{
    public function run()
    {
        $this->render('requireComments');
    }
}
