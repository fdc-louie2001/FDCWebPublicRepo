<?php echo $this->Flash->render(); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



<nav class="navbar navbar-expand-lg navbar-light bg-light d-flex justify-content-between">
	<div>
		<?php echo $this->Html->link(
			__('Home'),
			['controller' => 'messages', 'action' => 'inbox', 'home'],
			['class' => 'navbar-brand']
		); ?>

	</div>
	<div class="d-flex">
		<?php echo $this->Html->link(
			__(AuthComponent::user('name')),
			['controller' => 'users', 'action' => 'view', AuthComponent::user('id')],
			['class' => 'navbar-brand']
		); ?>

		<div class="logout-button ml-3">
			<?php if (AuthComponent::user()) {
				echo $this->Html->link(
					__('Logout'),
					['controller' => 'users', 'action' => 'logout'],
					['class' => 'btn btn-danger']
				);
			} else {
				echo $this->Html->link(
					__('Login '),
					['controller' => 'users', 'action' => 'login'],
					['class' => 'btn btn-primary mr-2']
				);

				echo $this->Html->link(
					__('Register '),
					['controller' => 'users', 'action' => 'register'],
					['class' => 'btn btn-silver']
				);
			}
			?>
		</div>
	</div>
</nav>
<?php echo $this->fetch('content'); ?>