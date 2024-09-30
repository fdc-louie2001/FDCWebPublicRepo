<div class="container mt-5">
	<div id="message-status"></div>
	<div class="mt-3 justify-content-start mb-2">
		<?php echo $this->Html->link('Back to Inbox', ['controller' => 'messages', 'action' => 'inbox'], ['class' => 'btn btn-danger']); ?>
	</div>
	<?php echo $this->Form->create(null, [
		'url' => ['controller' => 'messages', 'action' => 'send_message'],
		'id' => 'messageForm',
		'class' => 'message-form',
		'type' => 'post',
		'onsubmit' => 'return false;' // Prevent default form submission
	]); ?>

	<div class="form-group mb-2">
		<?php echo $this->Form->input('Message.receiverId', [
			'type' => 'select',
			'class' => 'select2 form-control js-example-templating',
			'options' => $users, // Users list passed from the controller
			'placeholder' => 'Select recipient',
			'label' => false
		]); ?>
	</div>

	<div class="form-group mt-2">
		<?php echo $this->Form->textarea('Message.sentMessage', [
			'class' => 'message-textbox form-control',
			'placeholder' => 'Type your message here...',
			'label' => false
		]); ?>
	</div>

	<div class="d-flex justify-content-end mt-2">
		<?php echo $this->Form->button(__('Send'), [
			'id' => 'sendMessageButton',
			'type' => 'submit',
			'class' => 'btn btn-success mt-2'
		]);
		echo $this->Form->end();
		?>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: 'Select recipient',
			allowClear: true
		});

		$('#sendMessageButton').on('click', function() {
			var formData = $('#messageForm').serialize();

			$.ajax({
				url: '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'send_message']); ?>',
				type: 'POST',
				data: formData,
				dataType: 'json',
				success: function(response) {

					if (response.status === 'success') {
						$('#message-status').html('<div class="alert alert-success">' + response.message + '</div>');
						$('#messageForm')[0].reset(); // Clear the form after sending the message
						$('.select2').val(null).trigger('change'); // Reset the Select2 field
					} else {
						$('#message-status').html('<div class="alert alert-danger">' + response.message + '</div>');
					}
				},
				error: function() {
					$('#message-status').html('<div class="alert alert-danger">An error occurred while sending the message.</div>');
				}
			});
		});
	});
</script>