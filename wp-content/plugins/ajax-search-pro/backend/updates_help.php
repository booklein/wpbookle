<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (ASP_DEMO) $_POST = null;

// Force to get new data for this session
//asp_updates()->getData( true );

?>
<div id="wpdreams" class="asp_updates_help">
	<div class="wpdreams-box">
		<div class="wpd-half">
			<h3>Documentation</h3>
			<div class="item">
				<ul>
					<li><a target="_blank" href="http://wpdreams.gitbooks.io/ajax-search-pro-documentation/content/" title="HTML documentation">HTML version</a></li>
					<li><a target="_blank" href="https://www.gitbook.com/download/pdf/book/wpdreams/ajax-search-pro-documentation" title="PDF documentation">PDF version (download)</a></li>
					<li><a target="_blank" href="https://wp-dreams.com/knowledgebase/" title="Knowledge Base">Knowledge base</a></li>
				</ul>
			</div>
			<h3>Knowledge Base</h3>
			<div class="item">
				<?php echo asp_updates()->getKnowledgeBase(); ?>
			</div>
		</div>
		<div class="wpd-half-last">
			<h3>Updates</h3>
			<div class="item">
				<?php if (asp_updates()->needsUpdate()): ?>
					<p class="infoMsg">A new version <strong><?php echo asp_updates()->getVersionString(); ?></strong> is available!</p>
					<a target="_blank" href="http://wpdreams.gitbooks.io/ajax-search-pro-documentation/content/update_notes.html">How to update?</a>
				<?php else: ?>
					<p>You have the latest version installed.</p>
				<?php endif; ?>
			</div>
			<h3>Changelog</h3>
			<div class="item">
				<dl>
					<?php foreach (asp_updates()->getChangeLog() as $version => $log): ?>
						<dt class="changelog_title">v<?php echo $version; ?> - <a href="#">view changelog</a></dt>
						<dd class="hiddend"><pre><?php echo $log; ?></pre></dd>
					<?php endforeach; ?>
				</dl>
			</div>
			<h3>Support</h3>
			<div class="item">
				<?php if (asp_updates()->getSupport() != ""): ?>
				<p class="errorMsg">IMPORTANT:<br><?php echo asp_updates()->getSupport(); ?></p>
				<?php endif; ?>
				If you can't find the answer in the documentation or knowledge base, or if you are having other issues,
				feel free to <a href="https://wp-dreams.com/open-support-ticket-step-1/" target="_blank">open a support ticket</a>.
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(function($){
		$("dt.changelog_title a").click(function(e){
			e.preventDefault();
			var $next = $(this).parent().next();
			if ($next.hasClass('hiddend')) {
				$next.removeClass('hiddend');
				$(this).html('hide changelog');
			} else {
				$next.addClass('hiddend');
				$(this).html('view changelog');
			}
		});
	});
</script>