<div class="comment <?php print $comment_classes; ?>"><div class="comment-inner">

  <?php if ($title): ?>
    <h3 class="title"><?php print $title; if (!empty($new)): ?> <span class="new"><?php print $new; ?></span><?php endif; ?></h3>
  <?php elseif (!empty($new)): ?>
    <div class="new"><?php print $new; ?></div>
  <?php endif; ?>

  <?php if ($picture) print $picture; ?>

  <div class="submitted">
    <?php print t('On ') . format_date($comment->timestamp, 'custom', 'F jS, Y'); ?> <?php print theme('username', $comment) . t(' said:'); ?>
  </div>

  <div class="content">
    <?php print $content; ?>
  </div>

  <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
  <?php endif; ?>

</div></div> <!-- /comment-inner, /comment -->
