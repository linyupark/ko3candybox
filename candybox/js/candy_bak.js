var _date_month = ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'];
var _date_days = ['日','一','二','三','四','五','六'];

window.addEvent('domready', function(){
    candyPopElement();
});

function candyCloneInput(box_id){
    var clone = $(box_id).clone().inject(box_id, 'after');
    var clone_inputs = clone.getElements('input');
    clone_inputs.each(function(el){
        el.set('value', '');
    });
}

function candyFadeout(box_id){
    var box = new Fx.Tween($(box_id), {
        onComplete: function(){$(box_id).dispose();}
    });
    box.start('opacity', [,0]);
}

// 自动限制BOX中的图片宽度
function candyImageAutoResize(box_id, box_w){
    box_w = box_w ? box_w : $(box_id).getSize().x;
    var imgs = $(box_id).getElements('img');
    imgs.each(function(el){
        if(box_w < el.getSize().x){

            el.setStyle('width', (box_w-20));
        }
    });
}

// ckeidtor初始化
function candyCKE(text_id, toolbar){
    var ck = CKEDITOR.replace(text_id, {
        toolbar: toolbar,
        contentsCss: '/candybox/Editor/ckeditor/contents.css'
    });
    return ck;
}

// 弹出DIV
function candyPopElement(){
    $$('.candyPopElement').each(function(el){
        el.setStyle('position', 'relative');
        var cnt = $(el.get('rel'));
        cnt.setStyle('display', 'none');
        cnt.setStyle('position', 'absolute');
        el.addEvent('mouseenter', function(){
            this.setStyle('zIndex', 1);
            cnt.setStyle('display', 'block');
        });
        el.addEvent('mouseleave', function(){
            cnt.setStyle('display', 'none');
        });
    });
}

// 表单提交
var CandyForm = new Class({
    Implements: Options,
    id: '',
    options: {
        markError: 'err#',
        markSuccess: 'success#',
        markResp: 'resp#',
        textSending: '发送中',
        textError: '请检查表单数据',
        textSuccess: '成功发送',
        cssError: 'candy_form_err',
        cssSuccess: 'candy_form_success',
        cssResp: 'candy_form_resp',
        btnSubmit: '',
        outputMethod: 'html',
        delay: 500,
        redirect: '',
        callback: ''
    },
    initialize: function(id, options){
        this.id = id;
        this.setOptions(options);
    },
    send: function(){
        var form = $(this.id);
        var method = form.get('method');
        var url = form.get('action');
        var opts = this.options;
        var data = $chk(opts.data) ? form.toQueryString()+'&'+opts.data : form;
        var submit = $chk(opts.btnSubmit) ? $(opts.btnSubmit) : form.getElement('input[type=submit]');
        // stop submit
        form.addEvent('submit', function(e){
            e.stop();
        });

        var request = new Request({
            method: method,
            url: url,
            data: data,
            onRequest: function(){
                $$('.'+opts.cssError+', .'+opts.cssSuccess+', .'+opts.cssResp).destroy();
                submit.set('disabled', true);
                submit.set('value', opts.textSending);
                // loading image
                form.grab(new Element('img', {
                    'id': 'candyFormLoading',
                    'src': '/candybox/Media/image/loading.gif'
                }), 'bottom');
            },
            onSuccess: function(resp){

                // remove loading image
                $('candyFormLoading').destroy();

                var output = resp;
                var outputClass = '';
                var isSuccess = false;
                var jumper = function(){
                        window.location.href = arguments[0];
                    };

                if(output.contains(opts.markError)){
                    output = output.replace(opts.markError, '');
                    outputClass = opts.cssError;
                    submit.set('value', opts.textError);
                } else {
                    isSuccess = true;
                    if(output.contains(opts.markResp)){
                        output = output.replace(opts.markResp, '');
                        outputClass = opts.cssResp;
                    }
                    if(output.contains(opts.markSuccess)){
                        output = output.replace(opts.markSuccess, '');
                        outputClass = opts.cssSuccess;
                    }
                    submit.set('value', opts.textSuccess);
                }

                submit.set('disabled', false);

                // output
                if(outputClass != ''){
                    if(opts.outputMethod == 'html'){
                        form.grab(new Element('div', {
                            'class': outputClass,
                            'html': output,
                            'styles': {
                                opacity: 0
                            }
                        }), 'bottom');

                        $$('.'+opts.cssError+', .'+opts.cssSuccess+', .'+opts.cssResp).fade('in');
                    }
                    if(opts.outputMethod == 'alert'){
                        alert(output);
                    }
                }

                if(isSuccess == true){
                    // 跳转
                    if($chk(opts.redirect)) {
                        jumper.delay(opts.delay, this, opts.redirect);
                    }
                    // 回调
                    if($chk(opts.callback)){
                        var type = $type(opts.callback);
                        if(type == 'string')
                            eval(opts.callback);
                        if(type == 'function'){
                            opts.callback.delay(opts.delay, this, output);
                        }
                    }
                }
            }
        });
        request.send();
    }
});


/**
 * 鼠标mouseover效果
 */
function candyHover(box, els, color){
    var backgroundColor = '#f5f5f5';

    if($chk(color)){
        backgroundColor = color;
    }

    if($defined($(box))){
        var items = $(box).getElements(els);
        items.addEvent('mouseover', function(){
            this.setStyle('background', backgroundColor);
        });
        items.addEvent('mouseout', function(){
            this.setStyle('background', 'none');
        });
    }
}

function candyDatePicker(bindClassName, timer, format)
{
    new DatePicker('.'+bindClassName, {
        allowEmpty: true,
        months: _date_month,
        days: _date_days,
        timePicker: timer,
        inputOutputFormat: format,
        format: format
    });
}

/**
 * 对Date的扩展，将 Date 转化为指定格式的String
 * 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q) 可以用 1-2 个占位符
 * 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
 * eg:
 * (new Date()).fmt("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
 * (new Date()).fmt("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04
 * (new Date()).fmt("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04
 * (new Date()).fmt("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04
 * (new Date()).fmt("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18
 */
Date.prototype.fmt=function(fmt) {
	var o = {
	"M+" : this.getMonth()+1, //月份
	"d+" : this.getDate(), //日
	"h+" : this.getHours()%12 == 0 ? 12 : this.getHours()%12, //小时
	"H+" : this.getHours(), //小时
	"m+" : this.getMinutes(), //分
	"s+" : this.getSeconds(), //秒
	"q+" : Math.floor((this.getMonth()+3)/3), //季度
	"S" : this.getMilliseconds() //毫秒
	};
	var week = {
	"0" : "\u65e5",
	"1" : "\u4e00",
	"2" : "\u4e8c",
	"3" : "\u4e09",
	"4" : "\u56db",
	"5" : "\u4e94",
	"6" : "\u516d"
	};
	if(/(y+)/.test(fmt)){
		fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
	}
	if(/(E+)/.test(fmt)){
		fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "\u661f\u671f" : "\u5468") : "")+week[this.getDay()+""]);
	}
	for(var k in o){
		if(new RegExp("("+ k +")").test(fmt)){
			fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
		}
	}
	return fmt;
}

function candySetCookie(name, value)
{
    var Days = 30; //此 cookie 将被保存 30 天
    var exp  = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function candyGetCookie(name)//读取cookies函数
{
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
     if(arr != null) return unescape(arr[2]); return null;

}
function candyDelCookie(name)//删除cookie
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

function candyPlaceholder(color)
{
    $$('input').each(function(el)
    {
        var text = el.get('placeholder'),
        defaultColor = el.getStyle('color'),
        defaultValue = el.get('value');

        if (text && !defaultValue)
        {
            el.setStyle('color', color).set('value', text).addEvent('focus', function()
            {
                if (el.value == '' || el.value == text)
                {
                    el.setStyle('color', defaultColor).set('value', '');
                }
            }).addEvent('blur', function()
            {
                if (el.value == '' || el.value == text)
                {
                    el.setStyle('color', color).set('value', text);
                }
            });

            var form = el.getParent('form');
            if (form)
            {
                form.addEvent('submit', function()
                {
                    if (el.value == text)
                        el.set('value', '');
                });
            }
        }

        if(defaultValue){
            el.addEvent('click', function(){
                el.select();
            });
        }
    });
}