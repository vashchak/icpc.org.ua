<?php

namespace common\models\Qa;

class Question extends \common\ext\MongoDocument
{
    /**
     * User ID
     * @var string
     */
    public $userId;

    /**
     * Title
     * @var string
     */
    public $title;

    /**
     * Content
     * @var string
     */
    public $content;

    /**
     * List of assigned tags
     * @var array
     */
    public $tagList = array();

    /**
     * List of comments
     * @var array
     */
    public $comments = array();

    /**
     * Count of given answers
     * @var int
     * @see Answer::afterSave()
     */
    public $answerCount = 0;

    /**
     * Date created
     * @var int
     */
    public $dateCreated;

    /**
     * Author
     * @var User
     */
    protected $_user;

    /**
     * Returns the attribute labels.
     *
     * Note, in order to inherit labels defined in the parent class, a child class needs to
     * merge the parent labels with child labels using functions like array_merge().
     *
     * @return array attribute labels (name => label)
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'userId'        => \yii::t('app', 'User ID'),
            'title'         => \yii::t('app', 'Title'),
            'content'       => \yii::t('app', 'Content'),
            'tagList'       => \yii::t('app', 'Assigned tags'),
            'answerCount'   => \yii::t('app', 'Answer count'),
            'dateCreated'   => \yii::t('app', 'Registration date'),
        ));
    }

    /**
     * Returns the post author
     *
     * @return User
     */
    public function getAuthor()
    {
        if ($this->_user === null) {
            $this->_user = \common\models\User::model()->findByPk(new \MongoId($this->userId));
        }
        return $this->_user;
    }

    /**
     * Define attribute rules
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('userId, title, content, tagList, dateCreated', 'required'),
            array('title', 'length', 'max' => 300),
            array('content', 'length', 'max' => 5000),
        ));
    }

	/**
	 * This returns the name of the collection for this class
     *
     * @return string
	 */
	public function getCollectionName()
	{
		return 'qa.question';
	}

    /**
     * List of collection indexes
     *
     * @return array
     */
    public function indexes()
    {
        return array_merge(parent::indexes(), array(
            'dateCreated_tagList' => array(
                'key' => array(
                    'dateCreated'   => \EMongoCriteria::SORT_DESC,
                    'tagList'       => \EMongoCriteria::SORT_ASC,
                ),
            ),
            'userId_dateCreated' => array(
                'key' => array(
                    'userId'        => \EMongoCriteria::SORT_ASC,
                    'dateCreated'   => \EMongoCriteria::SORT_DESC,
                ),
            ),
        ));
    }
}
