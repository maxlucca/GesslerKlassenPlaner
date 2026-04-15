<?php include dirname(__FILE__) . '/../layout/html_top.php' ?>
<?php if($_SERVER['REQUEST_METHOD'] === 'POST') { ?>

<strong>abgeschickt</strong>

<?php } else { ?>
<form method='POST'  action="/klasse/erstellen.php">
  <input type='submit' value='Erstellen' />

</form>
<?php } ?>
<?php include dirname(__FILE__) . '/../layout/html_bottom.php' ?>
