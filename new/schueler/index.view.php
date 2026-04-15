<?php if(!defined('ABC')) die() ?>

<?php include dirname(__FILE__) . '/../layout/html_top.php' ?>

  <ul>
    <?php foreach($schuelrList as $schuler) { ?>
      <li><?= $schuler['name'] ?></li>

    <?php } ?>
  </ul>
  <form method='post'>
    <input type="submit" value="abschicken" />
  </form>

<?php include dirname(__FILE__) . '/../layout/html_bottom.php' ?>
