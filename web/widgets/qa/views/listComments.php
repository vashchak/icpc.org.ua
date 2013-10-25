<?php if (count($this->comments)): ?>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-10 text-left comment">
            <?php //$ucf_entity = \yii::app()->string->mb_ucfirst($this->entity['entity']); ?>
            <?php $ucf_entity = $this->entity['entity']; ?>
            <h4><strong><?php echo \yii::t('app', "{$ucf_entity} comments"); ?></strong></h4>
        </div>
    </div>
    <?php foreach ($this->comments as $comment): ?>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10 text-left comment">
                <p><?php echo \CHtml::encode($comment['content']); ?></p>
                <div class="text-right text-muted">
                    <em><?php echo $comment['author']['fio']; ?></em>
                    &nbsp;
                    <span><?php echo date('Y-m-d H:i:s', $comment['dateCreated']); ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="row intro">
        <div class="col-md-2"></div>
        <div class="col-md-10 text-left comment">
            <em><?php echo \yii::t('app', 'There isn\'t a single comment yet'); ?></em>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-10 text-left comment-container">
        <div class="comment-subcontainer" style="display: none;">
            <input type="hidden"
                   class="comment-entity-resolver"
                   data-entity ="<?php echo $this->entity['entity']; ?>"
                   data-id="<?php echo $this->entity['id']; ?>" />
            <div class="form-horizontal clearfix">
                <div class="form-group">
                    <textarea class="form-control comment-content" name="content"></textarea>
                </div>
            </div>
            <button type="button" class="btn btn-primary comment-submit-btn"><?php echo \yii::t('app', 'Add Comment'); ?></button>
        </div>
        <button type="button" class="btn btn-default comment-add-btn"><?php echo \yii::t('app', 'add comment'); ?></button>
    </div>
</div>