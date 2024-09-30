<div class="users form">
	<?php echo $this->Form->create(null, ['id' => 'loginForm', 'url' => ['action' => 'login']]); ?>
	<fieldset>
		<legend><?php echo __('Login User'); ?></legend>
		<?php
		echo $this->Form->input('email', ['required' => true, 'id' => 'email']); // Add id to input
		echo $this->Form->input('password', ['required' => true, 'id' => 'password']); // Add id to input
		?>
	</fieldset>
	<div id="validation-messages"></div> <!-- Container for validation messages -->
	<?php echo $this->Form->end(__('Submit')); ?>
</div>

<script>
	$(document).ready(function() {
		// Login AJAX form submit
		$('#loginForm').submit(function(event) {
			event.preventDefault();

			// Clear previous validation messages
			$('#validation-messages').empty();

			// Get form values
			var email = $('#email').val().trim(); // Corrected selector for email input
			var password = $('#password').val().trim(); // Corrected selector for password input
			var hasError = false;

			// Validate email
			if (email === '') {
				hasError = true;
				$('#validation-messages').append('<div class="alert alert-danger">Email is required.</div>');
			} else if (!validateEmail(email)) {
				hasError = true;
				$('#validation-messages').append('<div class="alert alert-danger">Invalid email format.</div>');
			}

			// Validate password
			if (password === '') {
				hasError = true;
				$('#validation-messages').append('<div class="alert alert-danger">Password is required.</div>');
			}

			// If there are validation errors, prevent form submission
			if (hasError) {
				return;
			}

			// Serialize form data and make AJAX request
			var formData = $(this).serialize();
			var actionUrl = $(this).attr('action');
			ajax_request('POST', actionUrl, formData, 'loginForm');
		});

		// AJAX request function
		function ajax_request(type, url, payload, id) {
			$.ajax({
				type: type,
				url: url,
				dataType: 'json',
				data: payload,
				success: function(response) {
					console.log(response);
					if (response.success) {
						alert(response.message);
						window.location.href = "<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'inbox'], true); ?>";
					} else {
						// Show the error message
						var messages = '<div id="validation-alert" class="alert alert-danger"><ul style="list-style-type: none;">';
						messages += '<li>' + response.message + '</li>';
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

		// Function to validate email format
		function validateEmail(email) {
			var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple regex for email validation
			return emailPattern.test(email);
		}
	});
</script>