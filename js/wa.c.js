(function(e,t){var n={},s="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwx",a=e.xmlhttp(),i={},r;function o(){var e=Array.prototype.slice,t=e.call(arguments);return function(){return t[1].apply(t[0],t.slice(2).concat(e.call(arguments)))}}n.websocket=function(t){var n=window.WebSocket?new WebSocket(t):null,s={send:function(e){n&&n.send(e);return s}};t=["open","message","close","error"];while(t.length){s[t[t.length-1]]=o(null,function(t,a){if(n&&e.is_function(a)){n["on"+t]=function(e){a.call(s,e);return s}}return s},t.pop())}return s};function l(t,n,s,i){a.abort(),a.open(t,n,true),a.onreadystatechange=e.is_function(s)?function(){this.readyState==4&&s.call(this,this.xmlhttp.responseText)}:null,a.xmlhttp.upload.onprogress=e.is_function(i)?i:null;return a}function u(e){if(r&&r!==e&&r.style){r.style.display="none"}}function c(){return false}e.bind(window,"mousedown",u);n.entime=function(n){n=e.date("YmdHis",n===t?e.time():n);for(var a=4;a<14;a+=2){n+=s[n.substr(a,2)]}return n.substr(0,4)+n.substring(14)},n.detime=function(t){if(e.is_string(t)&&t.length==9&&e.ltrim(t,s)==""){t=e.mktime(s.indexOf(t.charAt(6)),s.indexOf(t.charAt(7)),s.indexOf(t.charAt(8)),s.indexOf(t.charAt(4)),s.indexOf(t.charAt(5)),t.substr(0,4)|0);return t>0?t:0}return 0},n.over_input=function(e,n,s,a){e.onblur=function(){clearInterval(a);n.call(e,s)};a=setInterval(function(){n.call(e,s)},a===t?420:a|0)},n.status_callback=function(s,a){var i=n.detime(s.substr(0,9)),r=e.is_function(a)?a:alert;if(i<1||(a=e.json_decode(s.substring(9)))===null){return void r.call(window,s)}if(a.response_time===t){a.response_time=i}switch(a[1]){case"goto":case"goto_referer":return void e.go(a[1]=="goto"?a[0]:a.http_referer||a[0]);case"warn":return void alert(a[0]);default:i=n[a[1]]||window[a[1]];e.is_function(i)?i.call(a):r.call(window,s)}},n.ajax_query=function(t,s){var a="get",i=null;if(s){if(s.confirm&&confirm(s.confirm)===false){return false}if(s.prompt){i=prompt(s.prompt,s.value);if(i===null){return false}a="post",i="value="+e.urlencode(i)}}l(a,t,n.status_callback).send(i);return false},n.query_act=function(t,n){for(var s={},a=("(0)"+location.search.substr(1)).match(/\((\d)\)([\%\+\-\.\/\=\w]+)?/g)||[],i;i=a.shift();){s[i.substr(1,1)]=i.substr(3)}for(a in t){s[a]=t[a]}i="?"+s[0];s[0]=null;for(a in s){if(s[a]!==null){i+="("+a+")"+s[a]}}return n?i:e.go(i)},n.filter=function(t){if(t["field[]"].nodeType){return e.go(t.action)}for(var s=[],a=t["field[]"].length,i=1;i<a;++i){s[s.length]=[t["field[]"][i].value,t["query[]"][i].value,e.urlencode(t["value[]"][i].value)].join(".")}a={},a[t.dataset.index]=s.join("/");return n.query_act(a)},n.filter_add_conditions=function(t){e.get("dl",t.form).appendChild(e.get("dd",t.form).cloneNode(true)).removeAttribute("style")};n.menu_show=function(t){var n=e.get("#"+t.dataset.menu);n.onmousedown=function(){e.stopbubble(this)};n.onmouseup=function(){this.style.display="none"};t.onmousedown=function(){e.stopbubble(this);u(n)};t.onclick=function(){if(e.getstyle(n).display=="none"){r=n;n.style.display="block";t=e.offset_point(this);n.style.left=t.left+(t.width-n.offsetWidth)/2+"px"}else{n.style.display="none"}return false};return t.onclick()};n.form_fields=function(e){for(var n=0,s=[],a={};n<e.length;n++){if(e[n].name&&a["_"+e[n].name]===t){a["_"+e[n].name]=true;s.push(e[n].name)}}return s};n.form_values=function(e,t){};n.form_disableds=function(n,s){s=s===t?true:!!s;for(var a=0;a<n.length;a++){if(n[a].name){n[a].disabled=s}}e.cache(n,"onsubmit")||e.cache(n,"onsubmit",n.onsubmit);n.onsubmit=s?c:e.cache(n,"onsubmit")};n.form_post_file=function(e){};n.form_post_values=function(e){for(var n={},s={},a=0,i,r,o,l,u;a<e.length;a++){i=e[a];if(!i.name||i.value===t||i.disabled||(i.type=="checkbox"||i.type=="radio")&&i.checked===false){continue}for(r=n,o=i.name.match(/^\w+|\[\w+\]|\[\]/g)||[],l=0;l<o.length;l++){if(l==0&&o[l].charAt(0)=="["){break}if(o[l]=="[]"){u=o.slice(0,l).join("_");s[u]===t?s[u]=0:++s[u];u=s[u]}else{u=o[l].replace(/^\[|\]$/,"")}if(l+1==o.length){r[u]=i.value;break}if(r[u]===t){r[u]={}}r=r[u]}}return n};n.form_post=function(s,a){var i,r,o,u,c;if(a){i=n.form_post_values(s);for(r in a){if(e.is_string(o=i[r]===t?"":i[r])){u=e.utf8_encode(o).length;if(u>=a[r][0]&&u<=a[r][1]&&(a[r][2]?a[r][2].test(o):true)){continue}}else{if(a[r][2]){u=[];for(c in o){if(a[r][2].test(o[c])){continue}u=null;break}}else{u=e.array_values(o)}if(u&&u.length>=a[r][0]&&u.length<=a[r][1]){continue}}s=s[r]||s[r+"[]"];if(s.nodeType){if(!s.onfocus){s.onfocus=function(){n.over_input(this,function(t){var n=e.utf8_encode(this.value).length;this.className=n>=t[0]&&n<=t[1]&&(t[2]?t[2].test(this.value):true)?null:"r"},a[r])},s.className="r"}s.focus()}if(s.length){s=s[0].parentElement.parentElement;s.onclick=function(){this.className="set"},s.className="set-warn"}return false}}a=e.get("tr>td>div>div>div",s.firstChild.tFoot);l("post",s.action,function(e){n.status_callback(e),n.form_disableds(s,false),a.style.width="0%"},function(t){a.style.width=e.round(t.loaded*100/t.total)+"%"}).send(new FormData(s));n.form_disableds(s);return false};n.progressbar=function(t,n){var s=e.create("div"),a=s.appendChild(e.create("div"));e.is_string(n)&&void(s.style.width=n),a=a.appendChild(e.create("div")),s.className="wa_progressbar",a.style.width=(t|0)+"%";if(t&&t.nodeType==1){t.appendChild(s);return function(e){a.style.width=(e|0)+"%"}}return s.outerHTML};n.signin=function(t){var s=["username="+e.urlencode(t.username.value),"password="+e.md5(t.password.value)],a=e.get("i",t);t.captcha_encrypt&&t.captcha_decrypt&&s.push("captcha_encrypt="+e.urlencode(t.captcha_encrypt.value),"captcha_decrypt="+e.urlencode(t.captcha_decrypt.value)),a.innerHTML="",n.form_disableds(t),l("post",t.action,function(s){var r=n.detime(s.substr(0,9)),o=t.keep.value|0;clearTimeout(i.signin);if(r){if(e.abs(e.time()-r)<o){t.keep.checked?e.setcookie(t.dataset.tagname,s.substr(9),e.time()+o):e.setcookie(t.dataset.tagname,s.substr(9));return e.go(t.dataset.referer)}a.innerHTML=a.dataset.wa_warn_client_time_diff}else{a.innerHTML=s}a.style.opacity=1,n.form_disableds(t,false),i.signin=setTimeout(function(){a.style.opacity=0},4e3)}).send(s.join("&"));return false},n.signin_captcha_refresh=function(e){e.style.backgroundImage=null,e.innerHTML="Loading..",n.form_disableds(e.parentElement),l("get","?/wa/captcha",function(t){e.innerHTML="",e.parentElement.captcha_encrypt.value=t,e.style.backgroundImage='url("?/wa/captcha(1)'+t+'")',n.form_disableds(e.parentElement,false)}).send()};n.user_change_password=function(t){if(t.password_old.dataset.password_md5!=e.md5(t.password_old.value)){alert(t.dataset.error_old_password);return false}if(!t.password_confirm.value||t.password_confirm.value!=t.password_new.value){alert(t.dataset.error_confirm_password);return false}n.form_disableds(t),l("post",t.action,function(s){if(n.detime(s.substr(0,9))){return e.go(location.search)}alert(s);n.form_disableds(t,false)}).send(e.http_build_query({password_old:e.md5(t.password_old.value),password_new:t.password_new.value}));return false},n.user_change_password_input=function(){this.style.background=(this.name=="password_old"?this.dataset.password_md5==e.md5(this.value):this.value&&this.value==this.form.password_new.value)?"#e0ffe0":"#ffe0e0"},n.user_change_password_test=function(){for(var n=e.get("div",this.form),s=e.utf8_encode(this.value),a={},i=s.length,r=0,o;r<i;r++){o=s.charCodeAt(r);if(a[o]===t){a[o]=1}else{++a[o]}}s=0;for(r in a){o=a[r]/i;s-=o*e.log(o)/e.log(2)}s=s/4*100|0;switch(true){case s<33:this.style.background="#ffe0e0";break;case s<66:this.style.background="#ffffe0";break;default:this.style.background="#e0ffe0"}this.form.password_confirm.style.background=this.value&&this.value==this.form.password_confirm.value?"#e0ffe0":"#ffe0e0"},n.input_time=function(){var t=[],s=e.create("div"),a=e.date("G,0,0,n,j,Y").split(","),i=a.join(",").split(","),r,o,l;function u(){for(var n=e.mktime(0,0,0,i[3],1,i[5]),s=e.date("w",n)-1,r=e.date("t",n)*1+s,o=0,l;o<42;o++){t[o].className=t[o].innerHTML="",t[o].style.cssText="visibility:hidden";if(o>s&&o<=r){t[o].innerHTML=l=o-s,t[o].style.visibility="visible",l==a[4]&&i[3]==a[3]&&i[5]==a[5]?f.call(t[o]):l==1&&f.call(t[o])}}}function c(){var t=this.name=="current"?e.time():e.mktime.apply(e,i);e.is_function(l)?l.call(o,t):o.value=e.date(l,t),s.style.display="none"}function d(){switch(this.name){case"m":return u(i[3]=this.value);case"y":return u(i[5]=this.value);case"h":return void(i[0]=this.value);case"i":return void(i[1]=this.value);case"s":return void(i[2]=this.value);default:return f.call(this)}}function f(){t[r]&&(t[r].className=""),t[r=this.name].className="this_day",i[4]=this.innerHTML}s.className="wa_input_time",s.onmouseover=function(){document.onmousedown=o.onblur=null},s.onmouseout=function(){document.onmousedown=o.onblur=n.input_time},s.innerHTML=["<table><thead><tr>",'<td colspan="4"><select name="y"></select><span>-</span><select name="m"></select></td>','<td colspan="3" style="text-align:right;"><button name="current" class="b">Current time</button></td>',"</tr></thead><tbody><tr>","<td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td>","</tr></tbody><tfoot><tr>",'<td colspan="5"><select name="h"></select><span>:</span><select name="i"></select><span>:</span><select name="s"></select></td>','<td colspan="2" style="text-align:right;"><button name="select">Selected</button></td>',"</tr></tfoot></table>"].join(""),u(function(){for(var n=0,a;n<42;n++){n%7||(a=e.get("tbody",s).appendChild(e.create("tr")));t[n]=a.appendChild(e.create("td")),t[n].name=n,t[n].onmouseover=function(){this.style.color="green"},t[n].onmouseout=function(){this.style.color=null},t[n].onmousedown=d}n=e.query("button",s),n[0].onclick=n[1].onclick=c}()),function(t,n,a,i){var r=e.get(t,s);while(n<=a){t=r.appendChild(e.create("option")),t.value=t.innerHTML=n,t.selected=i==n++}r.onchange=d;return arguments.callee}("select[name=y]",i[5]-40,i[5]*1+10,i[5])("select[name=m]",1,12,i[3])("select[name=h]",0,23,i[0])("select[name=i]",0,59,i[1])("select[name=s]",0,59,i[2]),(n.input_time=function(t,a){if(e.is_string(a)||e.is_function(a)){o=t,l=a,t.onblur=n.input_time;if(document.body){t=e.offset_point(t),s.style.top=t.bottom-8+"px",s.style.left=t.left+32+"px",e.parent(s)||e.append(s)}return void(s.style.display="")}return void(s.style.display="none")}).apply(n,arguments)},n.ajax=l,window.wa=n})($);