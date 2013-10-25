<?php \yii::app()->getClientScript()->registerCoreScript('ckeditor'); ?>
<?php \yii::app()->getClientScript()->registerCoreScript('underscore'); ?>

<a class="btn btn-warning" href="/qa/update/<?php echo (string)$question->_id; ?>"><?php echo \yii::t('app', 'Edit'); ?></a>
<br/>
<br/>
<div class="panel panel-primary">
    <div class="panel-heading"><?php echo \CHtml::encode($question->title); ?></div>
    <div class="panel-body"><?php echo $question->content; ?></div>
    <div class="panel-footer text-muted">
        <div class="row">
            <div class="col-md-6 text-left">
                <?php $this->widget('\web\widgets\qa\ListTags', array('tags' => $question->tagList)); ?>
            </div>
            <div class="col-md-6 text-right">
                <em><?php echo $question->getAuthor()->fio(); ?></em>
                &nbsp;
                <span class="text-muted"><?php echo date('Y-m-d H:i:s', $question->dateCreated); ?></span>
            </div>
        </div>
    </div>
</div>

<?php $this->widget('\web\widgets\qa\ListComments', array(
    'comments' => $question->comments,
    'entity' => array(
        'entity' => 'question',
        'id' => (string)$question->_id
    )
)); ?>

<?php if ($question->answerCount): ?>
    <h3><?php echo \yii::t('app', '{count} Answers', array('{count}' => $question->answerCount)); ?></h3>
    <hr/>
    <?php foreach ($answers as $answer): ?>
        <div class="row">
            <div class="col-xs-14 col-md-12">
                <div class="panel <?php echo $answer->getAuthor()->isApprovedCoordinator ? 'panel-success' : 'panel-default'; ?>">
                    <div class="panel-heading"><?php echo $answer->getAuthor()->fio(); ?></div>
                    <div class="panel-body"><?php echo \CHtml::encode($answer->content); ?></div>
                    <div class="panel-footer text-muted"><?php echo date('Y-m-d H:i:s', $answer->dateCreated); ?></div>
                </div>
            </div>
        </div>
        <?php $this->widget('\web\widgets\qa\ListComments', array(
            'comments' => $answer->comments,
            'entity' => array(
                'entity' => 'answer',
                'id' => (string)$answer->_id
            )
        )); ?>
        <br/>
    <?php endforeach; ?>
<?php else: ?>
    <div class="row">
        <div class="col-xs-14 col-md-12">
            <h4><?php echo \yii::t('app', 'There isn\'t a single answer'); ?></h4>
        </div>
    </div>
    <br/>
<?php endif; ?>

<div class="row">
    <div class="col-xs-14 col-md-12 answer-container">
        <div class="form-horizontal clearfix">
            <div class="form-group">
                <textarea id="answer-content" name="content"></textarea>
            </div>
        </div>
        <br/>
        <button type="button" class="btn btn-primary answer-create">
            <?php echo \yii::t('app', \yii::app()->user->isGuest ? 'Login' : 'Submit'); ?>
        </button>
    </div>
</div>

<script>
    $(function(){
        window.editor = CKEDITOR.replace('answer-content', {
            extraPlugins: 'onchange',
            height: '200px'
        });

        $(".answer-create").on('click', function(){
            $.ajax({
                type: "POST",
                url: "/qa/saveAnswer/<?php echo (string)$question->_id; ?>",
                data: {
                    content: window.editor.getData()
                },
                success: function(data) {
                    if (data.status === 'success') {
                        window.location = "/qa/view/<?php echo (string)$question->_id; ?>";
                    } else {
                        appShowErrors(data.errors, $('.form-horizontal'));
                    }
                },
                error: function(xhr) {
                    if (parseInt(xhr.status) === 403) {
                        window.location = "/auth/login";
                    } else {
                        console.log('Unexpected server error: ', xhr.statusText);
                    }
                }
            });
        });
    });
</script>