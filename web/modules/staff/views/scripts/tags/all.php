<h2><?php echo \yii::t('app', 'All tags'); ?></h2>
<hr/>
<a class="btn btn-primary" href="/staff/tags/create"><?php echo \yii::t('app', 'Add a tag'); ?></a>
<br/>
<br/>
<?php $this->widget('\web\widgets\qa\ListTags', array('tags' => $tags, 'mode' => 'table')); ?>

<script>
    $(function(){
        $('.tag').on('click', function(){
            var $el = $(this);
            if (confirm('<?php echo \yii::t('app', 'Are you sure you want to delete this tag: "'); ?>' + $el.data('name') + '" ?')) {
                $.ajax({
                    type: "POST",
                    url: "/staff/tags/delete/" + $el.data('id'),
                    success: function(data) {
                        if (data.result) {
                            $el.remove();
                        }
                    },
                    error: function(xhr) {
                        if (parseInt(xhr.status) === 403) {
                            alert('<?php echo \yii::t('app', 'You are forbidden to perform this action'); ?>');
                        } else {
                            console.log('Unexpected server error: ', xhr.statusText);
                        }
                    }
                });
            }
            return false;
        });
    });
</script>