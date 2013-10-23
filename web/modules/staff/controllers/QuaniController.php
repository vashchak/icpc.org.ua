<?php

namespace web\modules\staff\controllers;

use \common\models\Qa;

class QuaniController extends \web\modules\staff\ext\Controller
{
    /**
     * Create a question
     */
    public function actionCreate()
    {
        $question = new Qa\Question();
        if ($this->request->isPostRequest) {
            $this->applyChanges(
                $question,
                array(
                    'title' => $this->request->getParam('title', ''),
                    'content' => $this->request->getParam('content', ''),
                    'tagList' => explode(',', $this->request->getParam('tagList', array())),
                    'answerCount' => 0,
                )
            );
        }
        $this->render(
            'create',
            array(
                'question' => $question
            )
        );
    }

    /**
     * Update a question
     *
     * @param string $id mondoDB record key
     */
    public function actionUpdate($id)
    {
        $question = Qa\Question::model()->findByPk(new \MongoId($id));
        if ($this->request->isPostRequest) {
            $this->applyChanges(
                $question,
                array(
                    'title' => $this->request->getParam('title', ''),
                    'content' => $this->request->getParam('content', ''),
                    'tagList' => explode(',', $this->request->getParam('tagList', array())),
                )
            );
        }
        $this->render(
            'update',
            array(
                'question' => $question,
            )
        );
    }

    /**
     * Create an answer to the question
     *
     * @param string $id mondoDB record key
     */
    public function actionSaveAnswer($id)
    {
        $content = $this->request->getParam('content', '');
        $this->applyChanges(
            new Qa\Answer(),
            array(
                'content' => $content,
                'questionId' => $id,
            ),
            function() use ($id) {
                $question = Qa\Question::model()->findByPk(new \MongoId($id));
                $question->answerCount = intval($question->answerCount) + 1;
                try {
                    if (!$question->save()) {
                        $this->renderJson(array(
                            'errors' => $question->getErrors
                        ));
                    }
                } catch (\Exception $e) {
                    $this->renderJson(array(
                        'error' => array(
                            'common' => $e->getMessage()
                        )
                    ));
                }
            }
        );
    }
}