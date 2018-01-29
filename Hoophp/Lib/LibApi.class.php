<?php
/**
 * @author Joy
 */
class LibApi {
    //put your code here
    public function generateTest($apiFullPath) {
        $handle = fopen($apiFullPath, 'r');
        $names = array();
        $tmpDoc = "";
        $state = 0; //0:查找注释开始,1:查找注释结尾,2:操作注释定义对象
        while (!feof($handle)) {
            $line = fgets($handle, 1024);
            $line = trim($line);

            if (!$line) {
                continue;
            }
            switch ($state) {
                case 0:
                    if (strpos($line, "/**") !== false) {
                        $nodes = self::_formatNodesStart($line);
                        $nodesName = "";
                        $nodesTitle = "";
                        $nodesType = "";
                        $state = 1;
                    }
                    break;
                case 1:
                    if (strpos($line, "*/") !== false) {
                        $nodes .= self::_formatNodeEnd($line);
                        $state = 2;
                    } else {
                        if (!$nodesTitle) {
                            $nodesTitle = self::_getNodeTitle($line);
                        }
                        $nodes .= self::_formatNodesContent($line);
                    }
                    break;
                case 2:
                    if (strpos($line, "class ") !== false) {
                        $nodes .= self::_formatClassDef($line);
                        $nodesName = self::_getNodesName($line);
                        $nodesType = "class";
                        $state = 0;
                    } else if (strpos($line, "function ") !== false) {
                        $nodes .= self::_formatFunDef($line);
                        $nodesName = self::_getNodesName($line);
                        $nodesType = "function";
                        $state = 0;
                    } else if (preg_match('/^public[\s]+(\$[\w]+);$/i', $line)) {
                        $nodes .= self::_formatPropertyDef($line);
                        $nodesName = self::_getPropertyNodesName($line);
                        $nodesType = "property";
                        $state = 0;
                    }
                    break;
            }

            if ($nodes && $nodesName) {
                $tmpDoc .= "<a name='$nodesName'></a>" . $nodes;
                $names[] = array("name" => $nodesName, "title" => $nodesTitle, "type" => $nodesType);
                $nodes = null;
                $nodesName = null;
            }
        }

        fclose($handle);
        return self::_formatDocHtml($names);
    }

    static function _formatDocHtml(&$names) {
        $tmpTest = self::_formatTestHtml($names);
        
        $html = "<html><head><meta http-equiv = 'Content-Type' content = 'text/html; charset=utf-8' />\n";
        $html .= "<style>\n";
        $html .= "*{font-family:'微软雅黑';
                font-size:14px;
                }\n";
        $html .= "body {text-align:center;
                }\n";
        $html .= ".wrap {width:980px;
                text-align:left;
                margin-left:auto;
                margin-right:auto;
                margin-top:30px;
                }\n";
        $html .= ".nodes {border:1px solid #999;background:#cfcfcf;padding:10px;}\n";
        $html .= ".classdef,.classfun,.classprop{border:1px dotted #785;background:#f5f5f5; padding:10px;margin-bottom:20px;}\n";
        $html .= ".classdef{background-color:#ffefd5}";
        $html .= "strong{color:#BBBB00;}\n";
        $html .= ".index .name{font-size:16px; line-height:2em;} \n";
        $html .= ".index .tag{margin-left:1em;font-style:italic;color:#999;} \n";
        $html .= ".index .title{margin-left:0.5em;font-style:italic;color:#999;} \n";
        $html .= ".index .class{font-weight:bold;color:#009fcc} \n";
        $html .= ".index .function{margin-left:1em;color:#00aaaa;} \n";
        $html .= ".index .property{margin-left:1em;color:#00aaaa;} \n";
        $html .= ".testbox { display: block; list-style-type: none;}";
        $html .= "</style></head>\n";
        $html .= "<body><div class='wrap'>{$tmpTest}<div style='height:100px;'></div></div></body></html>";

        return $html;
    }

    static function _formatTestHtml(&$names) {
        $tmpTest = "\r\n<div style='background: #ffefd5; border: 1px solid #999; padding:10px;'>接口测试(<span style='color:red'>*注：字符串使用双引号包括</span>)</div>\r";
        $tmpTest .= "<table width='100%' style='background:#f5f5f5; border: 1px solid #999; padding:10px;'><tbody>";

        $js = "\r\n<script>\r\n
                function postData(){ var p='{'";

        $act = '';
        $controller = '';
        $isAct = true;
        $array_prop = array();
        foreach ($names as $obj) {
            $name = $obj["name"];
            if ($obj["type"] == "property") {
                $name = str_replace('$', '', $name);
                $tmpTest .= "<tr><td width='50px'>{$name}：</td><td><input type='text' id = 'param_{$name}' value='' /></td></tr>";
                //$tmpName .= "<li><a class='name {$type}' href='#{$name}'>{$name}</a><span class='tag'>{$tag}</span><span class='title'>{$title}</span></li>";
                $array_prop[] = $name;
            }
            if (strpos($name, 'Service')) {
                $act = $name;
                $isAct = true;
            }
            if (strpos($name, 'Controller')) {
                $controller = $name;
                $isAct = false;
            }
        }
        $tmpTest.="<tr><td ><input type='button' value='提交' onclick='javascript:postData();'></td></tr>";
        $tmpTest.="</tbody></table><form id='form1' method='post' action='{$action}' ><input name='param_p' id='param_p' type='hidden' value='' >\r
                <input name='param_act' type='hidden' value='{$act}'><input name='param_ctrl' type='hidden' value='{$controller}'></form>";

        for ($index = 0; $index < count($array_prop); $index++) {
            if ($index == count($array_prop) - 1) {
                $js.="+'\"{$array_prop[$index]}\":\"'+ document.getElementById('param_{$array_prop[$index]}').value+'\"'";
            } else {
                $js.="+'\"{$array_prop[$index]}\":\"'+ document.getElementById('param_{$array_prop[$index]}').value+'\",'";
            }
        }

        //$js = substr($js, 0, strlen($js) - 4);
        $js.="+'}';\r\n document.getElementById('param_p').value = p;  var form1 = document.getElementById('form1'); form1.submit();}\r\n</script>\r\n";
        return $tmpTest . $js;
    }

    static function _getNodesName($line) {
        $regex = '/(?:class|function)[\s]+([\w]+)/';
        $matches = array();
        if (preg_match($regex, $line, $matches)) {
            //print_r($matches);
            return $matches[1];
        }
        return false;
    }

    static function _getPropertyNodesName($line) {
        $match = array();
        if (preg_match('/^public[\s]+(\$[\w]+);$/i', $line, $match)) {
            return $match[1];
        }
        return false;
    }

    static function _getNodeTitle($line) {
        $regex = '/\*[\s]+([^@].*)/';
        $matches = array();
        if (preg_match($regex, $line, $matches)) {
            //标题要求长度 > 3
            return strlen($matches[1]) > 3 ? $matches[1] : false;
        }
        return false;
    }

    static function _formatNodesStart($line) {
        return "<div style='height:5px;'></div><div class='nodes'><div>{$line}</div>";
    }

    static function _formatNodesContent($line) {
        return "<div style='text-indent:.5em;'>{$line}</div>";
    }

    static function _formatNodeEnd($line) {
        return "<div style='text-indent:.5em;'>{$line}</div></div>";
    }

    static function _formatClassDef($line) {
        $arr = explode('{', $line);
        $tmp = $arr[0];
        $nodesName = self::_getNodesName($line);
        $tmp = str_replace($nodesName, "<strong>$nodesName</strong>", $tmp);
        return "<div class='classdef'>{$tmp}</div>";
    }

    static function _formatFunDef($line) {
        $arr = explode('{', $line);
        $tmp = $arr[0];
        $nodesName = self::_getNodesName($line);
        $tmp = str_replace($nodesName, "<strong>$nodesName</strong>", $tmp);
        return "<div class='classfun'>{$tmp}</div>";
    }

    static function _formatPropertyDef($line) {
        $match = array();
        if (preg_match('/^public[\s]+(\$[\w]+);$/i', $line, $match)) {
            $nodesName = $match[1];
            $line = str_replace($nodesName, "<strong>$nodesName</strong>", $line);
        }
        return "<div class='classprop'>{$line}</div>";
    }

    /** 将数组元素进行urlencode
     * @param String $val
     */
    static function jsonFormatProtect(&$val) {
        if ($val !== true && $val !== false && $val !== null) {
            $val = urlencode($val);
        }
    }

    static function _jsonFormat($data, $indent = null) {
        
        // 对数组中每个元素递归进行urlencode操作，保护中文字符
        array_walk_recursive($data, 'self::jsonFormatProtect');

        // json encode
        $data = json_encode($data);

        // 将urlencode的内容进行urldecode
        $data = urldecode($data);

        // 缩进处理
        $ret = '';
        $pos = 0;
        $length = strlen($data);
        $indent = isset($indent) ? $indent : '    ';
        $newline = "\n";
        $prevchar = '';
        $outofquotes = true;
        for ($i = 0; $i <= $length; $i++) {
            $char = substr($data, $i, 1);
            if ($char == '"' && $prevchar != '\\') {
                $outofquotes = !$outofquotes;
            } elseif (($char == '}' || $char == ']') && $outofquotes) {
                $ret .= $newline;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }
            $ret .= $char;
            if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
                $ret .= $newline;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }
            $prevchar = $char;
        }
        return $ret;
    }
}