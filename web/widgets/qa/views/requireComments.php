<?php \yii::app()->getClientScript()->registerCoreScript('underscore'); ?>

<script type="text/html" id="tmpl_comment">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-10 text-left comment">
            <p><%- content %></p>
            <div class="text-right text-muted">
                <em><%- author_fio %></em>
                &nbsp;
                <span><%= date_created %></span>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl_entity_title">
    <h4>
        <strong>
            <%= entity %>&nbsp;comments</span>
        </strong>
    </h4>
</script>

<script>
    $(document).ready(function() {
        new appQaComments();
    });
</script>
