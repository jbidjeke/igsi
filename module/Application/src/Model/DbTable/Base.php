<?php
namespace Application\Model\DbTable;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

class Base extends AbstractTableGateway
{

    protected $table = 'empty'; // cette table n'existe pas
    
    public  function getAdapter()
    {   
        return $this;
    }
    
    
    public  function setAdapter($db)
    {
        $this->adapter;
    }
	
    /**
    * fetch query 
    *
    * @param  string | \Zend\Db\Sql\Select $select
    * @param  null|array $parameters
    * @param  null|string $type; e.g  ($type = array or object)
    * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
    * @return \Zend\Db\ResultSet\ResultSetInterface  | Array
    */
    public function fetchAll($select = null, $parameters = null, $type = null)
    { 
            if ($select instanceof ResultSet)		    
                return $resultSet = $select; 

            if (func_num_args() == 0){
                $select = $this->sql->buildSqlString($this->select());
            }
        elseif ($select instanceof Select){
                $select = $this->sql->buildSqlString($select);
            }
    elseif (!is_string($select))
                throw new \Zend\Db\Adapter\Exception\InvalidArgumentException('Parameter to this method must be null, a sql query string, or Zend\Db\Sql\Select');  

            $statement = $this->adapter->createStatement($select);
            $statement->prepare();
            $result = $statement->execute($parameters);
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet;
                $resultSet->initialize($result);

                if ($type != null)  
            return $resultSet->toArray();

                return $resultSet;

            }else
                throw new \Zend\Db\Adapter\Exception\InvalidArgumentException('Invalid execution, bad request');

    }


    /**
     * fetch query
     *
     * @param  string | \Zend\Db\Sql\Select $select
     * @param  null|array $parameters
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     * @return Array
     */
    public function fetchAllToArray($select = null, $parameters = null)
    {


        if ($select instanceof ResultSet)
            return $resultSet = $select;

            if (func_num_args() == 0){
                $select = $this->sql->buildSqlString($this->select());
            }
            elseif ($select instanceof Select){
                $select = $this->sql->buildSqlString($select);
            }
            elseif (!is_string($select))
            throw new \Zend\Db\Adapter\Exception\InvalidArgumentException('Parameter to this method must be null, a sql query string, or Zend\Db\Sql\Select');

            $statement = $this->adapter->createStatement($select);
            $statement->prepare();
            $result = $statement->execute($parameters);
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet;
                $resultSet->initialize($result);

                return $resultSet->toArray();


            }else
                throw new \Zend\Db\Adapter\Exception\InvalidArgumentException('Invalid execution, bad request');

    }




    /**
     * fetch row
     *
     * @param  string | \Zend\Db\Sql\Select $select
     * @param  null|array $parameters
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     * @return array
     */
    public function fetchRow($select = null, $parameters = null)
    {
    if (!is_string($select))
            throw new \Zend\Db\Adapter\Exception\InvalidArgumentException('Parameter to this method must be null, a sql query string');

        if ($parameters != null)     	
            // Query the database:
            $resultSet = $this->adapter->query($select, $parameters);
        else 
            $resultSet = $this->adapter->query($select);
        // Get array of data:
        return $rowData = $resultSet->current()->getArrayCopy();
    }

    public function add($sql, $parameters){
        $statement = $this->adapter->createStatement($sql);
        $statement->prepare();
        return $statement->execute($parameters);
    }



    /**
     * fetch query
     *
     * @param  string | \Zend\Db\Sql\Select $select
     * @param  null|array $parameters
     * @param  null|string $type; e.g  ($type = array or object)
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     * @return \Zend\Db\ResultSet\ResultSetInterface  | Array
     */
    public function fetchAllWithOtherAdapter($adapter, $select = null, $parameters = null)
    {

            $statement = $adapter->createStatement($select);
            $statement->prepare();
            $result = $statement->execute($parameters);
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet;
                $resultSet->initialize($result);

                return $resultSet;

            }else
                throw new \Zend\Db\Adapter\Exception\InvalidArgumentException('Invalid execution, bad request');

    }


    public function doQuery($sql){
        $statement = $this->adapter->createStatement($sql);
        $statement->prepare();
        $statement->execute();
    }
	
	
	
	
	
}
