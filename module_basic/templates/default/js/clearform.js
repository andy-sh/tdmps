/**
 * description: 清空指定form的所有可输入元素值
 * create time: 2007-5-5 11:33:36
 * @version $Id: clearform.js 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

/**
 * formObj: the checked form
 * exceptObjName: 不需要清空的元素名称
 */
function clearForm(formObj, exceptObjName)
{
	if(formObj == null)
	{
		formObj = document.forms[0];
	}
	
	if(exceptObjName == null)
	{
		exceptObjName == "";
	}
	
	var selectObjs = formObj.getElementsByTagName("SELECT");
	for(var i=0; i<selectObjs.length; i++)
	{
		if((selectObjs[i].name=="")||(eval("/(^|,)"+selectObjs[i].name+"(,|$)/g").test(exceptObjName)))
			continue;
		selectObjs[i].value="";
	}

	var inputObjs = formObj.getElementsByTagName("INPUT");
	for(var i=0; i<inputObjs.length; i++)
	{
		if((inputObjs[i].name=="")||(eval("/(^|,)"+inputObjs[i].name+"(,|$)/g").test(exceptObjName)))
			continue;
		
		if(inputObjs[i].type.toUpperCase()=="TEXT")
			inputObjs[i].value="";
		else if((inputObjs[i].type.toUpperCase()=="RADIO")||(inputObjs[i].type.toUpperCase()=="CHECKBOX"))
			inputObjs[i].checked=false;
	}

	var textareaObjs = formObj.getElementsByTagName("TEXTAREA");
	for(var i=0; i<textareaObjs.length; i++)
	{
		if((textareaObjs[i].name=="")||(eval("/(^|,)"+textareaObjs[i].name+"(,|$)/g").test(exceptObjName)))
			continue;
		textareaObjs[i].value="";
	}   
}