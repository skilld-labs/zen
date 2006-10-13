<div class="node<?php if ($sticky) { print " sticky"; } ?><?php if (!$status) { print " node-unpublished"; } ?>" id="node-<?php print $node->nid; ?>">
  <?php if ($page == 0): ?>
    <h2 class="title">
      <a href="<?php print $node_url ?>"><?php print $title; ?></a>
    </h2>
  <?php endif; ?>

  <?php if ($picture) print $picture; ?>  
  
  <?php if ($submitted): ?>
    <span class="submitted"><?php print t('Posted ') . format_date($node->created, 'custom', "F jS, Y") . t(' by ') . theme('username', $node); ?></span> 
  <?php endif; ?>
  
  <div class="content">
    <?php print $content; ?>
  </div>
  
  <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
  <?php endif; ?>
  
  <?php if (count(taxonomy_node_get_terms($node->nid))): ?>
    <div class="taxonomy"><?php print t('tags: ') . $terms; ?></div>
  <?php endif; ?>     
</div>
