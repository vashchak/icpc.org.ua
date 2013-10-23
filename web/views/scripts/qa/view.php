<?php \yii::app()->getClientScript()->registerCoreScript('ckeditor'); ?>

<?php if (\yii::app()->user->checkAccess('qaQuestionUpdateOwn', array('userId' => (string)$question->userId))): ?>
    <a class="btn btn-warning" href="/qa/update/<?php echo (string)$question->_id; ?>"><?php echo \yii::t('app', 'Edit'); ?></a>
    <br/>
    <br/>
<?php endif; ?>
<div class="panel panel-primary">
    <input type="hidden" value="" name="<?php echo $question->_id; ?>" />
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
<?php if ($question->answerCount): ?>
    <h3><?php echo \yii::t('app', '{count} Answers', array('{count}' => $question->answerCount)); ?></h3>
    <hr/>
    <?php foreach ($answers as $answer): ?>
        <div class="row">
            <div class="col-xs-14 col-md-12">
                <div class="panel <?php echo $answer->getAuthor()->isApprovedCoordinator ? 'panel-success' : 'panel-default'; ?>">
                    <div class="panel-heading"><?php echo $answer->getAuthor()->fio(); ?></div>
                    <div class="panel-body"><?php echo $answer->content; ?></div>
                    <div class="panel-footer text-muted"><?php echo date('Y-m-d H:i:s', $answer->dateCreated); ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="row">
        <div class="col-xs-6 col-md-2 text-right"><h4><?php echo \yii::t('app', 'There isn\'t a single answer'); ?></h4></div>
        <div class="col-xs-12 col-md-10"></div>
    </div>
<?php endif; ?>

<?php if (\yii::app()->user->checkAccess('answerCreate')): ?>
    <div class="row">
        <div class="col-xs-6 col-md-2 text-right">
            <button type="button" class="btn answer-open">
                <?php echo \yii::t('app', 'Close answer form'); ?>
            </button>
        </div>
        <div class="col-xs-12 col-md-10 answer-container">
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
<?php endif; ?>

<script>
    $(function(){
        window.editor = CKEDITOR.replace('answer-content', {
            extraPlugins: 'onchange',
            height: '200px'
        });

        $(".answer-create").on('click', function(){
            <?php if (!\yii::app()->user->isGuest): ?>
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
                    error: function(error) {
                        console.log('Unexpected server error: ', error);
                    }
                });
            <?php else: ?>
                window.location = '/auth/login';
            <?php endif; ?>
        });

        $('.answer-open').on('click', function(){
            var $container = $('.answer-container').eq(0);
            var $btnOpen = $('.answer-open').eq(0);
            $container.toggle("slow", function(){
                if ($(':visible', $container).length) {
                    $btnOpen.html('<?php echo \yii::t('app', 'Close answer form'); ?>');
                } else {
                    $btnOpen.html('<?php echo \yii::t('app', 'Open answer form'); ?>');
                }
            });
        });
    });
</script>