<?php

class EmptyAction extends Action {

    public function _empty() {
        $this->redirect("Search/index");
    }

}

?>
