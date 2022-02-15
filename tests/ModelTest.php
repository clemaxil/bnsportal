<?php
define('APP_ENTRY', 'myApp');

use App\Model;
use PHPUnit\Framework\TestCase;


class ModelTest extends TestCase
{
	/**
	 * 
	 * @var mixed
	 */
	protected static $id;


	/**
	 * 
	 * @return void 
	 */
	public function testModelConstructor()
	{
		$this->assertInstanceOf(App\Model::class, new Model());
	}


	/**
	 * 
	 * @return void 
	 */
	public function testModelSetConnection()
	{
		$model = new Model();
		$this->assertTrue($model->setConnection());
	}

	/**
	 * 
	 * @return void 
	 */
	public function testModelGetInstance()
	{
		$model = new Model();
		$this->assertInstanceOf(App\Model::class, $model::getInstance());
	}

	/**
	 * 
	 * @return void 
	 */
	public function testModelInsert()
	{
		$model = new Model();
		$model->table = 'phpunit';
		$query = $model->insert('phpunit', ['first_name' => 'Eric', 'last_name' => 'Clemaxil']);
		$this->assertTrue($query);
	}


	/**
	 * 
	 * @return void 
	 */
	public function testModelSelect()
	{
		$model = new Model();
		$select = $model->select('phpunit', ['first_name' => 'Eric'], 'id ASC', 10, 0);
		self::$id = $select[0]['id'];
		$this->assertMatchesRegularExpression('#Eric#', $select[0]['first_name'], 'Model select');
	}


	/**
	 * 
	 * @return void 
	 */
	public function testModelUpdate()
	{
		$model = new Model();
		$update = $model->update('phpunit', self::$id, ['first_name' => 'Eric2']);
		$this->assertEquals(1, $update);
	}


	/**
	 * 
	 * @return void 
	 */
	public function testModelQuery()
	{
		$model = new Model();
		$statement = $model->query('select first_name from phpunit where id=1');
		$result = $statement->fetchAll();
		$this->assertMatchesRegularExpression('#David#', $result[0]['first_name'], 'Model select');
	}



	/**
	 * 
	 * @return void 
	 */
	public function testModelDeleteSoft()
	{
		$model = new Model();
		$query = $model->delete('phpunit', self::$id);
		$this->assertEquals(1, $query);
	}


	/**
	 * 
	 * @return void 
	 */
	public function testModelDeleteHard()
	{
		$model = new Model();
		$query = $model->delete('phpunit', self::$id, 'hard');
		$this->assertEquals(1, $query);
	}
}
