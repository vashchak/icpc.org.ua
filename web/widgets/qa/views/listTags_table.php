<table class="table">
    <tbody>
        <?php $i = 0; ?>
        <?php foreach($this->tags as $tag): ?>
            <?php
                $name = is_string($tag) ? $tag : $tag->name;
                $desc = is_string($tag) ? $tag : $tag->desc;
                $id = is_string($tag) ? $tag : (string)$tag->_id;
            ?>
            <?php if ($i % $this->tableRows == 0): ?>
                <tr>
            <?php endif; ?>
                <td class="tagCell">
                    <a href="/qa/tag/<?php echo urlencode(\CHtml::encode($name)); ?>"
                       class="tag"
                       title="<?php echo $desc; ?>"
                       data-name="<?php echo $name; ?>"
                       data-id="<?php echo (string)$id; ?>">
                        <span class="label label-<?php echo $this->colorize(); ?>"><?php echo \CHtml::encode($name); ?></span>
                    </a>
                    <br/>
                    <small class="text-muted"><?php echo $desc; ?></small>
                </td>
            <?php if ($i % $this->tableRows == 3): ?>
                </tr>
            <?php endif; ?>
            <?php $i++; ?>
        <?php endforeach; ?>
    </tbody>
</table>