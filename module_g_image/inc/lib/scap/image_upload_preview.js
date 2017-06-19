/**
 * 图片预览相关实现
 * 
 * @version $Id: image_upload_preview.js 213 2013-02-04 09:48:53Z liqt $
 * @creator liqt @ 2013-02-01 21:39:32 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * 配合上传图片前实现本地预览功能
	 * - @todo 暂不支持ie8以下，因为使用到html5的FileReader
	 * 
	 * @param handle_img 图片预览img的标识，如 'img.preview_image_cover'
	 */
	image_upload_preview: function(handle_img)
	{
		$(this).change(function(){
			if (window.File && window.FileReader && window.FileList && window.Blob)
		    {
		        var input = this;// 将jquery对象转换为dom对象
		        if (input.files && input.files[0])
		        {
		            var reader = new FileReader();
		            reader.onload = function (e)
		            {
		                $(handle_img).attr('src', e.target.result);
		            };
		            reader.readAsDataURL(input.files[0]);
		        }
		    }
		    else
		    {
		        console.log('该浏览器不支持FileReader功能，render_local_image_preview()无效。');
		        return;
		    }
		});
	}
});