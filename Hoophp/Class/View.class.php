<?php
class View{
    /**
     * 使用布局文件展示视图
     * @global type $QueryAction 请求控制器
     * @param type $view 模板文件
     * @param type $params 展示数据
     */
    public function render($view,$params=null){
        global $QueryAction;
        $tpl_path = APP . "Views/{$QueryAction}/{$view}.php";
        $content = $this->renderPhpFile($tpl_path, $params);
        $layout_path = APP . "Views/layout/main.php";
        echo $this->renderPhpFile($layout_path, ['content' => $content]);
    }
    
    /**
     * 展示视图
     * @global type $QueryAction 请求控制器
     * @param type $view 模板文件
     * @param type $params 展示数据
     */
    public function renderFile($view,$params){
        global $QueryAction;
        $tpl_path = APP . "Views/{$QueryAction}/{$view}.php";
        echo $this->renderPhpFile($tpl_path, $params);
    }
    
    /**
     * 引入视图文件
     * @param type $_file_
     * @param type $_params_
     * @return type
     */
    private function renderPhpFile($_file_, $_params_ = []){    
        ob_start();    
        ob_implicit_flush(false);    
        extract($_params_, EXTR_OVERWRITE);    
        require($_file_);    
        return ob_get_clean();    
    }
}