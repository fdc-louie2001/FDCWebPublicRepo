<div class="container">
	<h2>Open Conversation</h2>

	<div class="mb-2">
		<strong>
			<span>Chatting with </span>
			<?php echo $this->Html->link(h($otherUserName), [
				'controller' => 'Users',
				'action' => 'view',
				$receiverId
			]); ?>
		</strong>
	</div>
	<div class="mb-2">
		<?php echo $this->Form->textarea('searchMessage', [
			'class' => 'form-control',
			'rows' => '1',
			'name' => 'body',
			'id' => 'searchMessage',
			'placeholder' => 'Search in conversation'
		]); ?>
	</div>

	<div>
		<?php
		echo $this->Form->create(null, ['id' => 'replyMessageForm', 'url' => ['controller' => 'messages', 'action' => 'replyMessage']]);
		echo $this->Form->textarea('sentMessage', ['class' => 'form-control', 'rows' => '5', 'name' => 'body', 'id' => 'message', 'placeholder' => 'Input your message reply here...']);
		echo $this->Form->hidden('conversationId', ['value' => $conversationId]);
		echo $this->Form->hidden('user_id', ['value' => $receiverId]);
		echo $this->Form->button('Send Message', ['type' => 'submit', 'class' => 'btn btn-success mt-2 w-100']);
		echo $this->Form->end();
		?>
	</div>
	<div id="messageExchange" class="mb-4">
		<?php if (!empty($messages)): ?>
			<?php foreach ($messages as $message): ?>
				<div class="message-card mb-2">
					<?php

					$currentUserId = AuthComponent::user('id');
					$messageBody = h($message['Message']['body']);
					if ($message['Sender']['id'] == $currentUserId): ?>
						<!-- User's own message -->
						<div class="message-sender text-end">
							<div class="alert alert-primary float-end">
								<strong><?php echo h($message['Sender']['name']); ?></strong>
								<div class="message-body">
									<p class="message-text"><?php echo $messageBody; ?></p>
									<?php if (strlen($messageBody) > 100): ?>
										<span class="read-more">Read More</span>
									<?php endif; ?>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<small class="text-muted"><?php echo h($message['Message']['createdAt']); ?></small>
									<span class="deleteMessage btn btn-danger" data-message-id="<?php echo h($message['Message']['id']); ?>">Delete</span>
								</div>


							</div>
						</div>
					<?php else: ?>
						<!-- Other user's message -->
						<div class="message-sender text-start ">
							<div class="alert alert-secondary">
								<strong><?php echo h($message['Sender']['name']); ?></strong>
								<div class="message-body">
									<p class="message-text"><?php echo $messageBody; ?></p>
									<?php if (strlen($messageBody) > 100): ?>
										<span class="read-more">Read More</span>
									<?php endif; ?>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<small class="text-muted"><?php echo h($message['Message']['createdAt']); ?></small>
									<span class="deleteMessage btn btn-danger" data-message-id="<?php echo h($message['Message']['id']); ?>">Delete</span>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

		<?php else: ?>
			<div>No messages in this conversation yet.</div>
		<?php endif; ?>
	</div>


	<?php if ($totalMessages > 5): ?>
		<button id="loadMore" class="btn btn-secondary" data-offset="5">
			Load More Messages
		</button>
	<?php endif; ?>
</div>

<script>
	$(document).ready(function() {
		// Function to create message HTML
		function createMessageHtml(message) {
			var currentUserId = <?php echo AuthComponent::user('id'); ?>;
			var messageBody = message.body;
			var messageHtml = '';

			// Check if the message is from the current user
			if (parseInt(message.senderId) === currentUserId) {
				// User's own message (aligned to the right)
				messageHtml += '<div class="message-card mb-2">';
				messageHtml += '<div class="message-sender text-end">';
				messageHtml += '<div class="alert alert-primary float-end">';
				messageHtml += '<strong>' + message.senderName + '</strong>';
				messageHtml += '<div class="message-body">';
				messageHtml += '<p class="message-text">' + messageBody + '</p>';
				if (messageBody.length > 100) {
					messageHtml += '<span class="read-more">Read More</span>';
				}
				messageHtml += '</div>';
				messageHtml += '<div class="d-flex justify-content-between align-items-center">';
				messageHtml += '<small class="text-muted">' + message.createdAt + '</small>';
				messageHtml += '<span class="deleteMessage btn btn-danger" data-message-id="' + <?php echo h($message['Message']['id']); ?> + '">Delete</span>';
				messageHtml += '</div></div></div></div>';
			} else {
				// Other user's message (aligned to the left)
				messageHtml += '<div class="message-card mb-2">';
				messageHtml += '<div class="message-sender text-start">';
				messageHtml += '<div class="alert alert-secondary">';
				messageHtml += '<strong>' + message.senderName + '</strong>';
				messageHtml += '<div class="message-body">';
				messageHtml += '<p class="message-text">' + messageBody + '</p>';
				if (messageBody.length > 100) {
					messageHtml += '<span class="read-more">Read More</span>';
				}
				messageHtml += '</div>';
				messageHtml += '<div class="d-flex justify-content-between align-items-center">';
				messageHtml += '<small class="text-muted">' + message.createdAt + '</small>';
				messageHtml += '<span class="deleteMessage btn btn-danger" data-message-id="' + message.id + '">Delete</span>';
				messageHtml += '</div></div></div></div>';
			}
			return messageHtml;
		}

		$('#searchMessage').on('input', function() {
			var searchText = $(this).val(); // Get the input value
			var conversationId = '<?php echo $conversationId; ?>'; // Pass the conversation ID

			$.ajax({
				url: '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'searchMessages', $conversationId]); ?>',
				method: 'GET',
				data: {
					body: searchText // Correct key to match the controller
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						var messageHtml = '';

						// Loop through the returned messages
						$.each(response.messages, function(index, message) {
							// Use the createMessageHtml function to generate HTML for each message
							messageHtml += createMessageHtml(message);
						});

						// Update the message display
						$('#messageExchange').html(messageHtml);
						$('#noResults').hide(); // Hide no results message
					} else {
						$('#messageExchange').html('<div>No messages found.</div>'); // Display no results message
						$('#noResults').show(); // Optionally show a no results message
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', error);
					alert('An error occurred while searching messages.');
				}
			});
		});

		$('#replyMessageForm').on('submit', function(e) {
			e.preventDefault(); // Prevent default form submission

			$.ajax({
				url: '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'replyMessage']); ?>',
				method: 'POST',
				data: $(this).serialize(), // Serialize form data
				dataType: 'json',
				success: function(response) {
					console.log(response); // Log the response to check it

					if (response.success) {
						// Create the message HTML using the new function
						var messageHtml = createMessageHtml(response);
						// Append the new message HTML to the message exchange area
						$('#messageExchange').prepend(messageHtml);

						// Scroll to the bottom of the message exchange area
						$('#messageExchange').scrollTop($('#messageExchange')[0].scrollHeight);

						// Optionally clear the input field
						$('#message').val('');
					} else {
						// Handle validation errors if any
						console.log(response.errors);
						alert('Unable to send message. Please try again.');
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', error);
					alert('An error occurred while sending the message.');
				}
			});
		});

		$('#loadMore').on('click', function() {
			var offset = $(this).data('offset'); // Get the current offset
			$.ajax({
				url: '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'loadMoreMessages', $conversationId]); ?>/' + offset,
				method: 'GET',
				dataType: 'json',
				success: function(response) {
					console.log(response); // Debugging log to see the response
					if (response.success) {
						var messageHtml = ''; // Initialize variable to hold message HTML

						// Loop through the returned messages to build HTML
						$.each(response.messages, function(index, message) {
							messageHtml += createMessageHtml(message);
						});

						// Append the new messages HTML to the message exchange area
						$('#messageExchange').append(messageHtml);

						// Update the offset for the next request
						$('#loadMore').data('offset', offset + 5);

						// Hide the button if no more messages
						if (response.noMoreMessages) {
							$('#loadMore').hide();
						}
					} else {
						alert('Unable to load more messages. Please try again.');
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', error);
					alert('An error occurred while loading more messages.');
				}
			});
		});
		//read more event
		$(document).on('click', '.read-more', function() {
			var messageText = $(this).siblings('.message-text'); // Get the message text element

			// Toggle the expanded class
			if (messageText.hasClass('expanded')) {
				messageText.removeClass('expanded');
				$(this).text('Read More');
			} else {
				messageText.addClass('expanded');
				$(this).text('Read Less');
			}
		});
		//delete event
		$(document).on('click', '.deleteMessage', function() {
			var messageId = $(this).data('message-id');

			if (confirm('Are you sure you want to delete this message?')) {
				$.ajax({
					url: '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'deleteMessage']); ?>',
					method: 'POST',
					data: {
						id: messageId
					},
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							alert(response.message); // Optional: Display success message
							// Remove the message from the DOM
							$(this).closest('.message-card').fadeOut(400, function() {
								$(this).remove();
							});
						} else {
							alert(response.error); // Display error message if deletion fails
						}
					}.bind(this), // Ensure 'this' refers to the clicked element
					error: function(xhr, status, error) {
						console.error('AJAX Error:', error);
						alert('An error occurred while deleting the message.');
					}
				});
			}
		});

	});
</script>

<style>
	.message-text {
		cursor: pointer;
		max-height: 80px;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	.message-text.expanded {
		max-height: none;
		white-space: normal;
	}
</style>