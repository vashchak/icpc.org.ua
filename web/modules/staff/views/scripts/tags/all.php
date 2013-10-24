<h2><?php echo \yii::t('app', 'All tags'); ?></h2>
<hr/>
<a class="btn btn-primary" href="/staff/tags/create"><?php echo \yii::t('app', 'Add a tag'); ?></a>
<br/>
<br/>
<?php $this->widget('\web\widgets\qa\ListTags', array('tags' => $tags)); ?>

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
                    error: function(error) {
                        console.log('Unexpected server error: ', error);
                    }
                });
            } else {

            }
        });
    });
</script>