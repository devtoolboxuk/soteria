<?php

namespace devtoolboxuk\soteria;

use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    private $security;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->security = new SoteriaService();

    }

    private function _testArray()
    {
        return [
            '<a href="http://www.chaos.org/">www.chaos.org</a>',
            '<a name="X">Short \'a name\' tag</a>',
            '<td colspan="3" rowspan="5">Foo</td>',
            '<td colspan=3 rowspan=5>Foo</td>',
            '<td colspan=\'3\' rowspan=\'5\'>Foo</td>',
            '<td rowspan="2" class="mugwump" style="background-color: rgb(255, 204 204);">Bar</td>',
            '<td nowrap>Very Long String running to 1000 characters...</td>',
            '<td bgcolor="#00ff00" nowrap>Very Long String with a blue background</td>',
            '<a href="proto1://www.foo.com">New protocol test</a>',
            '<img src="proto2://www.foo.com" />',
            '<a href="javascript:javascript:javascript:javascript:javascript:alert(\'Boo!\');">bleep</a>',
            '<a href="proto4://abc.xyz.foo.com">Another new protocol</a>',
            '<a href="proto9://foo.foo.foo.foo.foo.org/">Test of "proto9"</a>',
            '<td width="75">Bar!</td>',
            '<td width="200">Long Cell</td>',
            'search.php?q=%22%3Balert(%22XSS%22)%3B&n=1093&i=410',
            'http://localhost/text.php/"><script>alert(“Gehackt!”);</script></form><form action="/...',
        ];
    }

    private function _resultArray()
    {
        return [
            '<a href="http://www.chaos.org/">www.chaos.org</a>',
            '<a name="X">Short \'a name\' tag</a>',
            '<td colspan="3" rowspan="5">Foo</td>',
            '<td colspan=3 rowspan=5>Foo</td>',
            '<td colspan=\'3\' rowspan=\'5\'>Foo</td>',
            '<td rowspan="2" class="mugwump" >Bar</td>',
            '<td nowrap>Very Long String running to 1000 characters...</td>',
            '<td bgcolor="#00ff00" nowrap>Very Long String with a blue background</td>',
            '<a href="proto1://www.foo.com">New protocol test</a>',
            '<img src="proto2://www.foo.com" />',
            '<a href="">bleep</a>',
            '<a href="proto4://abc.xyz.foo.com">Another new protocol</a>',
            '<a href="proto9://foo.foo.foo.foo.foo.org/">Test of "proto9"</a>',
            '<td width="75">Bar!</td>',
            '<td width="200">Long Cell</td>',
            'search.php?q=";alert&#40;"XSS"&#41;;&n=1093&i=410',
            'http://localhost/text.php/">alert&#40;Gehackt!&#41;;&lt;/form&gt;&lt;form action="/...',
        ];
    }

    private function _resultIsFoundArray()
    {
        return [
            false,
            false,
            false,
            false,
            false,
            true,
            false,
            false,
            false,
            false,
            true,
            false,
            false,
            false,
            false,
            true,
            true
        ];
    }

    function testIsXssFoundArray()
    {
        $xss = $this->security->xss();
        if (!$xss->isCompatible()) {
            $this->markTestSkipped('Arrays not supported for PHP 5.4');
        }
        $testArray = $this->_testArray();
        $result = $this->_resultIsFoundArray();

        foreach ($testArray as $key => $string) {
            $xss->cleanUrl($string);
            $this->assertSame($xss->isXssFound(), $result[$key]);
        }
    }

    function testArray()
    {
        $xss = $this->security->xss();
        if (!$xss->isCompatible()) {
            $this->markTestSkipped('Arrays not supported for PHP 5.4');
        }
        $testArray = $this->_testArray();
        $resultArray = $this->_resultArray();

        $this->assertSame($resultArray, $xss->cleanUrl($testArray));
    }

    public function testFromJsXss()
    {
        // 兼容各种奇葩输入
        $this->assertSame('', $this->security->xss()->cleanUrl(''));
        $this->assertNull($this->security->xss()->cleanUrl(null));
        $this->assertSame(123, $this->security->xss()->cleanUrl(123));
        $this->assertSame('{a: 1111}', $this->security->xss()->cleanUrl('{a: 1111}'));
        // 清除不可见字符
        if (!$this->security->xss()->isCompatible()) {
            $this->assertSame("a  b", $this->security->xss()->cleanUrl("a\u0000\u0001\u0002\u0003\r\n b"));
        }
        // 过滤不在白名单的标签
        $this->assertSame('<b>abcd</b>', $this->security->xss()->cleanUrl('<b>abcd</b>'));
        $this->assertSame('<o>abcd</o>', $this->security->xss()->cleanUrl('<o>abcd</o>'));
        $this->assertSame('<b>abcd</o>', $this->security->xss()->cleanUrl('<b>abcd</o>'));
        $this->assertSame('<b><o>abcd</b></o>', $this->security->xss()->cleanUrl('<b><o>abcd</b></o>'));
        $this->assertSame('<hr>', $this->security->xss()->cleanUrl('<hr>'));
        $this->assertSame('<xss>', $this->security->xss()->cleanUrl('<xss>'));
        $this->assertSame('<xss o="x">', $this->security->xss()->cleanUrl('<xss o="x">'));
        $this->assertSame('<a><b>c</b></a>', $this->security->xss()->cleanUrl('<a><b>c</b></a>'));
        $this->assertSame('<a><c>b</c></a>', $this->security->xss()->cleanUrl('<a><c>b</c></a>'));
        // 过滤不是标签的<>
        $this->assertSame('<>>', $this->security->xss()->cleanUrl('<>>'));
        $this->assertSame("''", $this->security->xss()->cleanUrl("'<scri' + 'pt>'"));
        $this->assertSame("''", $this->security->xss()->cleanUrl("'<script' + '>'"));
        $this->assertSame('<<a>b>', $this->security->xss()->cleanUrl('<<a>b>'));
        $this->assertSame('<<<a>>b</a><x>', $this->security->xss()->cleanUrl('<<<a>>b</a><x>'));
        // 过滤不在白名单中的属性
        $this->assertSame('<a oo="1" xx="2" title="3">yy</a>', $this->security->xss()->cleanUrl('<a oo="1" xx="2" title="3">yy</a>'));
        $this->assertSame('<a >pp</a>', $this->security->xss()->cleanUrl('<a title xx oo>pp</a>'));
        $this->assertSame('<a >pp</a>', $this->security->xss()->cleanUrl('<a title "">pp</a>'));
        $this->assertSame('<a t="">', $this->security->xss()->cleanUrl('<a t="">'));
        // 属性内的特殊字符
        $this->assertSame('<a >>">', $this->security->xss()->cleanUrl('<a title="\'<<>>">'));
        $this->assertSame('<a title="">', $this->security->xss()->cleanUrl('<a title=""">'));
        $this->assertSame('<a title="oo">', $this->security->xss()->cleanUrl('<a h=title="oo">'));
        $this->assertSame('<a  title="oo">', $this->security->xss()->cleanUrl('<a h= title="oo">'));
        $this->assertSame('<a title="alert&#40;/xss/&#41;">', $this->security->xss()->cleanUrl('<a title="javascript&colon;alert(/xss/)">'));
        // 自动将属性值的单引号转为双引号
        $this->assertSame('<a title=\'abcd\'>', $this->security->xss()->cleanUrl('<a title=\'abcd\'>'));
        $this->assertSame('<a title=\'"\'>', $this->security->xss()->cleanUrl('<a title=\'"\'>'));
        // 没有双引号括起来的属性值
        $this->assertSame('<a >', $this->security->xss()->cleanUrl('<a title=home>'));
        $this->assertSame('<a >', $this->security->xss()->cleanUrl('<a title=abc("d")>'));
        $this->assertSame('<a >', $this->security->xss()->cleanUrl('<a title=abc(\'d\')>'));
        // 单个闭合标签
        $this->assertSame('<img />', $this->security->xss()->cleanUrl('<img src/>'));
        $this->assertSame('<img  />', $this->security->xss()->cleanUrl('<img src />'));
        $this->assertSame('<img />', $this->security->xss()->cleanUrl('<img src//>'));
        $this->assertSame('<br />', $this->security->xss()->cleanUrl('<br />'));
        $this->assertSame('<br/>', $this->security->xss()->cleanUrl('<br/>'));
        // 畸形属性格式
        $this->assertSame('<a target = "_blank" title ="bbb">', $this->security->xss()->cleanUrl('<a target = "_blank" title ="bbb">'));
        $this->assertSame('<a target = \'_blank\' title =\'bbb\'>', $this->security->xss()->cleanUrl("<a target = '_blank' title ='bbb'>"));
        $this->assertSame('<a >', $this->security->xss()->cleanUrl('<a target=_blank title=bbb>'));
        $this->assertSame('<a target = "_blank"  title =  "bbb">', $this->security->xss()->cleanUrl('<a target = "_blank" title =  title =  "bbb">'));
        $this->assertSame('<a target = " _blank "  title =  "bbb">', $this->security->xss()->cleanUrl('<a target = " _blank " title =  title =  "bbb">'));
        $this->assertSame('<a   title =  "bbb">', $this->security->xss()->cleanUrl('<a target = _blank title =  title =  "bbb">'));
        $this->assertSame('<a   title =  "bbb">', $this->security->xss()->cleanUrl('<a target = ' . 0x42 . '_blank' . 0x42 . ' title =  title =  "bbb">'));
        $this->assertSame('<img  title="xxx">', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title="xxx">'));
        $this->assertSame('<img >', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title=xxx>'));
        $this->assertSame('<img >', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title= xxx>'));
        $this->assertSame('<img  title= "xxx">', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title= "xxx">'));
        $this->assertSame('<img  title= \'xxx\'>', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title= \'xxx\'>'));
        $this->assertSame('<img  title = \'xxx\'>', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title = \'xxx\'>'));
        $this->assertSame('<img  title= "xxx" alt="yyy">', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title= "xxx" no=yes alt="yyy">'));
        $this->assertSame('<img  title= "xxx" alt="\'yyy\'">', $this->security->xss()->cleanUrl('<img width = 100    height     =200 title= "xxx" no=yes alt="\'yyy\'">'));
        // 过滤所有标签
        $this->assertSame('<a title="xx">bb</a>', $this->security->xss()->cleanUrl('<a title="xx">bb</a>'));
        $this->assertSame('<hr>', $this->security->xss()->cleanUrl('<hr>'));
        // 增加白名单标签及属性
        $this->assertSame('<ooxx yy="ok" cc="no">uu</ooxx>', $this->security->xss()->cleanUrl('<ooxx yy="ok" cc="no">uu</ooxx>'));
        $this->assertSame('>">\'>alert&#40;String.fromCharCode(88,83,83&#41;)', $this->security->xss()->cleanUrl('></SCRIPT>">\'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>'));
        $this->assertSame(';!--"<XSS>=', $this->security->xss()->cleanUrl(';!--"<XSS>=&{()}'));
        $this->assertSame('', $this->security->xss()->cleanUrl('<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>'));
        $this->assertSame('<IMG src="">', $this->security->xss()->cleanUrl('<IMG SRC="javascript:alert(\'XSS\');">'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=javascript:alert(\'XSS\')>'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=JaVaScRiPt:alert(\'XSS\')>'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=`javascript:alert("RSnake says, \'XSS\'")`>'));
        $this->assertSame('<IMG """><>>', $this->security->xss()->cleanUrl('<IMG """><SCRIPT>alert("XSS")</SCRIPT>">'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>'));
        $this->assertSame('<IMG src="">', $this->security->xss()->cleanUrl('<IMG SRC="jav ascript:alert(\'XSS\');">'));
        $this->assertSame('<IMG src="">', $this->security->xss()->cleanUrl('<IMG SRC="jav&#x09;ascript:alert(\'XSS\');">'));
        $this->assertSame('<IMG src="">', $this->security->xss()->cleanUrl('<IMG SRC="jav\nascript:alert(\'XSS\');">'));
        $this->assertSame('<IMG >', $this->security->xss()->cleanUrl('<IMG SRC=java\0script:alert(\"XSS\")>'));
        $this->assertSame('<IMG src="">', $this->security->xss()->cleanUrl('<IMG SRC=" &#14;  javascript:alert(\'XSS\');">'));
        $this->assertSame('', $this->security->xss()->cleanUrl('<SCRIPT/XSS SRC="http://ha.ckers.org/xss.js"></SCRIPT>'));
        $this->assertSame('&lt;BODY !#$%&()*~+-_.,:;?@[/|\]^`=alert&#40;"XSS"&#41;&gt;', $this->security->xss()->cleanUrl('<BODY onload!#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>'));
        $this->assertSame('&lt;BODY  !#$%&()*~+-_.,:;?@[/|\]^`=alert&#40;"XSS"&#41;&gt;', $this->security->xss()->cleanUrl('<BODY onload !#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>'));
        $this->assertSame('&lt;alert&#40;"XSS"&#41;;//&lt;', $this->security->xss()->cleanUrl('<<SCRIPT>alert("XSS");//<</SCRIPT>'));
        $this->assertSame('', $this->security->xss()->cleanUrl('<SCRIPT SRC=http://ha.ckers.org/xss.js?< B >'));
        $this->assertSame('&lt;SCRIPT SRC=//ha.ckers.org/.j', $this->security->xss()->cleanUrl('<SCRIPT SRC=//ha.ckers.org/.j'));
        $this->assertSame('<IMG src=""', $this->security->xss()->cleanUrl('<IMG SRC="javascript:alert(\'XSS\')"'));
        $this->assertSame('&lt;iframe src=http://ha.ckers.org/scriptlet.html &lt;', $this->security->xss()->cleanUrl('<iframe src=http://ha.ckers.org/scriptlet.html <'));
        // 过滤 javascript:
        $this->assertSame('<a >', $this->security->xss()->cleanUrl('<a style="url(\'javascript:alert(1)\')">'));
        $this->assertSame('<td background="url(\'alert&#40;1&#41;\')">', $this->security->xss()->cleanUrl('<td background="url(\'javascript:alert(1)\')">'));
        // 过滤 style
        $this->assertSame('<DIV >', $this->security->xss()->cleanUrl('<DIV STYLE="width: \nexpression(alert(1));">'));
        $this->assertSame('<DIV >', $this->security->xss()->cleanUrl('<DIV STYLE="width: \n expressionexpression((alert(1));">'));
        // 不正常的url
        $this->assertSame('<DIV >', $this->security->xss()->cleanUrl('<DIV STYLE="background:\n url (javascript:ooxx);">'));
        $this->assertSame('<DIV >', $this->security->xss()->cleanUrl('<DIV STYLE="background:url (javascript:ooxx);">'));
        // 正常的url
        $this->assertSame('<DIV >', $this->security->xss()->cleanUrl('<DIV STYLE="background: url (ooxx);">'));
        $this->assertSame('<IMG src="">', $this->security->xss()->cleanUrl('<IMG SRC=\'vbscript:msgbox("XSS")\'>'));
        $this->assertSame('<IMG SRC="[code]">', $this->security->xss()->cleanUrl('<IMG SRC="livescript:[code]">'));
        $this->assertSame('<IMG SRC="[code]">', $this->security->xss()->cleanUrl('<IMG SRC="mocha:[code]">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="javas/**/cript:alert(\'XSS\');">'));
        $this->assertSame('<a href="test">', $this->security->xss()->cleanUrl('<a href="javascript:test">'));
        $this->assertSame('<a href="/javascript/a">', $this->security->xss()->cleanUrl('<a href="/javascript/a">'));
        $this->assertSame('<a href="/javascript/a">', $this->security->xss()->cleanUrl('<a href="/javascript/a">'));
        $this->assertSame('<a href="http://aa.com">', $this->security->xss()->cleanUrl('<a href="http://aa.com">'));
        $this->assertSame('<a href="https://aa.com">', $this->security->xss()->cleanUrl('<a href="https://aa.com">'));
        $this->assertSame('<a href="mailto:me@ucdok.com">', $this->security->xss()->cleanUrl('<a href="mailto:me@ucdok.com">'));
        $this->assertSame('<a href="#hello">', $this->security->xss()->cleanUrl('<a href="#hello">'));
        $this->assertSame('<a href="other">', $this->security->xss()->cleanUrl('<a href="other">'));
        // 这个暂时不知道怎么处理
        //self::assertSame($this->security->xss()->cleanUrl('¼script¾alert(¢XSS¢)¼/script¾'), '');
        $this->assertSame('&lt;!--[if gte IE 4]>alert&#40;\'XSS\'&#41;;<![endif]--&gt; END', $this->security->xss()->cleanUrl('<!--[if gte IE 4]><SCRIPT>alert(\'XSS\');</SCRIPT><![endif]--> END'));
        $this->assertSame('&lt;!--[if gte IE 4]>alert&#40;\'XSS\'&#41;;<![endif]--&gt; END', $this->security->xss()->cleanUrl('<!--[if gte IE 4]><SCRIPT >alert(\'XSS\');</SCRIPT><![endif]--> END'));
        // HTML5新增实体编码 冒号&colon; 换行&NewLine;
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="javascript&colon;alert(/xss/)">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="javascript&colonalert(/xss/)">'));
        $this->assertSame('<a href="a&NewLine;b">', $this->security->xss()->cleanUrl('<a href="a&NewLine;b">'));
        $this->assertSame('<a href="a&NewLineb">', $this->security->xss()->cleanUrl('<a href="a&NewLineb">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="javasc&NewLine;ript&colon;alert(1)">'));
        // data URI 协议过滤
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="data:">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="d a t a : ">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="data: html/text;">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="data:html/text;">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="data:html /text;">'));
        $this->assertSame('<a href="">', $this->security->xss()->cleanUrl('<a href="data: image/text;">'));
        $this->assertSame('<img src="">', $this->security->xss()->cleanUrl('<img src="data: aaa/text;">'));
        $this->assertSame('<img src="">', $this->security->xss()->cleanUrl('<img src="data:image/png; base64; ofdkofiodiofl">'));
        $this->assertSame('<img src="PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K">', $this->security->xss()->cleanUrl('<img src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K">'));
        // HTML备注处理
        $this->assertSame('&lt;!--                               --&gt;', $this->security->xss()->cleanUrl('<!--                               -->'));
        $this->assertSame('&lt;!--      a           --&gt;', $this->security->xss()->cleanUrl('<!--      a           -->'));
        $this->assertSame('&lt;!--sa       --&gt;ss', $this->security->xss()->cleanUrl('<!--sa       -->ss'));
        $this->assertSame('&lt;!--                               ', $this->security->xss()->cleanUrl('<!--                               '));
    }

}
