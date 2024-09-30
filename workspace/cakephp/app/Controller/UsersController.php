<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	/**
	 * index method
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		$this->Auth->allow('add', 'index', 'register');
	}


	public function login()
	{
		$this->autoRender = false; // Disable default view rendering

		if ($this->request->is('post')) {
			$response = [];
			if ($this->Auth->login()) {
				$userId = $this->Auth->user('id');
				if ($userId) {
					$this->User->id = $userId;

					// Try to save the lastLogin field
					if (!$this->User->saveField('lastLogin', date('Y-m-d H:i:s'))) {
						$response = [
							'success' => false,
							'message' => 'Could not update last login time.',
							'errors' => $this->User->validationErrors
						];
					} else {
						$response = [
							'success' => true,
							'message' => 'Account logged in successfully!',
						];
					}
				}
			} else {
				$response = [
					'success' => false,
					'message' => 'Invalid username or password, try again.',
				];
			}

			$this->response->type('json');
			echo json_encode($response);
			return;
		}

		$this->set('title_for_layout', __('Login'));
		$this->render("login");
	}



	public function logout()
	{
		$this->Auth->logout();
		return $this->redirect(['controller' => 'users', 'actions' => 'login']);
	}

	public function index()
	{
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null)
	{
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */

	public function register()
	{
		$this->autoRender = false; // Disable default view rendering

		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$response = [
					'success' => true,
					'message' => 'Account created successfully!',
				];
			} else {
				$response = [
					'success' => false,
					'message' => array_values($this->User->validationErrors) // Format for better error handling
				];
			}

			// Set the response type to JSON and return the response
			$this->response->type('json');
			$this->response->body(json_encode($response));
			return;
		}
		$this->set('title_for_layout', __('Registration'));
		$this->render("register");
	}

	public function change_password() {
        $this->autoRender = false; // Disable view rendering
    
        // Check if the request is POST
        if ($this->request->is('post')) {
            $userId = $this->Auth->user('id'); // Get the currently logged-in user ID
            $user = $this->User->findById($userId);
    
            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                return;
            }
    
            $currentPassword = $this->request->data['currentPassword'];
            $newPassword = $this->request->data['newPassword'];
    

            // Verify current password
            if (AuthComponent::password($currentPassword) !== $user['User']['password']) {
                echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
                return;
            }

            // Update the password with the new one
            $this->User->id = $userId; // Set the user ID
            $this->User->saveField('password', $newPassword); // Save new password
    
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    
        // Set response type to JSON
        $this->response->type('application/json');
    }






	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null)
	{
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}

		if ($this->request->is(['post', 'put'])) {
			$this->User->id = $id;

			// Handle profile picture upload
			if (!empty($this->request->data['User']['profilePic']['name'])) {
				$file = $this->request->data['User']['profilePic'];
				$uploadPath = WWW_ROOT . 'img' . DS . 'uploads' . DS;

				// Ensure the uploads directory exists
				if (!file_exists($uploadPath)) {
					mkdir($uploadPath, 0755, true);
				}

				// Generate a unique filename
				$filename = time() . '_' . basename($file['name']);
				$fullPath = $uploadPath . $filename;

				// Move the uploaded file
				if (move_uploaded_file($file['tmp_name'], $fullPath)) {
					$this->request->data['User']['profilePic'] = 'uploads/' . $filename; // Save the relative path
				} else {
					$this->Flash->error(__('Unable to upload the profile picture.'));
				}
			} else {
				// If no new file is uploaded, retain the existing profile picture
				unset($this->request->data['User']['profilePic']); // Make sure this is not set to an array
			}


			// Save the user data
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect('/messages/index');
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		} else {
			// Load existing user data into $this->request->data
			$options = ['conditions' => ['User.' . $this->User->primaryKey => $id]];
			$this->request->data = $this->User->find('first', $options);
		}
	}


	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null)
	{
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete($id)) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
