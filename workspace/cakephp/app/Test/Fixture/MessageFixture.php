<?php
/**
 * Message Fixture
 */
class MessageFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'senderId' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'receiverId' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_0900_ai_ci', 'charset' => 'utf8mb4'),
		'conversationId' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_0900_ai_ci', 'charset' => 'utf8mb4'),
		'createdAt' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_0900_ai_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'senderId' => 1,
			'receiverId' => 1,
			'body' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'conversationId' => 'Lorem ipsum dolor sit amet',
			'createdAt' => '2024-09-29 08:12:22'
		),
	);

}
