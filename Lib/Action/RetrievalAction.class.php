<?php

class RetrievalAction extends BaseAction {

    public static $method_array = array(
        "index" => "捐赠信息检索",
        "query" => "检索结果"
    );

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
            $result = $m_fullgood->where($map)->order("donatetime desc")->limit($p->firstRow . "," . $p->listRows)->select();
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
            $queryArray["donatetimestart"] = $queryArray["donatetimestart"];
        if (!empty($queryArray["donatetimeend"]))
            $queryArray["donatetimeend"] = $queryArray["donatetimeend"];

        //检索条件的构建
        $map = array();
        $donatetimestart = "2004-01-01";
        $donatetimeend = "2030-01-01";
        foreach ($queryArray as $key => $value) {
            if ($key != "donatetimestart" && $key != "donatetimeend") {
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
        //如果不是"所有站点用户",需要匹配location的值，显示单个站点数据。否则不需要匹配location的值从而显示所有站点数据
        if (FALSE === is_all_user()) {
            $map["location"] = session("locationid");
        } else {
            //这里是特别处理当所以站点的用户进来时，检索页面提供了站点的下拉选项
            //如果下拉选择所有站点，则就是检索所有数据，故不需要对location匹配
            //如果选择了非所有站点，就需要匹配location，此处的匹配条件不需要在这里构造，而是自动在上面的foreach循环里构造了
            if ("所有站点" === get_location_name_by_id($queryArray["location"])) {
                unset($map["location"]);
            }
        }
        return $map;
    }

    public function toexcel() {
        if (!isset($_SESSION["querytext"]))
            $this->redirect("Retrieval/index");

        load("extend");
        $query = session("querytext");
        $list = M("Fullgood")->where($query)->select();
        $filename = auto_charset_my("物资列表_导出时间_" . date("Y_m_d_H_i_m"), "utf-8", "gbk");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        $this->assign("list", $list);
        echo $this->fetch("_excel");
    }

}

?>
