<?php
/**
 * 字符(串)处理类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.string.inc.php 931 2013-12-10 08:00:02Z liqt $
 * @creator liqt @ 2013-01-15 11:17:25 by caster0.0.2
 */
namespace scap\module\g_tool;

/**
 * 字符(串)处理类
 */
class string
{
    /**
     * 截取指定长度的字符串
     * - 自动去除前后空格
     * - 自动去除重复空格
     * 
     * @param string $content 原始字符串内容
     * @param int $start 起始位置，第一位为0，默认为0
     * @param int $length 要截取的长度,默认为NULL，保留所有
     * @param string $postfix 后缀字符，默认为 ...
     * 
     * @return string 截取的内容
     */
    public static function get_substr($content, $start = 0, $length = NULL, $postfix = '...')
    {
        $result = '';
        $data_in = array();
        
    	$data_in['safe_content'] = trim($content);// 去除前后空格
    	$data_in['safe_content'] = str_replace("  ", " ", $data_in['safe_content']);// 去除重复空格
    	
    	if (!is_null($length))
    	{
        	// 获取内容字数
        	$data_in['count'] = mb_strlen($data_in['safe_content'], 'UTF8');
            
            $result = mb_substr($data_in['safe_content'], $start, $length, 'UTF8');
            
            if ($data_in['count'] > $length)
            {
                $result .= $postfix;
            }
    	}
    	else 
    	{
    	    $result = $data_in['safe_content'];
    	}
        
        return $result;
    }
    
    /**
     * 截取指定长度的安全字符串
     * -去除html及脚本标签，去除首位空格等特殊字符
     * 
     * @param string $content 原始字符串内容
     * @param int $start 起始位置，第一位为0，默认为0
     * @param int $length 要截取的长度,默认为NULL，保留所有
     * @param string $postfix 后缀字符，默认为 ...
     * 
     * @return string 截取的内容
     */
    public static function get_clean_substr($content, $start = 0, $length = NULL, $postfix = '...')
    {
        $result = '';
        $data_in = array();
        
        // $document 应包含一个 HTML 文档。本例将去掉 HTML 标记，javascript 代码 和空白字符。还会将一些通用的HTML 实体转换成相应的文本。	
    	$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
    	                 "'<[\/\!]*?[^<>]*?>'si",           // 去掉 HTML 标记
    	                 "'([\r\n])[\s]+'",                 // 去掉空白字符
    	                 "/([\s]{2,})/",                    // 去除多余的空格和换行符，只保留一个
    	                 "'&(quot|#34);'i",                 // 替换 HTML 实体
    	                 "'&(amp|#38);'i",
    	                 "'&(lt|#60);'i",
    	                 "'&(gt|#62);'i",
    	                 "'&(nbsp|#160);'i",
    	                 "'&(iexcl|#161);'i",
    	                 "'&(cent|#162);'i",
    	                 "'&(pound|#163);'i",
    	                 "'&(copy|#169);'i",
    	                 "'&#(\d+);'e");                    // 作为 PHP 代码运行
    	
    	$replace = array ("",
    	                  "",
    	                  "\\1",
    	                  "\\1",
    	                  "\"",
    	                  "&",
    	                  "<",
    	                  ">",
    	                  " ",
    	                  chr(161),
    	                  chr(162),
    	                  chr(163),
    	                  chr(169),
    	                  "chr(\\1)");
    	
    	$data_in['safe_content'] = preg_replace($search, $replace, $content);
    	// 去除前后空格
    	$data_in['safe_content'] = trim($data_in['safe_content']);
    	// 去除重复空格
    	$data_in['safe_content'] = str_replace("  ", " ", $data_in['safe_content']);
    	
    	if (!is_null($length))
    	{
        	// 获取内容字数
        	$data_in['count'] = mb_strlen($data_in['safe_content'], 'UTF8');
            
            $result = mb_substr($data_in['safe_content'], $start, $length, 'UTF8');
            
            if ($data_in['count'] > $length)
            {
                $result .= $postfix;
            }
    	}
    	else 
    	{
    	    $result = $data_in['safe_content'];
    	}
        
        return $result;
    }
    
    /**
     * 产生随机字符串
     * 
     * @param int $len 字符串长度
     * @param string $type 字符串类型|备选字符集，目前支持：all/char/number;如果不是all/char/number则讲type作为备选字符集合传入
     * 
     * @return string 随机字符串
     */
    public static function gen_rand_str($len, $type = 'number')
    {
    	switch($type)
    	{
    		case 'all':
    		 	$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    		 	break;
    		case 'char':
    		 	$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    		 	break;
    		case 'number':
    		 	$chars='0123456789';
    		 	break;
    		default:
    		 	$chars=$type;
    		 	break;
    	 }
    	 
    	 if (empty($chars))
    	 {
    	     $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    	 }
    	 
    	 $rand = '';
    	 mt_srand((double)microtime()*1000000*getmypid());
    	 while(strlen($rand) < $len)
    	 {
    	 	$rand .= substr($chars,(mt_rand() % strlen($chars)),1);
    	 }
    	 
    	 return $rand;
    }
    
    /**
     * 将全角符号转换为半角符号
     * 
     * @param string $str 需转化的字符串
     * 
     * @return string 转化后的字符串
     */
    public static function make_semiangle($str)
    {   
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',   
                     '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',   
                     'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',   
                     'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',   
                     'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',   
                     'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',   
                     'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',   
                     'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',   
                     'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',   
                     'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',   
                     'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',   
                     'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',   
                     'ｙ' => 'y', 'ｚ' => 'z',   
                     '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',   
                     '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',   
                     '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',   
                     '》' => '>',   
                     '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',   
                     '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',   
                     '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',   
                     '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',   
                     '　' => ' ','＄'=>'$','＠'=>'@','＃'=>'#','＾'=>'^','＆'=>'&','＊'=>'*',
                     '『' => '[', '』' => ']'
        			
        );
        return strtr($str, $arr);
    }
    
    /**
     * 去除指定内容中的标点符号
     * - 支持中文全角符号
     * 
     * @param string $content 原始字符串内容
     * 
     * @return string 处理后的字符内容
     */
    public static function clean_punctuation($content)
    {
        $result = '';
        
        $result = self::make_semiangle($content);// 将中文标点转为英文标点
        $result = preg_replace("/[[:punct:]]/", "", $result);// 去除标点
        
        return $result;
    }
    
    /**
     * 根据字节数量获得可读性的字符串
     * 
     * @param int $input 字节数量
     * 
     * @return string xxGB/xxMB/xxKB等
     */
    public static function get_readable_from_bytes($input)
    {
        $result = '';
        $input = intval($input);
        
        if($input >= 1073741824)
        {
            $result = round($input / 1073741824 * 100) / 100 . 'GB';
        }
        elseif($input >= 1048576)
        {
            $result = round($input / 1048576 * 100) / 100 . 'MB';
        }
        elseif($input >= 1024)
        {
            $result = round($input / 1024 * 100) / 100 . 'KB';
        }
        else
        {
            $result = $input . 'Bytes';
        }
        
        return $result;
    }
    
    /**
	 * 对需要进入sql查询的参数进行安全处理
	 * - 防止sql inject
	 * - http://www.bitrepository.com/sanitize-data-to-prevent-sql-injection-attacks.html
	 * 
	 * @param string $input 待处理的字符串
	 * 
	 * @return string 处理后的字符串
     */
    public static function safe_input_for_sql($input)
    {
        // remove whitespaces (not a must though)
        $input = trim($input); 
        
        // apply stripslashes if magic_quotes_gpc is enabled
        if(get_magic_quotes_gpc()) 
        {
            $input = stripslashes($input); 
        }
        
        // a mySQL connection is required before using this function
        $input = mysql_real_escape_string($input);
        
        return $input;
    }
    
    /**
     * 将数字转换为字母
     * - Takes a number and converts it to a-z,aa-zz,aaa-zzz, etc with uppercase option
     * - .X,Y,Z,AA,AB,AC,
     * - http://studiokoi.com/blog/article/converting_numbers_to_letters_quickly_in_php
     * 
     * @param int $number 待转的数字
     * @param bool $flag_uppercase 是否输出大写字母，默认为true
     * 
     * @return string
     */
    public static function number_to_letter($number, $flag_uppercase = true)
    {
        // Map numbers 1 - 26 on to character codes 97 - 122.
        // For higher numbers build recursively from the right.
        $number -= 1;
        $letter = chr($number % 26 + 97);
        if ($number >= 26) {
            $letter = self::number_to_letter(floor($number/26),$uppercase).$letter;
        }
        
        return ($flag_uppercase ? strtoupper($letter) : $letter);
    }
    
    /**
     * 将数字转化为中文字
     * - 如1 > 一，13 > 一十三
     * 
     * @param int $number 整数
     * 
     * @return string
     */
    public static function number_to_chinese($number)
    {
        $char = array("零","一","二","三","四","五","六","七","八","九");
        $dw = array("","十","百","千","万","亿","兆");
        $retval = "";
        $proZero = false;
        for($i = 0;$i < strlen($number);$i++)
        {
            if($i > 0)    $temp = (int)(($number % pow (10,$i+1)) / pow (10,$i));
            else $temp = (int)($number % pow (10,1));
            
            if($proZero == true && $temp == 0) continue;
            
            if($temp == 0) $proZero = true;
            else $proZero = false;
            
            if($proZero)
            {
                if($retval == "") continue;
                $retval = $char[$temp].$retval;
            }
            else $retval = $char[$temp].$dw[$i].$retval;
        }
        if($retval == "一十") $retval = "十";
        return $retval;
    }
}
?>