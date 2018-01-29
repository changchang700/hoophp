<?php
abstract class Action{
    protected $view;
    public function __construct() {
        $this->view=new View();
    }
    /**
     * 使用布局文件展示视图
     * @param string $view 模板文件
     * @param array $params 展示数据
     */
    public function render($view,$params){
        $this->view->render($view,$params);
    }
    /**
     * 不使用布局文件展示视图
     * @param string $view 模板文件
     * @param array $params 展示数据
     */
    public function renderFile($view,$params){
        $this->view->renderFile($view,$params);
    }
}
