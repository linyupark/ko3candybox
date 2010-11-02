function candyCloneInput(box_id){
    var clone = $(box_id).clone().inject(box_id, 'after');
    var clone_inputs = clone.getElements('input');
    clone_inputs.each(function(el){
        el.set('value', '');
    });
}

function candyFadeout(box_id){
    var box = new Fx.Tween($(box_id), {
        onComplete: function(){
            $(box_id).dispose();
        }
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

// 鼠标mouseover效果
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
    if(arr != null) return unescape(arr[2]);
    return null;

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