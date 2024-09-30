<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel
{

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name field required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid email address.',
				'required' => true,
				'allowEmpty' => false
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This email is already in use.',
				'on' => 'create' // This can be omitted if you want it to apply to both create and update operations
			),
		),
		'password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'on' => 'create',
			),
			'matchPassword' => array(
				'rule' => 'matchPasswords', // Custom validation method to confirm password match
				'message' => 'Passwords do not match.',
				'last' => true
			)
		),
		'confirm_password' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter your confirm password.',
				'required' => true,
				'allowEmpty' => false,
				'last' => false,
				'on' => 'create'
			),
		),

		'gender' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			)
		),

		'hobby' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'allowEmpty' => true,

			)
		),

		'profilePic' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'allowEmpty' => true,
			),
		),
		'createdAt' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			),
		),
		'lastLogin' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			),
		),

	);
	public function matchPasswords($data)
	{
		if ($data['password'] === $this->data['User']['confirm_password']) {
			return true;
		}
		return false;
	}

	// Hash password before saving the user
	public function beforeSave($options = array())
	{
		// Hash the password if it's present and not empty
		if (isset($this->data['User']['password'])) {
			$this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
		}
		return true;
	}
}
