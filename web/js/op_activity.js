
var opActivity=function(id,posturl){var t=this;var addEventListener=function(obj,type,listener,useCapture){if(obj.addEventListener){obj.addEventListener(type,listener,useCapture);}else if(obj.attachEvent){type='on'+type;obj.attachEvent(type,listener);}};var getHttpRequest=function(){var req=null;if(window.XMLHttpRequest){req=new XMLHttpRequest();}else if(window.ActiveXObject){try{req=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){req=new ActiveXObject("Microsoft.XMLHTTP");}}
return req;};this.post=function(){var req=getHttpRequest();req.open("POST",posturl,true);req.setRequestHeader("X-Requested-With","XMLHttpRequest");req.setRequestHeader("Content-Type","application/x-www-form-urlencoded");var data="";for(var i=0;i<this.form.elements.length;i++){data+="&"+this.form.elements[i].name+"="+this.form.elements[i].value;}
data=data.length>1?data.substring(1):"";req.onreadystatechange=function(e){if(req.readyState==4&&req.status==200){t.timeline.innerHTML=req.responseText+t.timeline.innerHTML;}}
req.send(data);}
var loadfunc=function(event){t.form=document.getElementById(id+"_form");t.body=document.getElementById(id+"_activity_data_body");t.timeline=document.getElementById(id+"_timeline");t.body.onkeyup=function(){var count=this.value.length;document.getElementById(id+"_count").innerHTML=140-count;};t.body.onkeyup();addEventListener(t.form,"submit",function(event){if(t.body.value&&t.body.value.length>0&&t.body.value.length<=140){t.post();t.body.value="";t.body.onkeyup();}
if(event.preventDefault){event.preventDefault();}else{return false;}},false);}
addEventListener(window,"load",loadfunc,false);}