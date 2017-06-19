/**
 * 图片大小设置脚本
 * http://www.saurdo.com/12/01/image-resizer-chrome-extension
 * create time: 2012-3-8 下午03:40:12
 * @version $Id: image_resize.js 114 2012-04-12 10:06:14Z liqt $
 * @author LiQintao
 */

/**
 * -暂不支持IE浏览器
 * -chrome中当布局使用了column时，图片位于右侧区域，image的起始坐标会计算错误。
 */
var rezbox = false;

function cusrez(img, e)
{
	var mxwidth =  710;
	var mxheight = 430;
	
	// test if another image has a rez box open
	if(!rezbox)
	{
		// get our viewport width
		viewportX = window.innerWidth || document.body.clientWidth;
		console.log('viewportX:'+viewportX);
		
		// get real left offset of image
		imgL = realOffset(img, 0)		
		// get real top offset of image
		imgT = realOffset(img, 1)
		// test if there is room to open rezbox on the right
		// img.pos plus width of rezbox, plus buffer space less than width of window
		// or img.pos plus the img width - rezbox size less than 0, page only scrolls to the right
		roomRight = imgL + mxwidth + 50 <= viewportX || imgL + img.width - mxwidth <= 0;
 
		// get the mouse y position, IE uses a different method
		mouseposY = window.pageYOffset+e.clientY;
		// get mouse x position
		mouseposX = window.pageXOffset+e.clientX;
		// get the distance of the image from the left side of the window
		// if the image is too far to the right side then create the box opposite way
		imgX = roomRight ? imgL : imgL + img.width - mxwidth;
 
		rez_div = document.createElement('DIV');
		new_img = document.createElement('IMG');
		new_img.src = img.src;
		new_img.width = img.width;
		new_img.style.width = img.width+'px';
		new_img.style.position = "absolute";
		new_img.style.top = imgT+'px';
		new_img.style.left =  imgL+'px';
		new_img.style.opacity = "0.5";
		new_img.style.filter = "alpha(opacity=50)";
		// set style
		rez_div.style.position = "absolute";
		// set position to the img pos
		rez_div.style.top = imgT+'px';
		// set rezbox position to imgX
		rez_div.style.left = imgX+'px';
		// set our sizing box width - would be max width of image
		rez_div.style.width = mxwidth+'px';
		// set our sizing box height - would be max height of image
		rez_div.style.height = mxheight+'px';
		// make a border and background for it
		rez_div.style.border = "1px solid #f00";
		rez_div.style.background = "#fff";
		// needs to have an actual background because of IE, so why not make it semi-opaque
		rez_div.style.opacity = "0.5";
		rez_div.style.filter = "alpha(opacity=50)";
		// let's put a little message inside
		rez_div.innerHTML = '<div style="padding-right: 5px; background: #000; color: #fff; float: left;">单击鼠标左键确定设置。</div><div style="padding-left: 5px; background: #000; color: #fff; float: right;" id="rezsize"></div>';
		document.body.appendChild(new_img);
		// set our max height and width of image
		// should be same as the width and height of rez box
		img.style.maxWidth = mxwidth+'px';
		img.style.maxHeight = mxheight+'px';
 
		// give our rez box a little mousemove event!
		rez_div.onmousemove = function(e){
			// get our current mouse Y and mouse X
			mouseposY = window.pageYOffset+e.clientY;
			mouseposX = window.pageXOffset+e.clientX;
			// get real left offset of new_img
			new_imgL = imgL
			// get real left offset of rezbox
			rez_divL = imgX;
			// get real top offset of rezbox
			rez_divT = imgT;
			// change our height to be the current mouse position on the screen minus the images position on the screen
			//alert((mouseposY - imgY));
			new_img.style.height = (mouseposY - rez_divT)+'px';
			// check if our image has room
			if(roomRight){
				// change our width to be the current mouse position on the screen minus the images position on the screen
				new_img.style.width = (mouseposX - new_imgL)+'px';
				// check to see if we want to preserve aspect ratio, shift key being pressed?
				if(e.shiftKey){  
					// aspect ratio calculation img.ow/img.oh
					p = mxwidth / mxheight;
					d = mxheight / mxwidth;
					// our height is the width of the image divided by our ratio!
					if( p > d){
						new_img.style.height = (new_img.width / p)+'px';
					}
					else{
						new_img.style.width = (new_img.height / d)+'px';
					}
				}
			}
			// whoa, now it's in reverse!
			else{
				// wherever the rezbox is plus its width gets the right corner of it minus the mouse position gets how far you are from that position
				new_img.style.width = (rez_divL + mxwidth - mouseposX)+'px';
				// manually setting the image location, rezbox plus its width minus however big the image already is
				new_img.style.left = rez_divL + mxwidth - new_img.width+'px';
				//new_img.width = (new_img.offsetLeft - mouseposX);
				
				// check to see if we want to preserve aspect ratio, shift key being pressed?
				if(e.shiftKey){  
					// aspect ratio calculation img.ow/img.oh
					p = mxwidth / mxheight;
					d = mxheight / mxwidth;
					// our height is the width of the image divided by our ratio!
					if(p > d){
						new_img.style.height = (new_img.width / p)+'px';
					}
					else{
						new_img.style.width = (new_img.height / d)+'px';
						new_img.style.left = rez_divL + mxwidth - new_img.width+'px';
					}
				}
			}
			
			document.getElementById('rezsize').innerHTML = new_img.width+"x"+new_img.height;
		}
		
		// need a way to stop all this craziness!
		rez_div.onclick = function(){
			// now onmousemove does nothing!
			this.onmousemove = null; 
			img.style.width = new_img.width+'px';
			img.style.height = new_img.height+'px';
			// get rid of that damn rez box
			document.body.removeChild(this);
			document.body.removeChild(new_img);
			// no more rez box!
			rezbox = false;
		}
		// put that rez box on the page
		document.body.appendChild(rez_div);
		// there's a rez box alright!
		rezbox = true;
		//
		img.resized = false;
	}
}

/**
 * 获取图像的起始坐标
 * @param ob img handle
 * @param type 0:left, 1:top
 * 
 * @returns postion
 */
function realOffset(ob, type)
{
	if(type == 0)
	{
		offLeft = ob.offsetLeft;
		console.log('offLeft:'+ob.nodeName+' - '+ob.offsetLeft);
		while(ob != document.body){
			ob = ob.offsetParent; 
			offLeft += ob.offsetLeft;
			console.log('offLeft:'+ob.nodeName+' - '+ob.offsetLeft);
		}
		return offLeft;
	}
	else{
		offTop = ob.offsetTop;
		console.log('offTop:'+ob.nodeName+' - '+ob.offsetTop);
		while(ob != document.body){
			ob = ob.offsetParent; 
			offTop += ob.offsetTop
			console.log('offTop:'+ob.nodeName+' - '+ob.offsetTop);
		}
		return offTop;
	}
	return false;
}