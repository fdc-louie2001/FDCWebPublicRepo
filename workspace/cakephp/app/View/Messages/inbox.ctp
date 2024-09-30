<div class="container mt-2 p-3">
	<div class="d-flex justify-content-between">
		<span class="h5">Message Board</span>
		<?php echo $this->Html->link('Send Message', ['controller' => 'messages', 'action' => 'send_message'], ['class' => 'btn btn-success', 'type' => 'button']); ?>
	</div>
	<div id="messagesList" class="container mt-3">
		<!-- render the users who sent you a message -->
	</div>
</div>
<style>
	.container {
		max-width: 600px;
		margin: auto;
		padding: 20px;
		background-color: #f9f9f9;
		border-radius: 10px;
	}

	.message-item {
		display: flex;
		align-items: center;
		margin-bottom: 10px;
		padding: 10px;
		border: 1px solid #ddd;
		border-radius: 8px;
		background-color: #fff;
		transition: background-color 0.3s;
	}

	.message-item-link {
		text-decoration: none;
		color: inherit;
	}

	.message-item:hover {
		background-color: #f0f0f0;
	}


	.profile-pic {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		margin-right: 10px;
	}


	.message-info {
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	.sender-name {
		font-weight: bold;
		color: #333;
	}

	.last-message,
	.last-message-time {
		font-size: 0.9em;
		color: #666;
	}

	.last-message {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		width: 450px;
		max-width: 450px;
		display: inline-block;
	}

	.message-item-link:hover .sender-name,
	.message-item-link:hover .last-message,
	.message-item-link:hover .last-message-time {
		text-decoration: none;
	}
</style>

<script>
	$(document).ready(function() {
		// Fetch all messages on page load
		$.ajax({
			url: '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'getAllMessage']); ?>',
			method: 'GET',
			dataType: 'json',
			success: function(response) {
				if (response.status === 'success') {
					let uniqueSenders = response.uniqueSenders;
					$('#messagesList').empty();

					uniqueSenders.forEach(function(sender) {
						let profilePic = sender.profilePic.startsWith('http') || sender.profilePic.startsWith('img/') ?
							sender.profilePic :
							'img/' + sender.profilePic;

						let senderHtml = `
						<div class="message-item border border-dark d-flex justify-content-between align-items-center">
   <a href="<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'view_conversation']); ?>/${sender.conversationId}" class="message-item-link d-flex align-items-center" style="flex: 1; max-width: 87%;">
    <img src="${profilePic}" alt="${sender.name}" class="profile-pic" />
    <div class="message-info">
        <span class="sender-name">${sender.name}</span>
        <span class="last-message">${sender.lastMessage}</span>
        <span class="last-message-time">${sender.lastMessageTime}</span>
    </div>
</a>
    <button class="btn btn-danger btn-sm delete-conversation pr-10" data-id="${sender.conversationId}">Delete</button>
</div>

`;

						$('#messagesList').append(senderHtml);
					});

					// Attach event listener to delete buttons
					$('.delete-conversation').on('click', function() {
						const conversationId = $(this).data('id');
						if (confirm('Are you sure you want to delete this conversation?')) {
							deleteConversation(conversationId);
						}
					});

				} else {
					console.log('Error fetching messages');
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX Error:', error);
			}
		});
	});

	function deleteConversation(conversationId) {
		$.ajax({
			url: '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'deleteConversation']); ?>/' + conversationId,
			method: 'DELETE', // Use DELETE method for deletion
			dataType: 'json',
			success: function(response) {
				if (response.status === 'success') {
					alert(response.message);
					// Remove the deleted conversation from the DOM
					$('button[data-id="' + conversationId + '"]').closest('.message-item').fadeOut(400, function() {
						$(this).remove();
					});
				} else {
					alert(response.message);
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX Error:', error);
			}
		});
	}
</script>