# Web SDK

#### 目录

- [文档说明](#文档说明)
- [业务流程](#业务流程)
- [接入点](#接入点)
- [接口列表](#接口列表)
- [签名算法](#签名算法)
- [货币列表](#货币列表)
- [SDK](#SDK)
- [场景](#场景)

## 文档说明

本文阅读对象技术架构师、研发工程师、测试工程师、系统运维工程师等。

## 业务流程

步骤1：用户在【商户APP】中选择商品，提交订单，选择BizPay，并向【商户后端】提交数据。

步骤2：【商户后端】收到用户支付单，生成本地订单，调用BizPay统一下单接口。参见[统一下单API](#统一下单API)。

步骤3：统一下单接口返回正常的prepay_id，将相关数据返回给【商户APP】。

步骤4：【商户APP】调起微信支付。参见[安卓SDK](https://github.com/CrazyTeaFs/BizPaySDK-Docs/tree/master/AndroidSDK)、[iOS SDK](https://github.com/CrazyTeaFs/BizPaySDK-Docs/tree/master/iOSSDK)、

步骤5：用户在调起的BizPay客户端中完成支付，【商户后端】则会接收支付通知。

步骤6：【商户后端】查询支付结果。

## 接入点

接入点是【商户后端】与【BizPay后端】的接口，一般情况下不会替换。

当前接入点：

```
https://api.51bizpay.com
```

## 接口列表

我们目前提供【统一下单API】、【快捷支付API】。

### 统一下单API

#### 请求URL

- `/api/v1/pay/unifiedorder`

#### 请求方式

- `POST`

#### 请求参数

|参数名|变量名|必选|类型|说明|示列|
|:------|:------|:---|:-----|-----|----|
|开发者应用ID|app_id |是  |string |系统分配的APPID|`10001`|
|应用KEY|app_key |是  |string | 系统分配的APPKEY    |`67fbdd4afb875caa2a3001fc021bbfa2`|
|随机字符串|nonce     |是  |string | 随机字符串，最长32字符    |`123`|
|签名|sign     |是  |string | 签名字符串，参见[签名算法](#签名算法)|`7DAF1161A2B66087D511C3D6D1AB054A`|
|商户订单号|out_trade_no     |是  |string |商户应用自身的订单号，每个APPID下唯一，最长32字符 |`1234567`|
|备注|remark     |是  |string | 备注，显示在支付框，最长128字符    |`商品`|
|交易金额|amount|是|double|订单支付的货币数量|`0.003`|
|交易方式|currencies|是|string|用户可选的交易货币，参见[货币列表](#货币列表)|`1,2,3`|
|交易类型|type|是|string|amount的单位，可选值：`native`货币值、`usd`美元值|`native`|
|通知地址|notify_url|是|string|支付回调URL，不可带GET参数，参见 [成功回调数据](#成功回调数据) |`https://api.my.me/v1/pay/callback`|
|附加数据|attach|否|string|商户自定义数据，支付成功回调原样返回，最长128字符|`abcdef`|

##### 说明

- type设置为usd表示amount的单位是美元，系统将自动让用户支付相应的货币数量

#### 返回结果

当返回code为200时表示成功

```json
{
    "code": 200,
    "msg": "ok",
    "data": {
        "app_id": 10001,
        "app_key": "67fbdd4afb875caa2a3001fc021bbfa2",
        "nonce": "4ff6dcf23d1e9f84cd6ca7204d94d7f6",
        "prepay_id": "47aad51dd02f1cf689538866a81fcc677a3829c0",
        "pay_url": "https://api.51bizpay.com/4Z3REW4R29",
        "sign": "35EDA84089C41777CA1F6B41A143024D"
    }
}
```

响应中

|code值|msg值|
|:------|:------|
|200|ok|
|400|参数错误|
|401|APP不存在|
|402|APP已关闭状态|
|403|签名错误|
|410|该订单已被处理|
|420|支付类型或者金额设置错误|
|500|订单未创建成功|

返回其他错误，见msg提示。

### 快捷支付API

#### 请求URL

- `/api/v1/pay/quickpay`

#### 请求方式

- `POST`

#### 请求参数

|参数名|变量名|必选|类型|说明|示列|
|:------|:------|:---|:-----|-----|----|
|开发者/系统服务商应用ID|app_id |是|string |系统分配的APPID|`10001`|
|应用KEY|app_key |是|string | 系统分配的APPKEY    |`67fbdd4afb875caa2a3001fc021bbfa2`|
|子商户ID|sub_app_id |否|string |如果设置了这个值，表示是系统服务商的子商户|`85`|
|付款码|authcode|是|string|68开头的18到19位数字字符串，扫/输入用户付款码得到|`681234567890123456`|
|随机字符串|nonce|是  |string | 随机字符串，最长32字符    |`123`|
|签名|sign|是|string | 签名字符串，参见[签名算法](#签名算法)|`7DAF1161A2B66087D511C3D6D1AB054A`|
|商户订单号|out_trade_no|是|string |商户应用自身的订单号，每个APPID下唯一，最长32字符 |`1234567`|
|备注|remark|是|string|备注，显示在支付框，最长128字符    |`商品`|
|交易金额|amount|是|double|订单支付的货币数量|`0.003`|
|交易方式|currencies|是|string|用户可选的交易货币，参见[货币列表](#货币列表)|`1,2,3`|
|交易类型|type|是|string|amount的单位，可选值：`native`货币值、`usd`美元值|`native`|
|通知地址|notify_url|否|string|**快捷支付可不设置此参数。**支付回调URL，不可带GET参数，参见 [成功回调数据](#的) |`https://api.my.me/v1/pay/callback`|
|附加数据|attach|否|string|商户自定义数据，支付成功回调原样返回，最长128字符|`abcdef`|

##### 说明

- 在快捷支付下currencies只可指定一种。

#### 返回结果

当返回code为200时表示成功，且用户已扣款。

```json
{
    "code": 200,
    "msg": "ok",
    "data": {
        "app_id": 10001,
        "app_key": "67fbdd4afb875caa2a3001fc021bbfa2",
        "nonce": "4ff6dcf23d1e9f84cd6ca7204d94d7f6",
        "prepay_id": "47aad51dd02f1cf689538866a81fcc677a3829c0",
        "pay_url": "https://api.51bizpay.com/4Z3REW4R29",
        "sign": "35EDA84089C41777CA1F6B41A143024D"
    }
}
```

## 成功回调数据

用户支付成功后，系统会自动以POST方式请求notify_url，请保证请求能在60秒内完成。

有事件发生时，将发出以下数据。首先，请验证签名是否有效。

确认支付数据无误，请输出纯字符串success，表示该订单已完成支付。

```json
{
  "app_id": "7",
  "app_key": "67fbdd4afb875caa2a3001fc021bbfa2",
  "out_trade_no": "100000000002",
  "amount": "1.000000000000000000",
  "status": "success",
  "nonce": "881cb7ef01e0c021c78f491fdcc40c71",
  "currency": "1",
  "sign": "8B434B61028CE10043BA472B3E36EF58"
}
```
> 以上数据以 form-data 获得。php 中用`$_POST`获取。

## 签名算法

- 步骤1

构造需要签名的数据为键值对数组，PHP代码如下：
```php
$data = [
    'app_id' => $appid,
    'app_key' => $appkey,
    'nonce' => md5(microtime()),
    'others' => '',
];
```

- 步骤2

排序并构造为query字符串，按照`key`进行升序排序，并生成http_query，类似`app_id=10001&app_key=67fbdd4afb875caa2a3001fc021bbfa2&nonce=1001`，将`APPSecret`连接至最后，PHP代码如下：

```php
function buildQuery($data) {
    $temp = [];
    foreach ($data as $key => $value) {
        $temp[] = "{$key}={$value}";
    }
    return implode($temp, '&');
}
ksort($data);
$query = buildQuery($data) . "&secret={$appsecret}";
```

- 步骤3

对query字符串进行hash运算并转换为大写字母，目前只支持`md5`算法。
```php
$sign = strtoupper(md5($query));
```

### 签证签名
验证签名要进行签名，得到数组后，将`sign`字段取出来，并删除掉数组中的`sign`，再对这个数组进行签名，和取出来的`sign`一致，则正确。

签名验证通过后，必须严格按照如下的描述校验通知参数的合法性：

1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；

2、验证app_id是否为该商户本身设定值。

上述1、2有任何一个验证不通过，则表明同步校验结果是无效的，只有全部验证通过后，才可以认定买家付款成功。


## 货币列表

目前BizPay支持以下货币

|ID|货币名|符号|
|:------|:------|:------|
|1|比特币|BTC|
|2|以太坊|ETH|
|3|泰达币|USDT|
|4|瑞波币|XRP|

以后增加的货币类型，都会填写在这里。

## SDK

目前我们提供了PHP调用参考，见本目录下的`Bizpay.php`，你需要修改命名空间和添加`GuzzleHttp`扩展。


- 初始化

```php
$client = new Bizpay([
    'appid' => '10001',
    'appkey' => '46577c6a53c920909b89fc64cd8f82f7',
    'appsecret' => '01829a123366ce210856897e9f9f635a',
    'http' => ['base_uri' => 'https://api.51bizpay.com'],
]));
```

- 使用统一下单

```php
$data = $client->unifiedorder([
    'out_trade_no' => $order->number,
    'remark' => '购买商品',
    'amount' => $amount,
    'currencies' => implode(array_column(Bizpay::$currencies, 'id'), ','),
    'notify_url' => 'https://my.website/api/callback',
    'type' => 'usd',
]);
if (!empty($data)) {
    // SUCCESS
}
```

- 使用快捷支付

```php
$data = $client->quickpay([
    'authcode' => $authcode,
    'out_trade_no' => $order->number,
    'remark' => '购买商品',
    'amount' => $amount,
    'currencies' => 1, // 让用户支付比特币
    'type' => 'usd',
]);
if (!empty($data)) {
    // SUCCESS
}
```

## 场景

#### Web支付

目前暂时不提供Web支付。

#### 二维码支付

商户生成本地订单后调用统一下单接口，将得到的`pay_url`，将`pay_url`生成二维码，引导用户使用BizPay客户端扫描此二维码进行支付。

#### APP支付

查看相应的平台SDK文档来了解APP支付

#### 刷卡支付

调用快捷支付API来使用刷卡支付
