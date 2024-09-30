<style>
	.profile-pic {
		width: 100px;
		/* Adjust the size as needed */
		height: 100px;
		/* Ensure it's square */
		border-radius: 50%;
		/* Makes the image circular */
		border: 2px solid #ccc;
		/* Border color and size */
		object-fit: cover;
		/* Ensures the image covers the area without stretching */
	}

	.btn {
		margin-left: 6px;
	}
</style>
<div class="users form">
	<?php echo $this->Form->create('User', ['type' => 'file']); ?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
		<?php
		// Check if the user has an existing profile picture
		$profilePic = $this->request->data['User']['profilePic'];


		echo $this->Html->image($profilePic, ['alt' => 'Profile Picture', 'class' => 'profile-pic', 'id' => 'profilePicPreview']);
		echo $this->Form->input('profilePic', ['type' => 'file', 'accept' => 'image/*', 'id' => 'profilePicInput']);
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->button('Change Password', [
			'type' => 'button',
			'class' => 'btn btn-danger',
			'id' => 'changePasswordBtn' // Added ID for the button
		]);

		// Gender input
		echo $this->Form->input('gender', [
			'type' => 'radio',
			'options' => ['male' => 'Male', 'female' => 'Female'],
			'legend' => false,
			'value' => ($this->request->data['User']['gender']) ? 'male' : null
		]);

		// Birthdate input
		echo $this->Form->input('birthDate', ['type' => 'text', 'id' => 'UserBirthDate']);
		echo $this->Form->input('hobby');
		?>
	</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->value('User.id')], ['confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('User.id'))]); ?></li>
		<li><?php echo $this->Html->link(__('Logout'),  ['controller' => 'users', 'action' => 'logout']); ?>
			<!-- <li><?php echo $this->Html->link(__('List Users'), ['action' => 'index']); ?></li> -->
		</li>
	</ul>
</div>

<!-- Modal -->
<!-- Modal -->
<div class="modal" tabindex="-1" role="dialog" id="changePasswordModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Error message display -->
                <div id="error-message" class="alert alert-danger" style="display: none;"></div>
                
                <div class="form-group">
                    <input type="password" class="form-control" id="currentPassword" placeholder="Enter Current Password">
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="newPassword" placeholder="Enter New Password">
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="confirmNewPassword" placeholder="Confirm New Password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
	$(document).ready(function() {
		$('#profilePicInput').on('change', function(event) {
			const file = event.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function(e) {
					$('#profilePicPreview').attr('src', e.target.result);
				};
				reader.readAsDataURL(file);
			}
		});

		// Datepicker initialization
		$("#UserBirthDate").datepicker({
			dateFormat: "yy-mm-dd",
			maxDate: 0,
			changeMonth: true,
			changeYear: true,
		});

		// Show modal on Change Password button click
		$('#changePasswordBtn').on('click', function() {
			$('#changePasswordModal').modal('show');
		});

		// Save new password on Save Changes button click
		$('#saveChangesBtn').on('click', function() {
			const currentPassword = $('#currentPassword').val();
			const newPassword = $('#newPassword').val();
			const confirmNewPassword = $('#confirmNewPassword').val();

			// Clear previous error message
			$('#error-message').hide().text('');

			if (newPassword !== confirmNewPassword) {
				$('#error-message').text('New password and confirm password do not match!').show();
				return;
			}

			$.ajax({
				url: '<?php echo $this->Html->url(['controller' => 'Users', 'action' => 'change_password']); ?>',
				type: 'POST',
				data: {
					currentPassword: currentPassword,
					newPassword: newPassword
				},
				success: function(response) {
					// Ensure the response is parsed correctly
					response = JSON.parse(response);
					if (response.status === 'success') {
						alert('Password updated successfully!');
						$('#changePasswordModal').modal('hide');

					} else {
						$('#error-message').text(response.message).show(); // Display error message
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('#error-message').text('An error occurred. Please try again later.').show(); // Display generic error message
				}
			});
		});
	});
</script>