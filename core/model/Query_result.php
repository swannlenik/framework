<?php
/**
 * Created by PhpStorm.
 * User: Swann
 * Date: 08/12/2018
 * Time: 19:22
 */

namespace Core\Model;

class Query_result
{
    /**
     * @var string
     */
    private $query;
    /**
     * @var array
     */
    private $parametres;
    /**
     * @var array
     */
    private $result;
    /**
     * @var int
     */
    private $resultCount;

    /**
     * @var int
     */
    public $status;
    /**
     * @var string
     */
    private $message;

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResultCount()
    {
        return $this->resultCount;
    }

    /**
     * @param mixed $resultCount
     */
    public function setResultCount(float $resultCount): void
    {
        $this->resultCount = $resultCount;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getParametres()
    {
        return $this->parametres;
    }

    /**
     * @param array $parametres
     */
    public function setParametres(array $parametres): void
    {
        $this->parametres = $parametres;
    }

    /**
     * Query_result constructor.
     */
    public function __construct() {
        $this->query = "";
        $this->resultCount = 0;
    }

    /**
     * @return array
     */
    public function toObject() {
        $i = 0;
        $result = []; //new \stdClass();
        foreach($this->result as $item) {
            $result[$i] = new \stdClass();
            foreach($item as $key => $val) {
                $result[$i]->$key = $val;
            }
            $i++;
        }
        return $result;
    }
}