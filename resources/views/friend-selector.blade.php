<script type="text/javascript" src="{{env('APP_URL')}}/server/js/friend-selector-jquery.js"></script>

<script type="text/javascript">
var Fjqshadow = new Object();
/**
    获取元素的xpath
    特性：
    - 转换xpath为csspath进行jQuery元素获取
    - 仅生成自然表述路径（不支持非、或）
    @param dom {String/Dom} 目标元素
    @returns {String} dom的xpath路径
*/
Fjqshadow.domXpath = function(dom) {
  dom = Fjq(dom).get(0);
  var path = "";
  for (; dom && dom.nodeType == 1; dom = dom.parentNode) {
    var index = 1;
    for (var sib = dom.previousSibling; sib; sib = sib.previousSibling) {
      if (sib.nodeType == 1 && sib.tagName == dom.tagName)
        index++;
      }
    var xname =  dom.tagName.toLowerCase();
    if (dom.id) {
      xname += "[@id=\"" + dom.id + "\"]";
    } else {
      if (index > 0)
        xname += "[" + index + "]";
    }
    path = "/" + xname + path;
  }

  path = path.replace("html[1]/body[1]/","html/body/");

  return path;
};

Fjqshadow.xpathDom = function(xpath){
        // 开始转换 xpath 为 css path
        // 转换 // 为 " "
        xpath = xpath.replace(/\/\//g, " ");
        // 转换 / 为 >
        xpath = xpath.replace(/\//g, ">");
        // 转换 [elem] 为 :eq(elem) ： 规则 -1
        xpath = xpath.replace(/\[([^@].*?)\]/ig, function(matchStr,xPathIndex){
                var cssPathIndex = parseInt(xPathIndex)-1;
                return ":eq(" + cssPathIndex + ")";
        });
        // 1.2 版本后需要删除@
        xpath = xpath.replace(/\@/g, "");
      // 去掉第一个 >
      xpath = xpath.substr(1);
            // 返回jQuery元素
      return xpath;
};

var selectPos = window.localStorage.selectPos

var demoHtml = '<div id="friend-link-demo"><span>友情链接示例：</span><a href="#">友情链接1</a><a href="#">友情链接2</a><a href="#">友情链接3</a><a href="#">友情链接4</a><a href="#">友情链接5</a><a href="#">友情链接6</a><a href="#">友情链接7</a><a href="#">友情链接8</a><a href="#">友情链接9</a><a href="#">友情链接10</a></div>'

var currectDom
Fjq(document).ready(function () {
  // 监控输入值
  $('#xpath').bind('input propertychange', function() {
    var text = $(this).val()
    window.localStorage.friendSelector = text
    var dompath = Fjqshadow.xpathDom(text)
    if ($(dompath).length > 0) {
      currectDom = dompath
      window.localStorage.friendSelector = text
      demo()
    }
  })

  // 默认选中前中后
  var qzhid
  if (window.localStorage.selectPos) {
    var qzhid = '#' + window.localStorage.selectPos
  } else {
    var qzhid = 'inner'
  }

  Fjq(qzhid).attr('checked', 'true')

  Fjq('div,ul,p').hover(function () {
    Fjq('.hover').removeClass('hover')
    Fjq(this).addClass('hover')
  }, function () {
    Fjq(this).removeClass('hover')
  })

  Fjq('div,ul,p').click(function(e) {
    var xpath = Fjqshadow.domXpath(this)
    $("#console #xpath").val(xpath)
    window.localStorage.friendSelector = xpath

    currectDom = this

    demo()

    e.preventDefault()
    e.stopPropagation()
  })
})

function remove () {
  Fjq('.friend-link-selected').remove()
  Fjq('.selecter-box').removeClass('selecter-box')
}

function before () {
  if (selectPos !== 'before') {
    window.localStorage.selectPos = selectPos = 'before'
  }
  remove()
  Fjq(currectDom).before('<div class="friend-link-selected">' + demoHtml + '</div>')
}

function after () {
  if (selectPos !== 'after') {
    window.localStorage.selectPos = selectPos = 'after'
  }
  remove()
  Fjq(currectDom).after('<div class="friend-link-selected">' + demoHtml + '</div>')
}

function inner () {
  if (selectPos !== 'inner') {
    window.localStorage.selectPos = selectPos = 'inner'
  }
  remove()
  Fjq(currectDom).addClass('selecter-box')
  Fjq(currectDom).prepend('<div class="friend-link-selected">' + demoHtml + '</div>')
}

function demo () {
  console.log(currectDom)
  if (selectPos === 'before') {
    before()
  } else if (selectPos === 'after') {
    after()
  } else {
    inner()
  }
}

var checked = false
</script>

<style type="text/css">
.hover {
  position: relative;
}
.hover:before {
  content:" ";
  background: rgba(120, 173, 222, 0.7);
  position: absolute;
  z-index: 999999;
  opacity: 0.8;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  pointer-events: none;
}

.selecter-box > * {
  display: none !important;
}

.selecter-box .friend-link-selected {
  display: block !important;
  text-align: center;
  border: 1px dashed #ff8100;
}

#friend-link-demo a, #friend-link-demo span {
  display: inline-block;
  margin-right: 15px;
}

#console {
  bottom: 0;
  padding: 15px;
  position: fixed;
  left: 0;
  background: #ccc;
  z-index: 999999;
  box-shadow: 0 0 10px #000;
  border-radius: 0 5px 0 0;
}
#console em {
  font-style:normal;
  text-decoration: none !important;
}
#console label {
  border: none;
}
#console #xpath {
  border: 0;
  padding: 3px 5px;
  width: 200px;
}
</style>

<em id="console">
  <em class="xpath">
    <label for="xpath">Xpath：</label>
    <input type="text" id="xpath" name="xpath" />
    位置：
    <input type="radio" id="before" name="radio" onclick="before()" value="before">前</label>
    <label><input type="radio" id="inner" name="radio" onclick="inner()" value="inner">内部</label>
    <label><input type="radio" id="after" name="radio" onclick="after()" value="after">后</label>
  </em>
</em>