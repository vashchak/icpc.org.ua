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
     * List of available operations
     */
    const OP_ANSWER_CREATE          = 'answerCreate';
    const OP_ANSWER_READ            = 'answerRead';
    const OP_ANSWER_UPDATE          = 'answerUpdate';
    const OP_ANSWER_DELETE          = 'answerDelete';
    const OP_COACH_SET_STATUS       = 'coachSetStatus';
    const OP_COORDINATOR_SET_STATUS = 'coordinatorSetStatus';
    const OP_DOCUMENT_CREATE        = 'documentCreate';
    const OP_DOCUMENT_READ          = 'documentRead';
    const OP_DOCUMENT_UPDATE        = 'documentUpdate';
    const OP_DOCUMENT_DELETE        = 'documentDelete';
    const OP_NEWS_CREATE            = 'newsCreate';
    const OP_NEWS_READ              = 'newsRead';
    const OP_NEWS_UPDATE            = 'newsUpdate';
    const OP_RESULT_CREATE          = 'resultCreate';
    const OP_TEAM_CREATE            = 'teamCreate';
    const OP_TEAM_READ              = 'teamRead';
    const OP_TEAM_UPDATE            = 'teamUpdate';
    const OP_QUESTION_CREATE        = 'questionCreate';
    const OP_QUESTION_READ          = 'questionRead';
    const OP_QUESTION_UPDATE        = 'questionUpdate';
    const OP_QUESTION_DELETE        = 'questionDelete';

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
     * Biz rule for activating/suspending of coach
     *
     * @param array $params
     * @return bool
     */
    public function bizRuleCoachSetStatus(array $params)
    {
        $user = $params['user'];
        if ($this->checkAccess(User::ROLE_COORDINATOR_UKRAINE)) {
            return true;
        } elseif ($this->checkAccess(User::ROLE_COORDINATOR_REGION)) {
            return $this->_user->school->region === $user->school->region;
        } elseif ($this->checkAccess(User::ROLE_COORDINATOR_STATE)) {
            return $this->_user->school->state === $user->school->state;
        } else {
            return false;
        }
    }

    /**
     * Biz rule for activating/suspending of coordinators
     *
     * @param array $params
     * @return bool
     */
    public function bizRuleCoordinatorSetStatus(array $params)
    {
        $user = $params['user'];
        if ((string)$this->user->_id === (string)$user->_id) {
            return false;
        } elseif ($user->coordinator === User::ROLE_COORDINATOR_UKRAINE) {
            return $this->checkAccess(User::ROLE_COORDINATOR_UKRAINE);
        } elseif ($user->coordinator === User::ROLE_COORDINATOR_REGION) {
            return $this->checkAccess(User::ROLE_COORDINATOR_UKRAINE);
        } elseif ($user->coordinator === User::ROLE_COORDINATOR_STATE) {
            return $this->checkAccess(User::ROLE_COORDINATOR_REGION);
        } else {
            return false;
        }
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
     * Biz rule for edit team
     *
     * @param array $params
     * @return bool
     */
    public function bizRuleTeamUpdate(array $params)
    {
        return $this->checkAccess(User::ROLE_COACH);
    }

}
