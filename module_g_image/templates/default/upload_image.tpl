<!--{<*
 * 图片上传
 * create time: 2012-02-25 上午10:50:23
 * @version $Id: upload_image.tpl 4 2012-07-21 07:04:47Z liqt $
 * @author gaox
*>}-->
<script type="text/javascript">
// Convert divs to queue widgets when the DOM is ready
$(function() {
    $("#uploader").pluploadQueue({
        // General settings
        runtimes : 'html5,flash,gears,silverlight,browserplus',
        url : '{<$url_upload>}',
        max_file_size : '10mb',
        chunk_size : '10mb',
        unique_names : true,

        // Specify what files to browse for
        filters : [
            {title : "Image files", extensions : "jpg,gif,png"}
        ],

        // Flash settings
        flash_swf_url : '{<$url_flash>}',

        // Silverlight settings
        silverlight_xap_url : '{<$url_silverlight>}'
    });

    // Client side form validation

    $('form').submit(function(e) {
        var uploader = $('#uploader').pluploadQueue();

        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });

            uploader.start();
        } 
        else {
            alert('您至少要选择一个文件。');
        }

        return false;
    });
});
</script>
<div>
	<form method="post" action="{<$url_upload>}">
        <div id="uploader">
            <p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
        </div>
        <br style="clear: both" />
    </form>
</div>