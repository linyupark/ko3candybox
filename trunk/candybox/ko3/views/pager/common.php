<?php
    $link_span = 6;
    $start_link = ($current_page - $link_span) > 0 ? ($current_page - $link_span) : 1;
    $last_link = ($current_page + $link_span) > $total_pages ? $total_pages : ($current_page + $link_span);
?>
<div class="candy-pager">
	<?php if ($first_page !== FALSE): ?>
		<a href="<?php echo $page->url($first_page) ?>"><?php echo '1 ..' ?></a>
	<?php else: ?>
		<?php //echo __('First') ?>
	<?php endif ?>

	<?php if ($previous_page !== FALSE): ?>
		<a href="<?php echo $page->url($previous_page) ?>"><b><?php echo '&lt;' ?></b></a>
	<?php else: ?>
		<?php //echo __('Previous') ?>
	<?php endif ?>

	<?php for ($i = $start_link; $i <= $last_link; $i++): ?>
		<?php if ($i == $current_page): ?>
			<strong><?php echo $i ?></strong>
		<?php else: ?>
			<a href="<?php echo $page->url($i) ?>"><?php echo $i ?></a>
		<?php endif ?>
	<?php endfor ?>

	<?php if ($next_page !== FALSE): ?>
		<a href="<?php echo $page->url($next_page) ?>"><b><?php echo '&gt;' ?></b></a>
	<?php else: ?>
		<?php //echo __('Next') ?>
	<?php endif ?>

	<?php if ($last_page !== FALSE): ?>
		<a href="<?php echo $page->url($last_page) ?>"><?php echo '.. '.$total_pages ?></a>
	<?php else: ?>
		<?php //echo __('Last') ?>
	<?php endif ?>
</div>