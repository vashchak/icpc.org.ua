<?php $this->widget('\web\widgets\BreadCrumbs'); ?>
<h2><?php echo \yii::t('app', 'Latest questions'); ?></h2>
<hr/>
<?php if (!\yii::app()->user->isGuest): ?>
    <a class="btn btn-primary" href="/staff/quani/create"><?php echo \yii::t('app', 'Ask a question'); ?></a>
    <br/>
    <br/>
<?php endif; ?>
<?php $this->widget('\CLinkPager', array('pages' => $pages, "cssFile" => false)); ?>
    <ul class="list-group">
        <?php foreach($q as $k => $_q): ?>
            <li class="list-group-item">
                <span class="badge"><?php echo $_q->answerCount ?></span>
                <a href="/quani/view/<?php echo $_q->_id ?>"><?php echo $_q->title ?></a>
                <br/>
                <span class="text-muted"><?php echo date("Y-m-d H:i:s", $_q->dateCreated); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php $this->widget('\CLinkPager', array('pages' => $pages, "cssFile" => false)); ?>