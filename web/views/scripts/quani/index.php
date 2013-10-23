<h2><?php echo \yii::t('app', 'Latest questions'); ?></h2>
<hr/>
<a class="btn btn-primary" href="/staff/quani/create"><?php echo \yii::t('app', 'Ask a question'); ?></a>
<br/>
<br/>
<?php $this->widget('\CLinkPager', array('pages' => $pages, "cssFile" => false)); ?>
    <ul class="list-group">
        <?php foreach($q as $k => $_q): ?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-md-11">
                        <a href="/quani/view/<?php echo $_q->_id ?>"><?php echo $_q->title ?></a>
                        &nbsp;
                        <span class="badge"><?php echo $_q->answerCount ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6  text-left">
                        <?php $this->widget('\web\widgets\quani\ListTags', array('tags' => $_q->tagList)); ?>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="text-muted"><em><?php echo $_q->getAuthor()->fio(); ?></em></span>
                        &nbsp;
                        <span class="text-muted"><?php echo date("Y-m-d H:i:s", $_q->dateCreated); ?></span>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php $this->widget('\CLinkPager', array('pages' => $pages, "cssFile" => false)); ?>