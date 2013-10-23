<?php
    \yii::app()->getClientScript()->registerCoreScript('ckeditor');
    \yii::app()->getClientScript()->registerCoreScript('select2');
    //$this->widget('\web\widgets\BreadCrumbs');
?>

<?php if ($question->isNewRecord): ?>
    <h2><?php echo \yii::t('app', 'Create a new question'); ?></h2>
<?php else: ?>
    <h2><?php echo \yii::t('app', 'Edit the question'); ?></h2>
    <small>
        <b><?=\yii::t('app', 'Preview')?>:&nbsp;</b>
        <a href="/quani/view/<?php echo (string)$question->_id; ?>" target="_blank"></a>
    </small>
<?php endif; ?>

<div class="form-horizontal clearfix">
    <input type="hidden" name="id" value="<?php echo (string)$question->_id; ?>" />
    <div class="form-group">
        <input type="text" class="form-control" name="title"
               value="<?php echo \CHtml::encode($question->title); ?>"
               placeholder="<?php echo \yii::t('app', 'Title'); ?>">
    </div>
    <div class="form-group">
        <textarea class="form-control"
                  name="content"
                  id="question-content"
                  style="height: 500px;"><?php echo \CHtml::encode($question->content)?></textarea>
    </div>
    <div class="form-group">
        <input type="hidden" name="tagList">
    </div>
    <div class="form-group">
        <button class="btn btn-primary save-question btn-lg pull-left" disabled="disabled">
            <?php echo \yii::t('app', 'Save question'); ?>
        </button>
    </div>
</div>

<script>
    function canSave() {
        if (
            window.editor.getData().length &&
            $("input[name=title]").val().length &&
            $("input[name=tagList]").val()
        ) {
            $(".save-question").removeAttr("disabled");
        } else {
            $(".save-question").attr("disabled", "disabled");
        }
    }

    $(function(){
        window.editor = CKEDITOR.replace('question-content', {
            extraPlugins: 'onchange',
            height: '300px'
        });

        window.editor.on('change', function(e) {
            canSave();
        });

        $("input[name=title], input[name=tagList]").on("change", function(){
            canSave();
        });

        $(".save-question").on("click", function(){
            $.ajax({
                type: "POST",
                url: "/staff/quani/<?php echo $question->isNewRecord ? "create" : "update/{$question->_id}"; ?>",
                data: {
                    title: $("input[name=title]").val(),
                    content: window.editor.getData(),
                    tagList: $("input[name=tagList]").val()
                },
                success: function(data) {
                    if (data.status === 'success') {
                        window.location = "/quani/view/" + data.id;
                    } else {
                        appShowErrors(data.errors, $('.form-horizontal'));
                    }
                },
                error: function(error) {
                    console.log('Unexpected server error: ', error);
                }
            });
        });

        $("input[name=tagList]").select2({
            placeholder: "<?php echo \yii::t('app', 'List of tags'); ?>",
            minimumInputLength: 2,
            maximumSelectionSize: 10,
            multiple: true,
            width: '500',
            tags:[],
            initSelection : function (element, callback) {
                var data = [];
                $(element.val().split(",")).each(function () {
                    data.push({id: this, text: this});
                });
                callback(data);
            },
            ajax: {
                url: "/quani/getTags",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term,
                        page_limit: 10
                    };
                },
                results: function (data, page) {
                    return {results: data.tags};
                }
            },
            escapeMarkup: function (m) { return m; },
            formatSelection: function(item) {
                return item.text;
            },
            formatResult: function(item) {
                return item.text;
            },
            dropdownCssClass: "bigdrop"
        });

        <?php if (count($question->tagList)): ?>
            $('input[name=tagList]').val("<?php echo implode(',', $question->tagList); ?>").trigger('change');
        <?php endif; ?>
    });
</script>