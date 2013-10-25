<?php

namespace common\models\Qa;

class CommentMapper
{
    /**
     * Comment content
     * @var string[15..500]
     */
    protected $content;

    /**
     * Author info
     * @var array('fio' => '', 'email' => '')
     */
    protected $author;

    /**
     * Date of the creation
     * @var unix_timestamp
     */
    protected $dateCreated;

    protected $errors = array();

    public function __construct()
    {
        $this->errors = array();
        $this->setAuthor()
            ->setCreationDate();
    }

    public function setContent($content)
    {
        if (strlen($content) < 15 || strlen($content) > 500) {
            $this->addError('content', \yii::t('app', 'Content length is not appropriate. Should be [15..500]'));
        } else {
            $this->content = $content;
        }
        return $this;
    }

    public function setAuthor()
    {
        if (\yii::app()->user->isGuest) {
            $this->addError('author', \yii::t('app', 'In order to leave comments, you should be authenticated.'));
            \yii::app()->user->returnUrl = \yii::app()->createUrl(
                '/' . \yii::app()->controller->id
            );
        } else {
            $this->author = array(
                'fio' => \yii::app()->user->getInstance()->fio(),
                'email' => \yii::app()->user->getInstance()->email
            );
        }
        return $this;
    }

    public function setCreationDate()
    {
        $this->dateCreated = time();
        return $this;
    }

    public function getData()
    {
        return array(
            'content' => $this->content,
            'author' => $this->author,
            'dateCreated' => $this->dateCreated
        );
    }

    public function addError($attribute, $error)
    {
        $this->errors[$attribute] = $error;
    }

    public function addErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors($attribute = null)
    {
        if (is_null($attribute)) {
            return (bool)count($this->errors);
        } else {
            return isset($this->errors[$attribute]);
        }
    }
}
