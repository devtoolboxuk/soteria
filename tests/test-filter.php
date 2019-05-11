<?php

namespace devtoolboxuk\soteria;

use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    private $security;
    private $filter;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->security = new SoteriaService();
        $this->filter = $this->security->filter();
    }

    protected $testString = 'Test String';
    protected $testEmail = 'test@test.local';
    protected $testUrl = 'https://www.google.com';

    protected $latinAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    protected $numbers = '0123456789';
    protected $specialCharactersA = '!"£$%^&*()_+{}:@~<>?¬|';
    protected $specialCharactersB = "\`-=[];'#,./`";
    protected $germanSpecialCharacters = "ÄÖÜẞäöüß";
    protected $frenchSpecialCharacters = "âàäçéèêëîïôùûüœ";
    protected $dutchSpecialCharacters = "áéíóúàèëïöüĳÁÉÍÓÚÀÈËÏÖÜĲ";
    protected $spanishSpecialCharacters = "áéíóúñü¿¡";
    protected $scandinavianSpecialCharactersA = "æÆäÄøØöÖåÅ";
    protected $scandinavianSpecialCharactersB = "ÅåÄäÖöŠšŽž";
    protected $irishSpecialCharacters = "áíéóú";
    protected $polishSpecialCharactersA = "AĄBCĆDEĘFGHIJKLŁMNŃOÓPRSŚTUWYZŹŻ";
    protected $polishSpecialCharactersB = "aąbcćdeęfghijklłmnńoóprsśtuwyzźż";
    protected $cyrillicCharactersA = "Аа Бб Вв Гг Дд Ее Жж Зз Ии Йй Кк Лл Мм Нн";
    protected $cyrillicCharactersB = "Оо Пп	Рр Сс Тт Уу Фф Хх Цц Чч Шш Щщ Ьь Юю Яя";
    protected $arabic = "غ ظ ض ذ خ ث ت ش ر ق ص ف ع س ن م ل ك ي ط ح ز و ه د ج ب ا";
    protected $chineseTraditionalA = "電 買 車 紅 無 東 馬 風 時 鳥 語 頭 魚 園 長 島 愛 紙 書 見 假 佛 德 拜 黑 冰 兔 妒 每 壤 步";
    protected $chineseTraditionalB = "巢 惠 鞋 莓 圓 聽 實 證 龍 賣 龜 藝 戰 繩 關 鐵 圖 團 轉 廣 惡 豐 腦 雜 壓 雞 價 樂 氣 廳 發";
    protected $chineseTraditionalC = "勞 劍 歲 權 燒 贊 兩 譯 觀 營 處 齒 驛 櫻 產 藥 讀 顏 聲 學 體 點 麥 蟲 舊 會 萬 盜 寶 國 醫";
    protected $chineseTraditionalD = "雙 晝 觸 來 畫 黃 區";
    protected $chineseSimplifiedA = "电 买 车 红 无 东 马 风 时 鸟 语 头 鱼 园 长 岛 爱 纸 书 见 假 佛 德 拜 黑 冰 兔 妒 每 壤";
    protected $chineseSimplifiedB = "步 巢 惠 鞋 莓 圆 听 实 证 龙 卖 龟 艺 战 绳 关 铁 图 团 转 广 恶 丰 脑 杂 压 鸡 价 乐 气";
    protected $chineseSimplifiedC = "厅 发 劳 剑 岁 权 烧 赞 两 译 观 营 处 齿 驿 樱 产 药 读 颜 声 学 体 点 麦 虫 旧 会 万 盗";
    protected $chineseSimplifiedD = "宝 国 医 双 昼 触 来 画 黄 区";

    protected $testArray = [];

    function testEmail()
    {
        $this->assertSame($this->testEmail, $this->filter->email($this->testEmail));
        $this->assertNotEquals($this->testString, $this->filter->email($this->testEmail));
        $this->assertFalse($this->filter->email($this->testString));
    }

    function testInt()
    {
        $this->assertSame($this->numbers, $this->filter->int($this->numbers));
        $this->assertNotEquals($this->testString, $this->filter->email($this->numbers));
        $this->assertFalse($this->filter->int($this->testString));
    }
}
