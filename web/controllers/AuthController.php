<?php

namespace web\controllers;

use \common\models\School;
use \common\models\User;

class AuthController extends \web\ext\Controller
{

    /**
     * Check recaptcha code
     *
     * @return bool|string
     */
    protected function _checkRecaptcha()
    {
        $challengeField = $this->request->getParam('recaptcha_challenge_field');
        $responseField  = $this->request->getParam('recaptcha_response_field');
        $errorMessage   = \yii::t('app', 'The recaptcha code is incorrect.');

        if (($responseField === null) || ($challengeField === null)) {
            return $errorMessage;
        }

        \yii::import('common.lib.recaptcha.reCAPTCHA.recaptchalib', true);
        $response = recaptcha_check_answer(
            \yii::app()->params['recaptcha']['privateKey'],
            \yii::app()->request->userHostAddress,
            $challengeField,
            $responseField
        );

        if ($response->is_valid) {
            return true;
        } else {
            return $errorMessage;
        }
    }

    /**
     * Init
     */
    public function init()
    {
        parent::init();

        // Set default action
        $this->defaultAction = 'login';

        // Set active main menu item
        $this->setNavActiveItem('main', '');
    }

    /**
     * Before action
     *
     * @param \CAction $action
     * @return bool
     */
    protected function beforeAction($action)
    {
        if (!parent::beforeAction($action)) return false;

        // Take away loggedin users
        if ((!\yii::app()->user->isGuest) && ($this->action->id !== 'logout')) {
            return $this->redirect('/');
        }

        return true;
    }

    /**
     * Login page
     */
    public function actionLogin()
    {
        // Get params
        $email      = $this->request->getPost('email');
        $password   = $this->request->getPost('password');

        // Login
        $error = '';
        if ($this->request->isPostRequest) {
            $identity = new \web\ext\UserIdentity($email, $password);
            if ($identity->authenticate()) {
                \yii::app()->user->login($identity);
                return $this->redirect('/');
            } else {
                $error = $identity->errorMessage;
            }
        }

        // Render view
        $this->render('login', array(
            'email' => $email,
            'error' => $error,
        ));
    }

    /**
     * Password reset page
     */
    public function actionPasswordReset()
    {
        // Render view
        $this->render('passwordReset');
    }

    /**
     * Password reset sent success page
     */
    public function actionPasswordResetSent()
    {
        // Render view
        $this->render('passwordResetSent');
    }

    /**
     * Send email with password reset URL
     */
    public function actionPasswordResetSendEmail()
    {
        // Get params
        $email = $this->request->getParam('email');

        // Get user by email
        $userExists = (User::model()->countByAttributes(array(
            'email' => mb_strtolower($email),
        )) > 0);

        // Check recaptcha
        $recaptchaStatus = $this->_checkRecaptcha();

        // Form errors
        $errors = array();
        if (!$userExists) {
            $errors['email'] = \yii::t('app', 'We do not know such a email.');
        }
        if ($recaptchaStatus !== true) {
            $errors['recaptcha'] = $recaptchaStatus;
        }

        // Send password reset email
        if (count($errors) === 0) {

            // Get reset token
            $criteriaResetToken = new \EMongoCriteria();
            $criteriaResetToken->addCond('email', '==', $email);
            User\PasswordReset::model()->deleteAll($criteriaResetToken);
            $resetToken = new User\PasswordReset();
            $resetToken->email = $email;
            $resetToken->save();

            // Send email
            $message = new \common\ext\Mail\MailMessage();
            $message
                ->addTo($email)
                ->setFrom(\yii::app()->params['emails']['noreply']['address'], \yii::app()->params['emails']['noreply']['name'])
                ->setSubject(\yii::t('app', 'Password reset'))
                ->setView('passwordReset', array(
                    'link' => $this->createAbsoluteUrl('/auth/passwordResetEnterNew', array(
                        'token' => (string)$resetToken->_id,
                    )),
                ));
            \yii::app()->mail->send($message);
        }

        // Render json
        $this->renderJson(array(
            'errors' => count($errors) ? $errors : false,
        ));
    }

    /**
     * Enter new password page
     */
    public function actionPasswordResetEnterNew()
    {
        // Get params
        $tokenId = $this->request->getParam('token');

        // Get token record
        $token = User\PasswordReset::model()->findByPk(new \MongoId($tokenId));

        // Render view
        if (($token === null) || (!$token->isValid)) {
            $this->render('passwordResetTokenError');
        } else {
            $this->render('passwordResetEnterNew', array(
                'token' => $token,
            ));
        }
    }

    /**
     * Set new password
     */
    public function actionPasswordResetSetNew()
    {
        // Get params
        $tokenId        = $this->request->getParam('token');
        $password       = $this->request->getParam('password');
        $passwordRepeat = $this->request->getParam('passwordRepeat');

        // Get and check token
        $token = User\PasswordReset::model()->findByPk(new \MongoId($tokenId));
        if (($token === null) || (!$token->isValid)) {
            return;
        }

        // Get user
        $user = User::model()->findByAttributes(array(
            'email' => $token->email,
        ));

        // Set new password and login user
        $user->setPassword($password, $passwordRepeat);

        // Save to DB
        if (!$user->hasErrors()) {

            // Save new password, no validation
            $user->save(false);

            // Authenticate user
            $identity = new \web\ext\UserIdentity($token->email, $password);
            $identity->authenticate();
            \yii::app()->user->login($identity);

            // Delete token
            $token->delete();
        }

        // Render json
        $this->renderJson(array(
            'errors' => $user->hasErrors() ? $user->getErrors() : false,
        ));
    }

    /**
     * Logout
     */
    public function actionLogout()
    {
        \yii::app()->user->logout();
        return $this->redirect('/');
    }

    /**
     * Confirm email page
     */
    public function actionEmailConfirm()
    {
        // Get params
        $token = \yii::app()->request->getParam('token');
        if (empty($token)) {
            $this->httpException(400);
        }

        // Confirm email
        $emailConfirmation = User\EmailConfirmation::model()->findByPk(new \MongoId($token));
        if ($emailConfirmation !== null) {
            $user = User::model()->findByPk(new \MongoId($emailConfirmation->userId));
            $user->isEmailConfirmed = true;
            $user->save();
            $emailConfirmation->delete();
        }

        // Render view
        $this->render('emailConfirm');
    }

    /**
     * Signup page
     */
    public function actionSignup()
    {
        // Get params
        $firstNameUk    = $this->request->getPost('firstNameUk');
        $middleNameUk   = $this->request->getPost('middleNameUk');
        $lastNameUk     = $this->request->getPost('lastNameUk');
        $email          = mb_strtolower($this->request->getPost('email'));
        $password       = $this->request->getPost('password');
        $passwordRepeat = $this->request->getPost('passwordRepeat');
        $type           = $this->request->getPost('type');
        $coordinator    = $this->request->getPost('coordinator');
        $schoolId       = $this->request->getPost('schoolId');
        $rulesAgree     = (bool)$this->request->getPost('rulesAgree');

        // Set attributes
        $user = new User();
        $user->setAttributes(array(
            'firstNameUk'   => $firstNameUk,
            'middleNameUk'  => $middleNameUk,
            'lastNameUk'    => $lastNameUk,
            'email'         => $email,
            'type'          => $type,
            'coordinator'   => $coordinator,
            'schoolId'      => $schoolId,
        ), false);

        // Register a new user
        if ($this->request->isPostRequest) {

            // Validate user date
            $user->validate();
            $user->setPassword($password, $passwordRepeat);

            // Check rules to be accepted
            if (!$rulesAgree) {
                $user->addError('rulesAgree', \yii::t('app', 'Please, read and accept service rules'));
            }

            // Check recaptcha in the very end
            if (!$user->hasErrors()) {
                $recaptchaStatus = $this->_checkRecaptcha();
                if ($recaptchaStatus !== true) {
                    $user->addError('recaptcha', $recaptchaStatus);
                }
            }

            // If no errors, than create and auth user
            if (!$user->hasErrors()) {

                // Save user
                $user->save();

                // Assign student role
                if ($user->type === User::ROLE_STUDENT) {
                    \yii::app()->authManager->assign(User::ROLE_STUDENT, $user->_id);
                }

                // Create an email confirmation record
                $emailConfirmation = new User\EmailConfirmation();
                $emailConfirmation->userId = $user->_id;
                $emailConfirmation->save();

                // Send email
                $message = new \common\ext\Mail\MailMessage();
                $message
                    ->addTo($user->email)
                    ->setFrom(\yii::app()->params['emails']['noreply']['address'], \yii::app()->params['emails']['noreply']['name'])
                    ->setSubject(\yii::t('app', '{app} Email confirmation', array('{app}' => \yii::app()->name)))
                    ->setView('emailConfirmation', array(
                        'link' => $this->createAbsoluteUrl('/auth/emailConfirm', array(
                            'token' => (string)$emailConfirmation->_id,
                        )),
                    ));
                \yii::app()->mail->send($message);

                // Save user settings
                $settings = $user->settings;
                $settings->setAttributes(array(
                    'geo'   => $this->getGeo(),
                    'year'  => $this->getYear(),
                    'lang'  => $this->request->cookies['language']->value,
                ), false);
                $settings->save();

                // Authenticate user
                $identity = new \web\ext\UserIdentity($email, $password);
                $identity->authenticate();
                \yii::app()->user->login($identity);
            }

            // Render json
            $this->renderJson(array(
                'errors'    => $user->hasErrors() ? $user->getErrors() : false,
                'url'       => $user->hasErrors() ? '' : $this->createUrl('signedup', array('id' => $emailConfirmation->_id)),
            ));
        }

        // Render page
        else {

            // Get list of schools
            $criteria = new \EMongoCriteria();
            $criteria->sort('fullNameUk', \EMongoCriteria::SORT_ASC);

            // Render view
            $this->render('signup', array(
                'user'              => $user,
                'password'          => $password,
                'passwordRepeat'    => $passwordRepeat,
                'rulesAgree'        => $rulesAgree,
            ));
        }
    }

    /**
     * After signup page
     */
    public function actionSignedUp()
    {
        // Get params
        $confirmationId = $this->request->getParam('id');

        if ($confirmationId !== null) {
            $confirmation = User\EmailConfirmation::model()->findByPk(new \MongoId($confirmationId));
            if ($confirmation !== null) {
                $this->render('signedup', array(
                    'confirmation' => $confirmation,
                ));
            } else {
                $this->httpException(404);
            }
        } else {
            return $this->redirect('/');
        }
    }

    /**
     * Resend email confirmation action
     */
    public function actionResendEmailConfirmation()
    {
        // Get params
        $confirmationId = $this->request->getParam('confirmationId');

        // Get confirmation
        $confirmation = User\EmailConfirmation::model()->findByPk(new \MongoId($confirmationId));
        if ($confirmation === null) {
            $this->httpException(404);
        }

        // Get user
        $user = User::model()->findByPk(new \MongoId($confirmation->userId));
        if ($user === null) {
            $this->httpException(404);
        }

        // Send email
        $message = new \common\ext\Mail\MailMessage();
        $message
            ->addTo($user->email)
            ->setFrom(\yii::app()->params['emails']['noreply']['address'], \yii::app()->params['emails']['noreply']['name'])
            ->setSubject(\yii::t('app', '{app} Email confirmation', array('{app}' => \yii::app()->name)))
            ->setView('emailConfirmation', array(
                'link' => $this->createAbsoluteUrl('/auth/emailConfirm', array(
                    'token' => (string)$confirmation->_id,
                )),
            ));
        \yii::app()->mail->send($message);
    }

    /**
     * Action to get the list of schools for signup
     */
    public function actionSchools()
    {
        // Get params
        $query = $this->request->getParam('q');

        // Get schools
        $criteria = new \EMongoCriteria();
        $criteria->addCond('fullNameUk', '==', new \MongoRegex('/' . preg_quote($query) . '/i'));
        $schools = School::model()->findAll($criteria);

        // Create json list of schools
        $schoolsJson = array();
        foreach ($schools as $school) {
            $schoolsJson[] = array(
                'id'    => (string)$school->_id,
                'text'  => $school->fullNameUk
            );
        }

        // Render json
        $this->renderJson($schoolsJson);
    }
}
