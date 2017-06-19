<?php
/**
 * description: 在PHP中产生GUID序列
 * 
 * 通过服务器的 微妙时间+服务器地址(机器名和IP)+随机数 经过MD5产生而成;
 * 用法如下:
 * <code>
 * <?php
 *		$guid	 = new Guid(); 		
 *		for	($i = 1; $i <= 10; $i ++) 	
 *		{
 * 		 $guid->genGuid();
 *			 echo $guid->getGuid();
 *			 echo "<br/>";
 *	  	}
 * ?>
 * </code>
 * 
 * @version $Id: class.guid.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */
  
  	/**
  	 * Guid产生类
  	 */
	class Guid 
	{
		var $valueBeforeMD5;
		var $valueAfterMD5;
		var $HostName = 'localhost';
		var $IP = '127.0.0.1';
		
		/**
		 * Guid构造函数
		 */
		function Guid() {
			$this->genGuid();
		}
		
		/**
		 * 将服务器地址信息转化为字符串
		 */
		function toAddrStr() {
			$this->HostName = $_ENV["COMPUTERNAME"];
			$this->IP = $_SERVER["SERVER_ADDR"];
			return strtolower($this->HostName.'/'.$this->IP);
		}
		
		/**
		 * 获取当前系统时间的毫秒数
		 */
		function currentTimeMillis() {
			list ($usec, $sec) = explode(" ", microtime());
			return $sec.substr($usec, 2, 3);
		}	
		
		/**
		 * 产生一个随机数字串
		 */
		function nextLong() {
			$tmp = rand(0, 1) ? '-' : '';
			return $tmp.rand(1000, 9999).rand(1000, 9999).rand(1000, 9999).rand(100, 999).rand(100, 999);
		}
		
		/**
		 * 产生当前的GUID序列
		 */
		function genGuid() {
			$this->valueBeforeMD5 = $this->toAddrStr().':'.$this->currentTimeMillis().':'.$this->nextLong();
			$this->valueAfterMD5 = md5($this->valueBeforeMD5);
		}
		
		/**
		 * 获取到已产生的GUID字符串序列
		 */
		function getGuid() {
			$raw = strtoupper($this->valueAfterMD5);
			return substr($raw, 0, 8).substr($raw, 8, 4).substr($raw, 12, 4).substr($raw, 16, 4).substr($raw, 20);
		}

	}
?>