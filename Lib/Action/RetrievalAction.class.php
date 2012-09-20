<?php

class RetrievalAction extends BaseAction {

    public static $method_array = array(
        "index" => "检索首页",
        "query" => "检索结果"
    );

    public function index() {
        $this->display();
    }

    public function query() {
        if (IS_POST) {
            $m_fullgood = M("Fullgood");
            $queryArray = $this->_param();
            //这两个key的值是模块名和方法名
            unset($queryArray[0]);
            unset($queryArray[1]);
            if (!empty($queryArray["donatetimestart"]))
                $queryArray["donatetimestart"] = strtotime($queryArray["donatetimestart"]." 00:00:00");
            if (!empty($queryArray["donatetimeend"]))
                $queryArray["donatetimeend"] = strtotime($queryArray["donatetimeend"]." 23:59:59");

            //检索条件的构建
            $map = array();
            $start = strtotime("2004-01-01");
            $end = strtotime("2030-01-01");
            foreach ($queryArray as $key => $value) {
                if ($key != "donatetimestart" && $key != "donatetimeend") {
                    if (!empty($value)) {
                        $map[$key] = array("like", "%" . trim($value) . "%");
                    }
                }

                if ($key == "donatetimestart" && !empty($value))
                    $start = $value;
                if ($key == "donatetimeend" && !empty($value))
                    $end = $value;
            }
            $map["donatetime"] = array('between', array($start, $end));
            session("querytext", $map);
            //检索结果的处理
            $result = $m_fullgood->where($map)->select();
            if (count($result) == 0) {
                $this->assign("noresult");
            }
            $this->assign("result", $result);
            $this->display();
        } else {
            $this->redirect("Retrieval/index");
        }
    }

    public function toexcel() {
        if (!isset($_SESSION["querytext"]))
            $this->redirect("Retrieval/index");

        $query = session("querytext");
        $list = M("Fullgood")->where($query)->select();
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=goods.xls");
        $this->assign("list", $list);
        echo $this->fetch("_excel");
    }

}

?>
