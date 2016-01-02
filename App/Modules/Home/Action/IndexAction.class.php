<?php
class IndexAction extends Action {
    public function index() {
    	redirect('index.php?m=Artist&a=index', 0, '页面跳转中...');
    }
}