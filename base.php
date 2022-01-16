<?php
date_default_timezone_set('Asia/Taipei');
session_start();

class DB
{
    private $dsn = 'mysql:host=localhost;charset=utf8;dbname=web01';
    private $root = "root";
    private $pw = '';
    private $table;
    private $pdo;
    public function __construct($table)
    {
        $this->table = $table;
        $this->pdo = new PDO($this->dsn, $this->root, $this->pw);
    }

    private function jon($arg)
    {
        $sql = '';
        if (is_array($arg)) {
            foreach ($arg as $key => $val) {
                $tmp[] = "`$key`='$val'";
            }
            $sql .= "where " . join(' and ', $tmp);
        } else {
            $sql .= "where `id`='" . $arg . "'";
        }
        return $sql;
    }

    private function chk($arg)
    {
        $sql = '';
        if (!empty($arg[0]) && is_array($arg[0])) {
            $sql .= $this->jon($arg[0]);
        }
        if (!empty($arg[1])) {
            $sql .= " " . $arg[1];
        }
        return $sql;
    }

    public function all(...$arg)
    {
        $sql = "select * from $this->table ";
        $sql .= $this->chk($arg);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    }
    public function math($math, $col, ...$arg)
    {
        $sql = "select $math($col) from $this->table ";
        $sql .= $this->chk($arg);
        return $this->pdo->query($sql)->fetchColumn();
    }
    public function find($arg)
    {
        $sql = "select * from $this->table ";
        $sql .= $this->jon($arg);
        
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }
    public function del($arg)
    {
        $sql = "delete from $this->table ";
        $sql .= $this->jon($arg);
        return $this->pdo->exec($sql);
    }
    public function save($arg)
    {
        $sql = "";
        if (!empty($arg['id'])) {
            foreach ($arg as $key => $val) {
                if ($key != 'id') {
                    $tmp[] = "`$key`='$val'";
                }

            }
            $sql .= sprintf("update %s set %s where `id`='%s'", $this->table, join(',', $tmp), $arg['id']);
        } else {
            $sql .= sprintf("insert into %s (`%s`) values ('%s')", $this->table, join('`,`', array_keys($arg)), join("','", $arg));
        }
        return $this->pdo->exec($sql);
    }
    public function q($arg)
    {
        return $this->pdo->query($arg)->fetchAll(PDO::FETCH_ASSOC);

    }
}

function dd($arg)
{
    echo '<pre>';
    print_r( $arg);
    echo '</pre>';
}
function to($arg)
{
    header("location:" . $arg);
}

$Title = new DB('title');
$Ad = new DB('ad');
$Mvim = new DB('mvim');
$Image = new DB('image');
$News = new DB('news');
$Bottom = new DB('bottom');
$Total = new DB('total');
$Menu = new DB('menu');
$Admin = new DB('admin');

$title = $Title->find(['sh' => 1]);
$total = $Total->find(1);
$bottom = $Bottom->find(1);

if (empty($_SESSION['visited'])) {
    $total['total']++;
    $Total->save($total);
    $_SESSION['visited'] = $total['total'];
    $total = $Total->find(1);
}
