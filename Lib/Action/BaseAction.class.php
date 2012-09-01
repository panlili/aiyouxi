<?php

class BaseAction extends Action {

    public function _initialize() {
        if (!session("?truename"))
            $this->redirect("Search/index");
    }

    public function _empty() {
        $this->redirect($this->getActionName() . "/index");
    }

}

?>
