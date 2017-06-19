<?php
/**
 * cookie操作封装类实现文件
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.cookie.inc.php 715 2013-08-22 08:42:55Z liqt $
 * @creator LiQintao @ 2012-8-4 上午10:49:54
 */
namespace scap\module\g_tool;

/**
 * cookie操作封装类
 *
 */
class cookie
{
	/**
	 * @var string 特定的加盐字符串
	 */
	public static $salt = '8F495105DA0368354DBD4D4E74363DBE';
    
	/**
	 * @var integer  默认过期的总秒数
	 */
	public static $expiration = 0;

	/**
	 * @var string  Restrict the path that the cookie is available to
	 */
	public static $path = '/';

	/**
	 * @var string  Restrict the domain that the cookie is available to
	 */
	public static $domain = NULL;

	/**
	 * @var boolean  Only transmit cookies over secure connections
	 */
	public static $secure = FALSE;

	/**
	 * @var boolean  Only transmit cookies over HTTP, disabling Javascript access
	 */
	public static $httponly = FALSE;
    
	/**
	 * 获取指定的变量值
	 * 
	 * Gets the value of a signed cookie. Cookies without signatures will not
	 * be returned. If the cookie signature is present, but invalid, the cookie
	 * will be deleted.
	 *
	 *     // Get the "theme" cookie, or use "blue" if the cookie does not exist
	 *     $theme = cookie::get('theme', 'blue');
	 * 
	 * TODO 还不支持数组形式获取，如 scap[name]
	 *
	 * @param   string  变量键值名称
	 * @param   mixed   如果值不存在的默认返回值
	 * @param   bool     是否使用salt，默认为false
	 * 
	 * @return  string  变量值
	 */
	public static function get($key, $default = NULL, $use_salt = false)
	{
		if ( !isset($_COOKIE[$key]))
		{
			// The cookie does not exist
			return $default;
		}

		// Get the cookie value
		$cookie = $_COOKIE[$key];
        
		if (!$use_salt)// 不加盐的获取
		{
		    return isset($cookie) ? $cookie : $default;
		}
		
		// Find the position of the split between salt and contents
		$split = strlen(self::salt($key, NULL));

		if (isset($cookie[$split]) AND $cookie[$split] === '~')
		{
			// Separate the salt and the value
			list ($hash, $value) = explode('~', $cookie, 2);

			if (self::salt($key, $value) === $hash)
			{
				// Cookie signature is valid
				return $value;
			}

			// The cookie signature is invalid, delete it
			self::delete($key);
		}

		return $default;
	}
    
	/**
	 * 设置变量值
	 * 
	 * Sets a signed cookie. Note that all cookie values must be strings and no
	 * automatic serialization will be performed!
	 *
	 *     // Set the "theme" cookie
	 *     cookie::set('theme', 'red');
	 *
	 * @param   string   name of cookie
	 * @param   string   value of cookie
	 * @param   integer  lifetime in seconds
	 * @param   bool     是否使用salt，默认为false
	 * 
	 * @return  boolean
	 */
	public static function set($name, $value, $expiration = NULL, $use_salt = false)
	{
		if ($expiration === NULL)
		{
			// Use the default expiration
			$expiration = self::$expiration;
		}
        
		if ($expiration !== 0)
		{
			// The expiration is expected to be a UNIX timestamp
			$expiration += time();
		}
        
		// Add the salt to the cookie value
		if ($use_salt)
		{
		    $value = self::salt($name, $value).'~'.$value;
		}

		return setcookie($name, $value, $expiration, self::$path, self::$domain, self::$secure, self::$httponly);
	}
    
	/**
	 * 删除一个变量
	 * 
	 * Deletes a cookie by making the value NULL and expiring it.
	 *
	 *     cookie::delete('theme');
	 *
	 * @param   string   cookie变量名称
	 * 
	 * @return  boolean
	 */
	public static function delete($name)
	{
		// Remove the cookie
		unset($_COOKIE[$name]);

		// Nullify the cookie and make it expire
		return setcookie($name, NULL, -86400, self::$path, self::$domain, self::$secure, self::$httponly);
	}
    
	/**
	 * 生成一个加盐的字符串
	 * 
	 * Generates a salt string for a cookie based on the name and value.
	 * http://en.wikipedia.org/wiki/Salt_(cryptography)
	 *
	 *     $salt = cookie::salt('theme', 'red');
	 *
	 * @param   string   变量名称
	 * @param   string   变量值
	 * 
	 * @return  string 加密后的字符串
	 */
	public static function salt($name, $value)
	{
		// Require a valid salt
		if ( !self::$salt)
		{
			throw new \Exception('请设置一个加盐字符串：cookie::$salt.');
		}

		// Determine the user agent
		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';
        
		return sha1($agent.$name.$value.self::$salt);
	}
    
}
?>