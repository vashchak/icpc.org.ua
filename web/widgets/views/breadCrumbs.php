<ol class="breadcrumb">
    <?php foreach ($this->crumbs as $title => $link): ?>
        <li>
            <a href="<?php echo $link; ?>"><?php echo \yii::t('app', $title); ?></a>
        </li>
    <?php endforeach; ?>
    <li class="active"><?php echo \yii::t('app', ucfirst($this->active)); ?></li>
</ol>