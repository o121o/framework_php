
		<script type="text/javascript" src="/resource/jQuery/jquery.min.js"></script>
		<?php if(isset($libJs)): ?>
			<?php foreach($libJs as $js): ?>
				<script type="text/javascript" src="/resource/<?=$js;?>"></script>
			<?php endforeach; ?>
		<?php endif; ?>
		<script type="text/javascript" src="/resource/global.js"></script>
		<?php if(isset($customJs)): ?>
			<?php foreach($customJs as $js): ?>
				<script type="text/javascript" src="/<?=$js;?>"></script>
			<?php endforeach; ?>
		<?php endif; ?>
	</body>
</html>