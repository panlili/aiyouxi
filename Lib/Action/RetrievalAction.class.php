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
                $queryArray["donatetimestart"] = strtotime($queryArray["donatetimestart"]);
            if (!empty($queryArray["donatetimeend"]))
                $queryArray["donatetimeend"] = strtotime($queryArray["donatetimeend"]);

            //检索条件的构建
            $map = array();
            $start = strtotime("2004-01-01");
            $end = strtotime("2030-01-01");
            foreach ($queryArray as $key => $value) {
                if ($key != "donatetimestart" && $key != "donatetimeend") {
                    if (!empty($value)) {
                        $map[$key] = array("like", "%" . $value . "%");
                    }
                }

                if ($key == "donatetimestart" && !empty($value))
                    $start = $value;
                if ($key == "donatetimeend" && !empty($value))
                    $end = $value;
            }
            $map["donatetime"] = array('between', array($start, $end));

            //检索结果的处理
            $result = $m_fullgood->where($map)->select();
            $this->assign("result", $result);
            $this->display();
        }else {
            $this->redirect("Retrieval/index");
        }
    }

}

?>
