<?php
    \yii::app()->getClientScript()->registerCoreScript('ckeditor');
    //$this->widget('\web\widgets\BreadCrumbs');
?>

<h2><?php echo \yii::t('app', 'Tag creation'); ?></h2>
<hr/>
<div class="form-horizontal clearfix">
    <div class="form-group">
        <input type="text"
               class="form-control"
               name="name"
               value="<?php echo \CHtml::encode(\yii::app()->request->getParam('name', '')); ?>"
               placeholder="<?php echo \yii::t('app', 'Title'); ?>">
    </div>
    <div class="form-group">
        <textarea class="form-control"
                  name="desc"
                  id="tag-desc"
                  style="height: 500px;">
            <?php echo \CHtml::encode(\yii::app()->request->getParam('desc', '')); ?>
        </textarea>
    </div>
    <div class="form-group">
        <button class="btn btn-primary save-tag btn-lg pull-left" disabled="disabled">
            <?php echo \yii::t('app', 'Save question'); ?>
        </button>
    </div>
</div>

<script>
    function canSave() {
        if (window.editor.getData().length && $("input[name=name]").val().length) {
            $(".save-tag").removeAttr("disabled");
        } else {
            $(".save-tag").attr("disabled", "disabled");
        }
    }

    $(function(){
        window.editor = CKEDITOR.replace('tag-desc', {
            extraPlugins: 'onchange',
            height: '200px'
        });

        window.editor.on('change', function(e) {
            canSave();
        });

        $("input[name=name]").on("change", function(){
            canSave();
        });

        $(".save-tag").on("click", function(){
            $.ajax({
                type: "POST",
                url: "/staff/tags/create",
                data: {
                    name: $("input[name=name]").val(),
                    desc: window.editor.getData()
                },
                success: function(data) {
                    if (data.status === 'success') {
                        window.location = "/staff/tags/all/";
                    } else {
                        appShowErrors(data.errors, $('.form-horizontal'));
                    }
                },
                error: function(error) {
                    console.log('Unexpected server error: ', error);
                }
            });
        });
    });
</script>