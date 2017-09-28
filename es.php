<?php
include 'ESQueryBuilder.php';

$action = $_GET['action'];
$action();

function getHosts()
{
    $hosts = include 'config.php';
    exit(json_encode($hosts));
}

function getMapping()
{
    $host = $_POST['host'];
    $ESQueryBuilder = new ESQueryBuilder();
    $ESQueryBuilder->init($host);
    $mapping = $ESQueryBuilder->getMapping();
    exit(json_encode($mapping));
}

function getQuery()
{
    $host = $_POST['host'];
    $ESQueryBuilder = new ESQueryBuilder();
    $ESQueryBuilder->init($host);
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $ESQueryBuilder->limit($page, 15);
    
    if (isset($_POST['select']) && $_POST['select']) {
        $ESQueryBuilder->select($_POST['select']);
    }
    
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    if ($search) {
        $searchArr = json_decode($search, true);
        foreach($searchArr as $line) {
            switch($line[1]) {
                case 'equal':
                    $ESQueryBuilder->andWhere($line[0], $line[2]);
                    break;
                case 'notequal':
                    $ESQueryBuilder->notWhere($line[0], $line[2]);
                    break;
                case 'like':
                    $ESQueryBuilder->likeWhere($line[0], $line[2]);
                    break;
                case 'in':
                    $ESQueryBuilder->inWhere($line[0], explode(',', $line[2]));
                    break;
                case 'notin':
                    $ESQueryBuilder->notInWhere($line[0], explode(',', $line[2]));
                    break;
                case 'between':
                    $values = explode(',', $line[2]);
                    $ESQueryBuilder->betweenWhere($line[0], $values[0], $values[1]);
                    break;
            }
        }
    }

    $sort = isset($_POST['sort']) ? $_POST['sort'] : '';
    if ($sort) {
        $sortArr = explode(',', $sort);
        $ESQueryBuilder->orderBy($sortArr[0], $sortArr[1]);
    }

    $lists = $ESQueryBuilder->getLists();
    exit(json_encode(['result' => $lists, 'rawRequest' => $ESQueryBuilder->getRawRequest(), 'rawResult' => $ESQueryBuilder->getRawResult()]));
}

function getDsl()
{
    $host = $_POST['host'];
    $dsl = $_POST['dsl'];
    $ESQueryBuilder = new ESQueryBuilder();
    $ESQueryBuilder->init($host);
    $result = $ESQueryBuilder->getDsl($dsl);

    exit($result);
}

function update()
{
    $host = $_POST['host'];
    $id = $_POST['id'];
    $field = $_POST['field'];
    $value = $_POST['value'];
    $ESQueryBuilder = new ESQueryBuilder();
    $ESQueryBuilder->init($host);
    $result = $ESQueryBuilder->update($id, [$field => $value]);

    exit($result);
}
