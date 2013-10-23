<?php

namespace web\modules\staff\controllers;

use \common\models\Qa\Tag;

class TagsController extends \web\modules\staff\ext\Controller
{
    public function init()
    {
        parent::init();
        $this->defaultAction = 'all';
        $this->setNavActiveItem('main', 'tags');
    }

    public function actionAll()
    {
        $this->render('all', array(
            'tags' => Tag::model()->findAll()
        ));
    }

    public function actionIndex()
    {
        $this->forward('/staff/tags/all');
    }

    public function actionCreate()
    {
        $name = $this->request->getPost('name', '');
        $desc = $this->request->getPost('desc', '');
        if ($this->request->isPostRequest) {
            $this->applyChanges(
                new Tag,
                array(
                    'name' => $name,
                    'desc' => strip_tags($desc),
                )
            );
        }
        $this->render('create');
    }

    public function actionDelete($id)
    {
        $tag = Tag::model()->findByPk(new \MongoId($id));
        try {
            $this->renderJson(array(
                'result' => $tag->delete()
            ));
        } catch (\Exception $e) {
            $this->renderJson(array(
                'result' => false
            ));
        }
    }
}
