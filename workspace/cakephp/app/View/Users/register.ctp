<div id="validation-messages"></div>
<div class="users form">
	<?php echo $this->Form->create(null, ['id' => 'registerForm', 'url' => ['action' => 'register']]); ?>
	<fieldset>
		<legend><?php echo __('Add User'); ?></legend>
		<?php
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('password');
		echo $this->Form->input('confirm_password', ['type' => 'password']);
		?>
	</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>

<script>
	$(document).ready(function() {
		$('#registerForm').submit(function(event) {
			event.preventDefault();
			var formData = $(this).serialize();
			var actionUrl = $(this).attr('action');
			ajax_request('POST', actionUrl, formData, 'registerForm');
		});
	});

	function ajax_request(type, url, payload, id) {
		$.ajax({
			type: type,
			url: url,
			dataType: 'json',
			data: payload,
			success: function(response) {
				if (response.success) {
					alert(response.message);
					window.location.href = '/cakephp/users/login';
				} else {
					var messages = '<div id="validation-alert" class="alert alert-danger"><ul style="list-style-type: none;">';
					$.each(response.message, function(index, error) {
						messages += '<li>' + error + '</li>';
					});
					messages += '</ul></div>';
					$('#validation-messages').html(messages);
					setTimeout(() => {
						$('#validation-alert').fadeOut('slow', function() {
							$(this).remove();
						});
					}, 3000);
				}
			},
			error: function(xhr, status, error) {
				console.error('Error:', error);
				alert('An error occurred. Please try again.');
			}
		});
	}
</script>