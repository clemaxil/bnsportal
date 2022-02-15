<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;



/**
 * Main Model
 * @package MainModel
 */
class Model
{
    /**
     *
     * @var string
     */
    private $db_hostname;

    /**
     *
     * @var string
     */
    private $db_username;

    /**
     *
     * @var string
     */
    private $db_password;

    /**
     *
     * @var string
     */
    private $db_name;

    /**
     *
     * @var string
     */
    private $db_dsn;

    /**
     *
     * @var PDO
     */
    protected $db = null;

    /**
     *
     * @var Model
     */
    public static $instance = null;

    /**
     *
     * @var string
     */
    public $table;

    /**
     *
     * @var string
     */
    public $id;

    /**
     * 
     * @return void 
     */
    public function __construct()
    {
        $app_config = include(__DIR__ . '/../config.php');
        $this->db_hostname = $app_config['db_hostname'];
        $this->db_username = $app_config['db_username'];
        $this->db_password = $app_config['db_password'];
        $this->db_name = $app_config['db_name'];
        $this->db_dsn = $app_config['db_dsn'];
        $this->setConnection();
    }


    /**
     * 
     * @return string|true 
     */
    public function setConnection()
    {
        $dsn = $this->db_dsn . ":dbname=" . $this->db_name . ";host=" . $this->db_hostname . ";charset=utf8";
        $this->db = new PDO($dsn, $this->db_username, $this->db_password);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec('SET NAMES utf8');
        return true;
    }

    /**
     *
     * @return Model
     */
    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new Model;
        }
        return self::$instance;
    }


    /**
     *
     * @param string $table
     * @param string[] $where
     * @param string $orderby
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function select($table, $where, $orderby = '', $limit = -1, $offset = -1)
    {
        $query = 'SELECT * FROM ' . $table . ' WHERE ';
        foreach (array_keys($where) as $key) {
            $query .= $key . ' = :' . $key . ' AND ';
        }
        $query = substr($query, 0, -4) . ' AND deleted_at IS NULL';

        if (!empty($orderby)) {
            $query .= ' ORDER BY ' . $orderby;
        }

        if ($limit >= 0) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset >= 0) {
            $query .= ' OFFSET ' . $offset;
        }

        $statement = $this->db->prepare($query);

        foreach ($where as $key => $val) {
            $statement->bindValue(':' . $key, $val);
        }

        $statement->execute();
        return $statement->fetchAll();
    }



    /**
     *
     * @param string $table
     * @param string[] $fields id is mandatory
     * @return bool
     */
    public function insert(string $table, array $fields)
    {
        $query = 'INSERT INTO ' . $table . ' (created_at, updated_at, ';

        foreach (array_keys($fields) as $key) {
            $query .= $key . ', ';
        }
        $query = substr($query, 0, -2);

        $query .= ') VALUES (UTC_TIMESTAMP(), UTC_TIMESTAMP(), ';

        foreach (array_keys($fields) as $key) {
            $query .= ':' . $key . ', ';
        }
        $query = substr($query, 0, -2);
        $query .= ')';


        $statement = $this->db->prepare($query);

        foreach ($fields as $key => $val) {
            $statement->bindValue(':' . $key, $val);
        }

        $statement->execute();
        return true;
    }




    /**
     *
     * @param string $table
     * @param string $id
     * @param string[] $fields
     * @return bool
     */
    public function update($table, $id, $fields)
    {
        $query = 'UPDATE ' . $table . ' SET updated_at = UTC_TIMESTAMP(), ';

        foreach (array_keys($fields) as $key) {
            $query .= $key . ' = :' . $key . ', ';
        }
        $query = substr($query, 0, -2);

        $query .= ' WHERE id = \'' . $id . '\'';


        $statement = $this->db->prepare($query);

        foreach ($fields as $key => $val) {
            $statement->bindValue(':' . $key, $val);
        }

        $statement->execute();
        return $statement->rowCount() !== false;
    }




    /**
     *
     * @param string $table
     * @param string $id
     * @param string $mode
     * @return mixed
     */
    public function delete($table, $id, $mode = "soft")
    {
        $query = $query = 'UPDATE ' . $table . ' SET deleted_at = UTC_TIMESTAMP()  WHERE id=\'' . $id . '\'';

        if ($mode == "soft") {
            $query = 'UPDATE ' . $table . ' SET deleted_at = UTC_TIMESTAMP()  WHERE id=\'' . $id . '\'';
        }

        if ($mode == "hard") {
            $query = 'DELETE FROM ' . $table . ' WHERE id=\'' . $id . '\'';
        }

        $statement = $this->db->prepare($query);
        $statement->execute();
        return $statement->rowCount();
    }



    /**
     *
     * @param string $query
     * @return mixed
     */
    public function query($query)
    {
        $statement = $this->db->prepare($query);
        $statement->execute();
        return $statement;
    }
}
