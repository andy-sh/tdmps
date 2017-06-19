/**
 * xheditor的scap js封装
 * 
 * @version $Id: xheditor.js 211 2013-02-04 09:09:21Z liqt $
 * @creator liqt @ 2013-02-04 12:43:51 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * xheditor的scap便携封装函数
	 * 
	 * @param height 组件高度，如400
	 * @param readonly 是否只读(只读则显示html，否则显示编辑器)，true | false
	 * @param options 其他配置项:1.符合xheditor参数规范 2.自定义的配置：是否可上传图片uploadImg: true|false, 是否可上传附件uploadFile: true|false
	 * @param default_upload_url 默认的上传应用链接,如果为null或者undefined，使用默认上传链接
	 */
	xheditor: function(height, readonly, options, default_upload_url)
	{
		var options = options || {};
		
		if (readonly)
		{
			// 如果只读，则构造显示html，并隐藏+禁用原textarea(避免提交数据)
			$(this).hide().attr('disabled', 'true').after('<div style="height:'+height+'px;overflow-y:auto;overlflow-x:auto;">'+$(this).val()+'</div>');
			return;
		}
		
		default_upload_url = set_arg_value(default_upload_url, '?m=module_g_form.ui_server.upload_for_xheditor');
		
		// 默认配置项
		var defaults = {
			height: height,
			tools: 'mini',
			showBlocktag: true,
			internalScript: false,
			inlineScript: true,
			internalStyle: false,
			inlineStyle: true,
			forcePtag: true,
			cleanPaste: 2,
			upMultiple: 1,
			upImgUrl: default_upload_url,// 默认可上传
			upImgExt:"jpg,jpeg,gif,png",
			upLinkUrl: '',// 默认不能上传
			upFlashUrl: '',// 默认不能上传
			upMediaUrl: ''// 默认不能上传
		};
		
		if (is_set(options.uploadImg))
		{
			if (options.uploadImg == true)
			{
				options.upImgUrl = default_upload_url;
			}
			else
			{
				options.upImgUrl = '';
			}
		}
		
		if (is_set(options.uploadFile))
		{
			if (options.uploadFile == true)
			{
				options.upLinkUrl = default_upload_url;
			}
			else
			{
				options.upLinkUrl = '';
			}
		}
		
		// 合并的配置项
		var settings = $.extend({}, defaults, options);
		
		$(this).xheditor(settings);
	}
});