<html>
	<head>

	</head>
	<body>

		<h1>Register</h1>

		<ul>
			<li>
				<?php echo Neuron_URLBuilder::getInstance ()->getURL ('oauth/applications', array (), 'List applications'); ?>
			</li>

			<li>
				<?php echo Neuron_URLBuilder::getInstance ()->getUrl ('oauth/register', array (), 'Register'); ?>
			</li>
		</ul>

		<?php echo $content; ?>
	</body>
</html>