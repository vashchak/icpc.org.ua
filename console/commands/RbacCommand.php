<?php

use \common\ext\MongoDb\Auth,
    \common\models\User;

class RbacCommand extends \console\ext\ConsoleCommand
{

    /**
     * Auth manager
     * @var \common\ext\MongoDb\Auth\Manager
     */
    public $auth;

    /**
     * Init
     */
    public function init()
    {
        parent::init();

        $this->auth = \yii::app()->authManager;
    }

    /**
     * Init RBAC roles, tasks and operations
     */
    public function actionInit()
    {
        /**
         * Delete operations and tasks
         */
        $criteria = new \EMongoCriteria();
        $criteria->addCond('type', 'in', array(\CAuthItem::TYPE_OPERATION, \CAuthItem::TYPE_TASK));
        Auth\Item::model()->deleteAll($criteria);
        $this->auth->init();

        /**
         * Create operations
         */
        $this->_operationsDocument();
        $this->_operationsNews();
        $this->_operationsQuani();

        /**
         * Guest role
         */
        $guest = $this->auth->getAuthItem(User::ROLE_GUEST);
        if (!$guest) {
            $guest = $this->auth->createRole(User::ROLE_GUEST);
        }
        $guestOperationList = array(
            'documentRead',
            'newsRead',
            'questionRead'
        );
        $this->_assignOperations($guest, $guestOperationList);

        /**
         * User role
         */
        $user = $this->auth->getAuthItem(User::ROLE_USER);
        if (!$user) {
            $user = $this->auth->createRole(User::ROLE_USER);
        }
        $userOperationList = array(
            User::ROLE_GUEST,
            'questionCreate',
            'questionUpdateOwn'
        );
        $this->_assignOperations($user, $userOperationList);

        /**
         * Student role
         */
        $student = $this->auth->getAuthItem(User::ROLE_STUDENT);
        if (!$student) {
            $student = $this->auth->createRole(User::ROLE_STUDENT);
        }
        $studentOperationList = array(
            User::ROLE_USER,
        );
        $this->_assignOperations($student, $studentOperationList);

        /**
         * Coach role
         */
        $coach = $this->auth->getAuthItem(User::ROLE_COACH);
        if (!$coach) {
            $coach = $this->auth->createRole(User::ROLE_COACH);
        }
        $coachOperationList = array(
            User::ROLE_STUDENT,
            'questionUpdate',
            'answerCreate'
        );
        $this->_assignOperations($coach, $coachOperationList);

        /**
         * Coordinator role
         */
        $coordinator = $this->auth->getAuthItem(User::ROLE_COORDINATOR);
        if (!$coordinator) {
            $coordinator = $this->auth->createRole(User::ROLE_COORDINATOR);
        }
        $coordinatorOperationList = array(
            User::ROLE_COACH,
            'documentCreate',
            'documentUpdate',
            'newsCreate',
            'newsUpdate',
        );
        $this->_assignOperations($coordinator, $coordinatorOperationList);

        /**
         * Coordinator of Ukraine role
         */
        $coordinatorUkraine = $this->auth->getAuthItem(User::ROLE_COORDINATOR_UKRAINE);
        if (!$coordinatorUkraine) {
            $coordinatorUkraine = $this->auth->createRole(User::ROLE_COORDINATOR_UKRAINE);
        }
        $coordinatorUkraineOperationList = array(
            User::ROLE_COORDINATOR,
        );
        $this->_assignOperations($coordinatorUkraine, $coordinatorUkraineOperationList);

        /**
         * Admin role
         */
        $admin = $this->auth->getAuthItem(User::ROLE_ADMIN);
        if (!$admin) {
            $admin = $this->auth->createRole(User::ROLE_ADMIN);
        }
        $adminOperationList = array(
            User::ROLE_COORDINATOR_UKRAINE,
        );
        $this->_assignOperations($admin, $adminOperationList);

        echo "RBAC inited succesfully.";
    }

    /**
     * Assign the list of given operation to the given role
     *
     * @param \CAuthItem $authItem
     * @param array $operationList
     */
    protected function _assignOperations(\CAuthItem $authItem, array $operationList)
    {
        foreach ($operationList as $operation) {
            if (!$authItem->hasChild($operation)) {
                $authItem->addChild($operation);
            }
        }
    }

    /**
     * Quani operations
     *
     * @return void
     */
    protected function _operationsQuani()
    {
        $bizRuleQuestionRead      = 'return \yii::app()->rbac->bizRuleQuestionRead($params);';
        $bizRuleQuestionUpdate    = 'return \yii::app()->rbac->bizRuleQuestionUpdate($params);';
        $bizRuleQuestionCreate    = 'return \yii::app()->rbac->bizRuleQuestionCreate($params);';
        $bizRuleUpdateOwnQuestion = 'return \yii::app()->rbac->bizRuleUpdateOwnQuestion($params);';
        $bizRuleAnswerCreate      = 'return \yii::app()->rbac->bizRuleAnswerCreate($params);';
        $this->auth->createOperation('questionRead', 'Read question', $bizRuleQuestionRead);
        $this->auth->createOperation('questionUpdate', 'Update question', $bizRuleQuestionUpdate);
        $this->auth->createOperation('questionCreate', 'Create question', $bizRuleQuestionCreate);
        $this->auth->createOperation('questionUpdateOwn', 'Update own question', $bizRuleUpdateOwnQuestion);
        $this->auth->createOperation('answerCreate', 'Create answer', $bizRuleAnswerCreate);
    }

    /**
     * Document operations
     *
     * @return void
     */
    protected function _operationsDocument()
    {
        $bizRuleRead   = 'return \yii::app()->rbac->bizRuleDocumentRead($params);';
        $bizRuleUpdate = 'return \yii::app()->rbac->bizRuleDocumentUpdate($params);';

        $this->auth->createOperation('documentCreate', 'Create document');
        $this->auth->createOperation('documentRead', 'Read document', $bizRuleRead);
        $this->auth->createOperation('documentUpdate', 'Edit document', $bizRuleUpdate);
    }

    /**
     * News operations
     *
     * @return void
     */
    protected function _operationsNews()
    {
        $bizRuleRead   = 'return \yii::app()->rbac->bizRuleNewsRead($params);';
        $bizRuleUpdate = 'return \yii::app()->rbac->bizRuleNewsUpdate($params);';

        $this->auth->createOperation('newsCreate', 'Create news');
        $this->auth->createOperation('newsRead', 'Read news', $bizRuleRead);
        $this->auth->createOperation('newsUpdate', 'Edit news', $bizRuleUpdate);
    }

    /**
     * Create first admin user
     *
     * @return void
     */
    public function actionInitAdmin()
    {
        // Define admin params
        $email      = \yii::app()->params['rbac']['admin']['email'];
        $password   = \yii::app()->params['rbac']['admin']['password'];

        // Save admin to DB
        $admin = new \common\models\User();
        $admin->setAttributes(array(
            'firstName' => 'Root',
            'lastName'  => 'Admin',
            'email'     => $email,
            'role'      => User::ROLE_ADMIN,
        ), false);
        $admin->setPassword($password, $password);
        $admin->save();

        // Assign admin role
        $this->auth->assign(User::ROLE_ADMIN, $admin->_id);

        // Display admin params
        if ($admin->hasErrors()) {
            echo "Can't create admin user. Details are below.\n";
            var_dump($admin->getErrors());
        } else {
            echo "Email: $email\n";
            echo "Password: $password\n";
        }
    }
}
