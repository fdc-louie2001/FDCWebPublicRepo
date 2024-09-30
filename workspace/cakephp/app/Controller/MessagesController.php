<?php
App::uses('AppController', 'Controller');
/**
 * Messages Controller
 */
class MessagesController extends AppController
{
	/**
	 * Scaffold
	 *
	 * @var mixed
	 */
	public $scaffold;
	public $uses = ['User', 'Message'];

	public function generateConversationId($senderId, $receiverId)
	{
		// Create a unique string by concatenating the IDs
		return md5(min($senderId, $receiverId) . max($senderId, $receiverId));
	}

	public function inbox()
	{
		//to render the inbox.ctp
	}

	// public function getAllMessage()
	// {
	// 	if ($this->request->is('ajax') && $this->request->is('get')) {

	// 		$this->autoRender = false;
	// 		$userId = AuthComponent::user('id');

	// 		// Fetch the first 5 conversations
	// 		$limit = 5;

	// 		$allMessage = $this->Message->find('all', [
	// 			'conditions' => [
	// 				'OR' => [
	// 					'Message.receiverId' => $userId,
	// 					'Message.senderId' => $userId
	// 				]
	// 			],
	// 			'contain' => ['Sender', 'Receiver', 'Conversation'],
	// 			'order' => ['Message.createdAt' => 'DESC'],
	// 			'limit' => $limit
	// 		]);

	// 		$groupedMessages = [];
	// 		foreach ($allMessage as $message) {
	// 			$conversationId = $message['Message']['conversationId'];
	// 			if (!isset($groupedMessages[$conversationId])) {
	// 				$groupedMessages[$conversationId] = [
	// 					'conversationId' => $conversationId,
	// 					'messages' => [],
	// 					'lastMessage' => '',
	// 					'lastMessageTime' => null,
	// 					'senderId' => null,
	// 					'senderName' => null,
	// 					'profilePic' => null,
	// 				];
	// 			}
	// 			$groupedMessages[$conversationId]['messages'][] = $message['Message'];

	// 			if (
	// 				empty($groupedMessages[$conversationId]['lastMessageTime']) ||
	// 				strtotime($message['Message']['createdAt']) > ($groupedMessages[$conversationId]['lastMessageTime'])
	// 			) {
	// 				$groupedMessages[$conversationId]['lastMessage'] = $message['Message']['body'];
	// 				$groupedMessages[$conversationId]['lastMessageTime'] = $message['Message']['createdAt'];

	// 				if ($message['Message']['senderId'] === $userId) {
	// 					$groupedMessages[$conversationId]['senderId'] = $message['Message']['receiverId'];
	// 					$groupedMessages[$conversationId]['senderName'] = $message['Receiver']['name'];
	// 					$groupedMessages[$conversationId]['profilePic'] = $message['Receiver']['profilePic'];
	// 				} else {
	// 					$groupedMessages[$conversationId]['senderId'] = $message['Message']['senderId'];
	// 					$groupedMessages[$conversationId]['senderName'] = $message['Sender']['name'];
	// 					$groupedMessages[$conversationId]['profilePic'] = $message['Sender']['profilePic'];
	// 				}
	// 			}
	// 		}

	// 		$uniqueSenders = [];
	// 		foreach ($groupedMessages as $conversation) {
	// 			$uniqueSenders[] = [
	// 				'id' => $conversation['senderId'],
	// 				'name' => $conversation['senderName'],
	// 				'profilePic' => $conversation['profilePic'],
	// 				'conversationId' => $conversation['conversationId'],
	// 				'lastMessage' => $conversation['lastMessage'],
	// 				'lastMessageTime' => $conversation['lastMessageTime'],
	// 			];
	// 		}

	// 		$response = [
	// 			'status' => 'success',
	// 			'uniqueSenders' => $uniqueSenders,
	// 			'hasMore' => count($allMessage) === $limit // Check if more messages exist
	// 		];
	// 		return json_encode($response);
	// 	}
	// }





	public function getAllMessage()
	{
		if ($this->request->is('ajax') && $this->request->is('get')) {

			$this->autoRender = false;
			$userId = AuthComponent::user('id');

			$allMessage = $this->Message->find('all', [
				'conditions' => [
					'OR' => [
						'Message.receiverId' => $userId,
						'Message.senderId' => $userId
					]
				],
				'contain' => ['Sender', 'Receiver', 'Conversation'],
				'order' => ['Message.createdAt' => 'DESC'],
			]);

			$groupedMessages = [];
			foreach ($allMessage as $message) {
				$conversationId = $message['Message']['conversationId'];
				if (!isset($groupedMessages[$conversationId])) {
					$groupedMessages[$conversationId] = [
						'conversationId' => $conversationId,
						'messages' => [],
						'lastMessage' => '',
						'lastMessageTime' => null,
						'senderId' => null,
						'senderName' => null,
						'profilePic' => null,
					];
				}
				$groupedMessages[$conversationId]['messages'][] = $message['Message'];

				if (
					empty($groupedMessages[$conversationId]['lastMessageTime']) ||
					strtotime($message['Message']['createdAt']) > ($groupedMessages[$conversationId]['lastMessageTime'])
				) {

					$groupedMessages[$conversationId]['lastMessage'] = $message['Message']['body'];
					$groupedMessages[$conversationId]['lastMessageTime'] = $message['Message']['createdAt'];


					if ($message['Message']['senderId'] === $userId) {

						$groupedMessages[$conversationId]['senderId'] = $message['Message']['receiverId'];
						$groupedMessages[$conversationId]['senderName'] = $message['Receiver']['name'];
						$groupedMessages[$conversationId]['profilePic'] = $message['Receiver']['profilePic'];
					} else {

						$groupedMessages[$conversationId]['senderId'] = $message['Message']['senderId'];
						$groupedMessages[$conversationId]['senderName'] = $message['Sender']['name'];
						$groupedMessages[$conversationId]['profilePic'] = $message['Sender']['profilePic'];
					}
				}
			}

			$uniqueSenders = [];
			foreach ($groupedMessages as $conversation) {
				$uniqueSenders[] = [
					'id' => $conversation['senderId'],
					'name' => $conversation['senderName'],
					'profilePic' => $conversation['profilePic'],
					'conversationId' => $conversation['conversationId'],
					'lastMessage' => $conversation['lastMessage'],
					'lastMessageTime' => $conversation['lastMessageTime'],
				];
			}

			$response = [
				'status' => 'success',
				'uniqueSenders' => $uniqueSenders
			];
			return json_encode($response);
		}
	}

	public function send_message()
	{
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$data = $this->request->data;
			$senderId = $this->Auth->user('id');
			$receiverId = $data['Message']['receiverId'];
			$sentMessage = $data['Message']['sentMessage'];

			// Generate the conversation ID using the senderId and receiverId
			$conversationId = $this->generateConversationId($senderId, $receiverId);

			$message = [
				'Message' => [
					'senderId' => $senderId,
					'receiverId' => $receiverId,
					'body' => $sentMessage,
					'conversationId' => $conversationId,
					'createdAt' => date('Y-m-d H:i:s'),
				]
			];

			if ($this->Message->save($message)) {
				$response = [
					'status' => 'success',
					'message' => 'Message sent successfully'
				];
			} else {
				$response = [
					'status' => 'error',
					'message' => 'Error sending message',
					'errors' => $this->Message->validationErrors
				];
			}

			echo json_encode($response);
			return;
		}

		$users = $this->User->find('list', [
			'fields' => ['User.id', 'User.name'],
			'order' => ['User.name' => 'ASC']
		]);

		$this->set(compact('users'));
	}

	public function replyMessage()
	{
		$this->autoRender = false;

		if ($this->request->is('ajax')) {
			$this->Message->create();
			$senderId = AuthComponent::user('id');
			$receiverId = null;
			$conversationId = isset($this->request->data['User']['conversationId']) ? $this->request->data['User']['conversationId'] : null;

			if (!empty($conversationId)) {
				$existingMessage = $this->Message->find('first', [
					'conditions' => ['Message.conversationId' => $conversationId],
					'fields' => ['Message.senderId', 'Message.receiverId']
				]);

				if (!empty($existingMessage)) {
					$receiverId = $existingMessage['Message']['senderId'] === $senderId ? $existingMessage['Message']['receiverId'] : $existingMessage['Message']['senderId'];
				} else {
					$receiverId = $this->request->data['User']['user_id'];
				}
			} else {
				// New conversation
				$receiverId = $this->request->data['User']['user_id'];
				$conversationId = $this->generateConversationId($senderId, $receiverId);
			}

			$data = [
				'Message' => [
					'senderId' => $senderId,
					'receiverId' => $receiverId,
					'body' => $this->request->data['body'],
					'conversationId' => $conversationId,
					'createdAt' => date('Y-m-d H:i:s'),
				]
			];

			if ($this->Message->save($data)) {

				echo json_encode([
					'success' => true,
					'senderId' => $senderId,
					'receiverId' => $receiverId,
					'body' => $data['Message']['body'],
					'createdAt' => date('Y-m-d H:i:s'),
					'senderName' => AuthComponent::user('name')
				]);
			} else {
				$errors = $this->Message->validationErrors;
				echo json_encode([
					'success' => false,
					'errors' => $errors
				]);
			}
		}
	}

	public function view_conversation($conversationId, $offset = 0)
	{
		$limit = 5;

		$messages = $this->Message->find('all', [
			'conditions' => ['Message.conversationId' => $conversationId],
			'contain' => ['Users'],
			'order' => ['Message.createdAt' => 'DESC'],
			'limit' => $limit,
			'offset' => $offset
		]);

		$totalMessages = $this->Message->find('count', [
			'conditions' => ['Message.conversationId' => $conversationId]
		]);

		if (!empty($messages)) {
			$firstMessage = $messages[0];
			$receiverId = $firstMessage['Message']['senderId'] === $this->Auth->user('id')
				? $firstMessage['Message']['receiverId']
				: $firstMessage['Message']['senderId'];

			$otherUser = $this->User->findById($receiverId);
			$otherUserName = !empty($otherUser) ? $otherUser['User']['name'] : 'Unknown User';

			$this->set(compact('conversationId', 'receiverId', 'messages', 'totalMessages', 'otherUserName'));
		} else {

			$this->Flash->error(__('No messages found for this conversation.'));
			return $this->redirect(['action' => 'inbox']);
		}
	}

	public function loadMoreMessages($conversationId, $offset)
	{
		$this->autoRender = false;
		$limit = 5;

		$messages = $this->Message->find('all', [
			'conditions' => ['Message.conversationId' => $conversationId],
			'contain' => ['Sender'],
			'order' => ['Message.createdAt' => 'DESC'],
			'limit' => $limit,
			'offset' => $offset
		]);

		$noMoreMessages = count($messages) < $limit;

		$responseMessages = [];
		if (!empty($messages)) {
			foreach ($messages as $message) {
				$responseMessages[] = [
					'senderId' => $message['Sender']['id'],
					'senderName' => h($message['Sender']['name']),
					'body' => h($message['Message']['body']),
					'createdAt' => h($message['Message']['createdAt']),
				];
			}
		}

		if ($this->request->is('ajax')) {
			$this->response->type('application/json');
			$response = [
				'success' => true,
				'messages' => $responseMessages,
				'noMoreMessages' => $noMoreMessages,
			];
			$this->response->body(json_encode($response));
			return $this->response;
		} else {

			return $this->redirect(['action' => 'index']);
		}
	}


	public function searchMessages($conversationId = null)
	{
		$this->autoRender = false;
		$searchQuery = $this->request->query('body');

		$messages = $this->Message->find('all', [
			'conditions' => [
				'Message.conversationId' => $conversationId,
				'Message.body LIKE' => '%' . $searchQuery . '%'
			],
			'contain' => ['Users'],
			'order' => ['Message.createdAt' => 'DESC']
		]);

		if (!empty($messages)) {
			$data = [];
			foreach ($messages as $message) {
				$data[] = [
					'senderId' => $message['Message']['senderId'],
					'senderName' => $message['Sender']['name'],
					'body' => $message['Message']['body'],
					'createdAt' => $message['Message']['createdAt']
				];
			}
			echo json_encode([
				'success' => true,
				'messages' => $data
			]);
		} else {
			echo json_encode([
				'success' => false,
				'messages' => [],
				'message' => 'No messages found.'
			]);
		}
	}

	public function deleteMessage()
	{
		$this->autoRender = false;
		if ($this->request->is('ajax')) {

			$id = $this->request->data('id');
			$message = $this->Message->findById($id);

			if ($message) {
				$currentUserId = $this->Auth->user('id');

				if ($message['Message']['senderId'] === $currentUserId || $message['Message']['receiverId'] === $currentUserId) {

					if ($this->Message->delete($id)) {
						$this->response->type('json');
						$this->response->body(json_encode(['success' => true, 'message' => 'Message deleted successfully.']));
					} else {
						$this->response->type('json');
						$this->response->body(json_encode(['success' => false, 'error' => 'Could not delete the message.']));
					}
				} else {
					$this->response->type('json');
					$this->response->body(json_encode(['success' => false, 'error' => 'You are not authorized to delete this message.']));
				}
			} else {
				$this->response->type('json');
				$this->response->body(json_encode(['success' => false, 'error' => 'Message not found.']));
			}
		} else {
			$this->Flash->error(__('Invalid request.'));
			return $this->redirect(['action' => 'index']);
		}
	}

	public function deleteConversation($conversationId = null)
	{
		if ($this->request->is('ajax') && $conversationId) {
			$this->autoRender = false;

			// Delete all messages associated with the conversation ID
			$deletedCount = $this->Message->deleteAll(['Message.conversationId' => $conversationId]);

			if ($deletedCount) {
				$response = [
					'status' => 'success',
					'message' => 'Conversation deleted successfully.'
				];
			} else {
				$response = [
					'status' => 'error',
					'message' => 'Failed to delete conversation.'
				];
			}

			return json_encode($response);
		}
	}
}
