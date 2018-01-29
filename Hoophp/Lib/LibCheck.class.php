<?php
class LibCheck {
    /**
     * 检验是否是邮箱地址
     * @param type $str 邮箱地址
     * @return boolean
     */
	public function isEmail($str) {
		$p = "/^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$/i";
		if (preg_match($p, $str)) {
			return true;
		}
		return false;
	}
    /**
     * 检测是否是URL
     * @param type $str URL
     * @return boolean
     */
	public function isUrl($str) {
		$p = "/^(http|https):\/\/[\w-]+(.[\w-]+)*$/i";
		if (preg_match($p, $str)) {
			return true;
		}
		return false;
	}
    /**
     * 检测是否是电话号码
     * @param type $str 电话号码
     * @return boolean
     */
	public function isTel($str) {
		$p = "/^[\d-]{3,13}$/";
		if (preg_match($p, $str)) {
			return true;
		}
		return false;
	}
    /**
     * 检测是否是手机号码
     * @param type $str 手机号码
     * @return boolean
     */
	public function isPhone($str) {
		$p = "/^[\d]{11}$/";
		if (preg_match($p, $str)) {
			return true;
		}
		return false;
	}
    /**
     * 检验是6位的邮政编码
     * @param type $str 编码
     * @return boolean
     */
	public function isPost($str) {
		$p = "/^[\d]{6}$/";
		if (preg_match($p, $str)) {
			return true;
		}
		return false;
	}
    /**
     * 检验是"年-月-日 时:分:秒"的日期
     * @param type $str 时间
     * @return type
     */
	public function isDate($str) {
		return $str == Date("Y-m-d H:i:s", strtotime($str));
	}
}
