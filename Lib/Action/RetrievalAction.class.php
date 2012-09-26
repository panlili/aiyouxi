<?php

class RetrievalAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠信息检索",
        "query" => "检索结果"
    );

    public function index() {
        $this->display();
    }

    public function query() {
        $m_fullgood = M("Fullgood");
        if (array_key_exists("step", $this->_param())) {
            $map = $this->generateQuerySql($this->_param());
            session("querytext", $map);
        } else {
            $map = session("querytext");
        }

        //检索结果的处理
        $count = $m_fullgood->where($map)->count();
        if ($count == 0) {
            $this->assign("noresult");
            $this->display();
        } else {
            import("ORG.Util.Page");
            $p = new Page($count, self::RECORDS_ONE_PAGE);
            $page = $p->show();
            $result = $m_fullgood->where($map)->limit($p->firstRow . "," . $p->listRows)->select();
            $this->assign("page", $page);
            $this->assign("result", $result);
            $this->display();
        }
    }

    private function generateQuerySql($params) {
        //post表单传过来的数组
        $queryArray = $params;
        //这两个key的值是模块名和方法名
        unset($queryArray[0]);
        unset($queryArray[1]);
        //捐赠日期在数据库中是time形式
        if (!empty($queryArray["donatetimestart"]))
            $queryArray["donatetimestart"] = strtotime($queryArray["donatetimestart"] . " 00:00:00");
        if (!empty($queryArray["donatetimeend"]))
            $queryArray["donatetimeend"] = strtotime($queryArray["donatetimeend"] . " 23:59:59");

        //检索条件的构建
        $map = array();
        $donatetimestart = strtotime("2004-01-01");
        $donatetimeend = strtotime("2030-01-01");
        foreach ($queryArray as $key => $value) {
            if ($key != "donatetimestart" && $key != "donatetimeend" && $key != "distributedaystart" && $key != "distributedayend") {
                if (!empty($value)) {
                    $map[$key] = array("like", "%" . trim($value) . "%");
                }
            }

            if ($key == "donatetimestart" && !empty($value))
                $donatetimestart = $value;
            if ($key == "donatetimeend" && !empty($value))
                $donatetimeend = $value;
        }

        $map["donatetime"] = array('between', array($donatetimestart, $donatetimeend));
        return $map;
    }

    public function toexcel() {
        if (!isset($_SESSION["querytext"]))
            $this->redirect("Retrieval/index");

        $query = session("querytext");
        $list = M("Fullgood")->where($query)->select();
        $filename = auto_charset("物资列表_导出时间_" . date("Y_m_d_H_i_m"), "utf-8", "gbk");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        $this->assign("list", $list);
        echo $this->fetch("_excel");
    }

}

?>
