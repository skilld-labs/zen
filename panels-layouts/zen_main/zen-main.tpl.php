<div id="main">

  <div id="content" class="column" role="main">
    <?php if ($content['highlighted']): ?>
      <div class="region region-highlighted">
        <?php print $content['highlighted']; ?>
      </div>
    <?php endif; ?>
    <a id="main-content"></a>
    <?php print $content['content']; ?>
  </div>

  <?php if ($content['navigation']): ?>
    <div id="navigation">
      <?php print $content['navigation']; ?>
    </div>
  <?php endif; ?>

  <?php if ($content['sidebar_first'] || $content['sidebar_second']): ?>
    <aside class="sidebars">
      <?php if ($content['sidebar_first']): ?>
        <section class="region region-sidebar-first column sidebar">
          <?php print $content['sidebar_first']; ?>
        </section>
      <?php endif; ?>
      <?php if ($content['sidebar_second']): ?>
        <section class="region region-sidebar-second column sidebar">
          <?php print $content['sidebar_second']; ?>
        </section>
      <?php endif; ?>
    </aside>
  <?php endif; ?>

</div>
