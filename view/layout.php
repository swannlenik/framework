<?php include $this->layout["header"]; ?>
<?php
foreach($this->layout as $layer => $content) {
    if(!in_array($layer, ["header", "footer"])) {
        include $content;
    }
}
?>
<?php include $this->layout["footer"]; ?>