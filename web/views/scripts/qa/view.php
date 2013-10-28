<? \yii::app()->getClientScript()->registerCoreScript('ckeditor'); ?>
<? $this->widget('\web\widgets\qa\RequireComments'); ?>

<a class="btn btn-warning" href="/qa/update/<?= (string)$question->_id; ?>"><?= \yii::t('app', 'Edit'); ?></a>
<br/>
<br/>
<div class="panel panel-primary">
    <div class="panel-heading"><?= \CHtml::encode($question->title); ?></div>
    <div class="panel-body"><?= $question->content; ?></div>
    <div class="panel-footer text-muted">
        <div class="row">
            <div class="col-md-6 text-left">
                <? $this->widget('\web\widgets\qa\ListTags', array('tags' => $question->tagList)); ?>
            </div>
            <div class="col-md-6 text-right">
                <em><?= $question->getAuthor()->fio(); ?></em>
                &nbsp;
                <span class="text-muted"><?= date('Y-m-d H:i:s', $question->dateCreated); ?></span>
            </div>
        </div>
    </div>
</div>

<? $this->widget('\web\widgets\qa\ListComments', array(
    'comments' => $question->comments,
    'entity' => array(
        'entity' => 'question',
        'id' => (string)$question->_id
    )
)); ?>

<? if ($question->answerCount): ?>
    <h3><?= \yii::t('app', '{count} Answers', array('{count}' => $question->answerCount)); ?></h3>
    <hr/>
    <? foreach ($answers as $answer): ?>
        <div class="row">
            <div class="col-xs-14 col-md-12">
                <div class="panel <?= $answer->getAuthor()->isApprovedCoordinator ? 'panel-success' : 'panel-default'; ?>">
                    <div class="panel-heading"><?= $answer->getAuthor()->fio(); ?></div>
                    <div class="panel-body"><?= $answer->content; ?></div>
                    <div class="panel-footer text-muted"><?= date('Y-m-d H:i:s', $answer->dateCreated); ?></div>
                </div>
            </div>
        </div>
        <? $this->widget('\web\widgets\qa\ListComments', array(
            'comments' => $answer->comments,
            'entity' => array(
                'entity' => 'answer',
                'id' => (string)$answer->_id
            )
        )); ?>
        <br/>
    <? endforeach; ?>
<? else: ?>
    <div class="row">
        <div class="col-xs-14 col-md-12">
            <h4><?= \yii::t('app', 'There isn\'t a single answer'); ?></h4>
        </div>
    </div>
    <br/>
<? endif; ?>

<div class="row">
    <div class="col-xs-14 col-md-12 answer-container">
        <div class="form-horizontal clearfix">
            <div class="form-group">
                <textarea id="answer-content" name="content"></textarea>
            </div>
        </div>
        <br/>
        <button type="button" class="btn btn-primary answer-create">
            <?= \yii::t('app', \yii::app()->user->isGuest ? 'Login' : 'Submit'); ?>
        </button>
    </div>
</div>

<script>
    app["question"] = {
        "id":"<?= (string)$question->_id; ?>"
    };
    app["user"] = {
        "fio":"<?= \yii::app()->user->getInstance()->fio(); ?>"
    };
    $(document).ready(function() {
        new appQaView();
    });
</script>
