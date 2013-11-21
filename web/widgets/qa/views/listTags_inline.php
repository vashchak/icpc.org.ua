<?php foreach($this->tags as $tag): ?>
    <?php
        $name = is_string($tag) ? $tag : $tag->name;
        $desc = is_string($tag) ? $tag : $tag->desc;
        $id = is_string($tag) ? $tag : (string)$tag->_id;
    ?>
    <a href="/qa/tag/<?php echo urlencode(\CHtml::encode($name)); ?>"
       class="tag"
       title="<?php echo $desc; ?>"
       data-name="<?php echo $name; ?>"
       data-id="<?php echo (string)$id; ?>">
        <span class="label label-<?php echo $this->colorize(); ?>"><?php echo \CHtml::encode($name); ?></span>
    </a>
<?php endforeach; ?>
