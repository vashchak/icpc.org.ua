<?php

namespace web\controllers;

use \common\models\Qa;

class QuaniController extends \web\ext\Controller
{
    public function init()
    {
        parent::init();
        $this->defaultAction = 'latest';
        $this->setNavActiveItem('main', 'quani');
    }

    public function actionLatest()
    {
        $criteria = new \EMongoCriteria;
        $criteria->sort('dateCreated', \EMongoCriteria::SORT_DESC);
        $pages = new \CPagination(Qa\Question::model()->count());
        $pages->pageSize = 10;
        $q = Qa\Question::model()->findAll($criteria);
        $this->render(
            'index',
            array(
                'q' => $q,
                'pages' => $pages
            )
        );
    }

    public function actionView($id)
    {
        $question = Qa\Question::model()->findByPk(new \MongoId($id));

        $criteria = new \EMongoCriteria;
        $criteria
            ->addCond('questionId', '==', (string)$question->_id)
            ->setSort(array(
                'dateCreated' => \EMongoCriteria::SORT_DESC,
            ));
        $answers = Qa\Answer::model()->findAll($criteria);

        $this->render(
            'view',
            array(
                'question' => $question,
                'answers' => $answers ?: array(),
            )
        );
    }

    public function actionGetTags()
    {
        $q = mb_strtolower($this->request->getParam('q', ''));
        $page_limit = $this->request->getParam('page_limit', 10);
        $conditions = new \EMongoCriteria();
        $conditions->name = new \MongoRegex("/^" . preg_quote($q) . "/");
        $conditions->setLimit($page_limit);
        $conditions->setSort(array(
            'name' => \EMongoCriteria::SORT_ASC,
            'dateCreated' => \EMongoCriteria::SORT_ASC
        ));
        $tags = Qa\Tag::model()->findAll($conditions);
        $this->renderJson(
            array(
                'tags' => $this->simplifyData($tags),
            )
        );
    }

    protected function simplifyData($data)
    {
        $res = array();
        if (!$data) {
            return array();
        }
        foreach ($data as $key => $value) {
            $res[] = array(
                //'id' => (string)$value->_id,
                'id' => $value->name,
                'text' => $value->name
            );
        }
        return $res;
    }
}
