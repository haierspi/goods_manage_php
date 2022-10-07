<?php

namespace ff\nosql;

use ff\base\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class RedisCollection extends Component
{

    private $dataKey;
    private $indexKey;
    private $where;
    private $start;
    private $end;
    private $results = [];
    private $total = 0;

    public function __construct($indexKey, $dataKey)
    {
        $this->dataKey = $indexKey;
        $this->indexKey = $dataKey;
    }

    public function where($where)
    {
        if(isset($where)){
            $this->where = $where;
        }
        return $this;
    }

    public function offset($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
        return $this;
    }

    public function get()
    {
        if ($this->where) {

            $dataJosn = Redis::hGet($this->dataKey, $this->where);
            if ($dataJosn) {
                $this->results[] = json_decode($dataJosn);
                $this->total = 1;
            }
        } else {
            $this->total = (int) current(Redis::ZREVRANGE($this->indexKey, 0, 0, 'WITHSCORES'));

            $indexResults = Redis::ZRANGE($this->indexKey, $this->start, $this->end, 'WITHSCORES');
            if ($indexResults) {
                foreach ($indexResults as $oneDataKey => $score) {
                    $dataJosn = Redis::hGet($this->dataKey, $oneDataKey);
                    $this->results[] = json_decode($dataJosn);
                }
            }

        }
        return $this->results;
    }

    public function paginate($perPage = null, $pageName = 'page', $currentPage = null)
    {

        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);

        $start = $perPage * ($currentPage - 1);
        $end = $perPage * $currentPage - 1;

        $this->offset($start, $end)->get();

        // $results = [];

        return new LengthAwarePaginator(
            $this->results,
            $this->total,
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );

    }

}
