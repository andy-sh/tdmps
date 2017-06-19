/**
 * scap js常用基础函数库文件
 * 
 * @version $Id: common.js 723 2013-09-06 07:01:46Z liqt $
 * @creator liqt @ 2013-02-04 14:57:26 by caster0.0.2
 */

/**
 * 判断字符串是否存在内容
 * - checking if a string is empty, null or undefined
 * 
 * @param str 待判断字符串
 * 
 * @returns bool
 */
function is_empty(str)
{
    return (!str || 0 === str.length);
}

/**
 * 判断字符串是否存在非空白的内容
 * - checking if a string is blank, null or undefined
 * 
 * @param str 待判断字符串
 * 
 * @returns bool
 */
function is_blank(str)
{
	return (!str || /^\s*$/.test(str));
}

/**
 * 判断变量是否存被定义或设置
 * 
 * @param variable 待判变量
 * 
 * @returns bool
 */
function is_set(variable)
{
	return (typeof variable !== "undefined");
}

/**
 * 设置函数的参数值
 * - 在没有输入或者null的情况下，使用默认值
 * 
 * @param arg 待赋值参数
 * @param default_value 默认值
 * 
 * @returns 参数数值
 */
function set_arg_value(arg, default_value)
{
	return (typeof arg === "undefined" || arg == null) ? default_value : arg;
}

/**
 * 将指定的对象转换为json样式
 * - http://stackoverflow.com/questions/1625208/print-content-of-javascript-object
 * - https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/stringify
 * 
 * @param obj 指定的对象
 * 
 * @returns string
 */
function object_to_json(obj)
{
	return JSON.stringify(obj, null, 4);
}

/**
 * 在控制台输出内容
 * - 不会在不支持console.log的浏览器中报错
 * 
 * @param content 输出的内容
 * 
 * @returns void
 */
function log(content)
{
	window.console && console.log(content);
}