<?php

namespace web\widgets;

class BreadCrumbs extends \web\ext\Widget
{
    public $crumbs = array();
    public $active;
    protected $default = array(
        'Main page' => '/'
    );

    public function init()
    {
        parent::init();
        $this->initCrumbs();
        $this->active = $this->active ?: $this->controller->action->id;
    }

    public function run()
    {
        $this->render('breadCrumbs');
    }

    protected function initCrumbs()
    {
        $c = \yii::app()->params['crumbs'][\yii::app()->controller->id];
        $alt = isset($c) ? array_merge($this->default, $c) : $this->default;
        $this->crumbs = count($this->crumbs) ? $this->crumbs : $alt;
    }
}
