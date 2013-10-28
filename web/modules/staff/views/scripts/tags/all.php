<h2><?php echo \yii::t('app', 'All tags'); ?></h2>
<hr/>
<a class="btn btn-primary" href="/staff/tags/create"><?php echo \yii::t('app', 'Add a tag'); ?></a>
<br/>
<br/>
<?php $this->widget('\web\widgets\qa\ListTags', array('tags' => $tags, 'mode' => 'table')); ?>

<script>
    $(document).ready(function(){
        new appStaffTagsAll({
            conf: '<?php echo \yii::t('app', 'Are you sure you want to delete this tag: "'); ?>',
            deny: '<?php echo \yii::t('app', 'You are forbidden to perform this action'); ?>'
        });
    });
</script>
