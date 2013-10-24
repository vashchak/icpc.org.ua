<?php

namespace common\components;

use \common\models\User;

/**
 * Implements checkAccess for $this->user
 *
 * @property User $user
 */
class Rbac extends \CApplicationComponent
{

    /**
     * Current user
     * @var User
     */
    protected $_user;

    /**
     * Set current user
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Returns current user
     *
     * @return User
     */
    public function getUser()
    {
        if ($this->_user === null) {
            if (\yii::app()->getComponent('user') !== null) {
                $this->setUser(\yii::app()->user->getInstance());
            } else {
                $this->setUser(new User());
            }
        }
        return $this->_user;
    }

    /**
     * Check access shortcut
     *
     * @param string $operation
     * @param array $params
     * @return bool
     */
    public function checkAccess($operation, array $params = array())
    {
        return \yii::app()->authManager->checkAccess($operation, (string)$this->user->_id, $params);
    }

    /**
     * Biz rule for viewing and downloading document
     *
     * @param array $params
     * @return bool
     */
    public function bizRuleDocumentRead(array $params)
    {
        return true;
    }

    /**
     * Biz rule for edit document
     *
     * @param array $params
     * @return bool
     */
    public function bizRuleDocumentUpdate(array $params)
    {
        return $this->checkAccess('admin');
    }

    /**
     * Biz rule for reading news
     *
     * @param array $params
     * @return bool
     */
    public function bizRuleNewsRead(array $params)
    {
        $news = $params['news'];
        if ($news->isPublished) {
            return true;
        } else {
            return $this->bizRuleNewsUpdate($params);
        }
    }

    /**
     * Biz rule for edit news
     *
     * @param array $params
     * @return bool
     */
    public function bizRuleNewsUpdate(array $params)
    {
        return $this->checkAccess(User::ROLE_COORDINATOR_STATE);
    }

    /**
     * Question read rule
     *
     * @param array $params
     * @return boolean
     */
    public function bizRuleQuestionRead(array $params)
    {
        return true;
    }

    /**
     * Question update rule
     *
     * @param array $params
     * @return boolean
     */
    public function bizRuleQuestionUpdate(array $params)
    {
        return $this->checkAccess('admin');
    }

    /**
     * Question create rule
     *
     * @param array $params
     * @return boolean
     */
    public function bizRuleQuestionCreate(array $params)
    {
        return !\yii::app()->user->isGuest;
    }

    /**
     * Question update by author
     *
     * @param array $params
     * @return boolean
     */
    public function bizRuleUpdateOwnQuestion(array $params)
    {
        $q = \common\models\Qa\Question::model()->findByPk(
            new \MongoId(\yii::app()->request->getParam('id', ''))
        );
        return (\yii::app()->user->id == (string)$q->userId) || $this->checkAccess('admin');
    }

    /**
     * Answer update rule
     *
     * @param array $params
     * @return boolean
     */
    public function bizRuleAnswerCreate(array $params)
    {
        return !\yii::app()->user->isGuest;
    }
}
