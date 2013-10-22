<?php \yii::app()->getClientScript()->registerCoreScript('ckeditor'); ?>

<?php $this->widget('\web\widgets\BreadCrumbs'); ?>

<?php if (!\yii::app()->user->isGuest): ?>
    <a class="btn btn-warning" href="/staff/quani/update/<?php echo $question->_id; ?>"><?php echo \yii::t('app', 'Update'); ?></a>
    <br/>
    <br/>
<?php endif; ?>
<div class="panel panel-primary">
    <input type="hidden" value="" name="<?php echo $question->_id; ?>" />
    <div class="panel-heading"><?php echo \CHtml::encode($question->title); ?></div>
    <div class="panel-body"><?php echo $question->content; ?></div>
    <div class="panel-footer text-muted"><?php echo date('Y-m-d H:i:s', $question->dateCreated); ?></div>
</div>
<p class="tagList">
    <?php foreach ($question->tagList as $tag): ?>
    <span class="label label-default"><?php echo \CHtml::encode($tag); ?></span>
    <?php endforeach; ?>
</p>
<?php if ($question->answerCount): ?>
    <?php foreach ($answers as $answer): ?>
        <div class="row">
            <div class="col-xs-6 col-md-2"></div>
            <div class="col-xs-12 col-md-10">
                <div class="panel panel-info">
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

<?php if (!\yii::app()->user->isGuest): ?>
    <div class="row">
        <div class="col-xs-6 col-md-2 text-right">
            <button type="button" class="btn answer-open">
                <?php echo \yii::t('app', 'Close answer form'); ?>
            </button>
        </div>
        <div class="col-xs-12 col-md-10 answer-container">
            <textarea id="answer-content"></textarea>
            <br/>
            <button type="button" class="btn btn-primary answer-create">
                <?php echo \yii::t('app', 'Submit'); ?>
            </button>
        </div>
    </div>
<?php endif; ?>

<?php if (!\yii::app()->user->isGuest): ?>
    <script>
        $(function(){
            window.editor = CKEDITOR.replace('answer-content', {
                extraPlugins: 'onchange',
                height: '200px'
            });

            $(".answer-create").on('click', function(){
                $.ajax({
                    type: "POST",
                    url: "/staff/quani/saveAnswer/<?php echo (string)$question->_id; ?>",
                    data: {
                        content: window.editor.getData()
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            window.location = "/quani/view/<?php echo (string)$question->_id; ?>";
                        } else {
                            appShowErrors(data.errors, $('.form-horizontal'));
                        }
                    },
                    error: function(error) {
                        console.log('Unexpected server error: ', error);
                    }
                });
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
<?php endif; ?>