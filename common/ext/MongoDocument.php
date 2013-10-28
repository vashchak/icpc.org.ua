<?php

namespace common\ext;

abstract class MongoDocument extends \common\ext\MongoDb\Document
{
    protected function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->userId = $this->userId ?: \yii::app()->user->id;
        $this->userId = (string)$this->userId;

        if ($this->dateCreated == null) {
            $this->dateCreated = time();
        }

        return true;
    }
}
