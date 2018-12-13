# Web SDK

#### 目录

- [文档说明](#文档说明)
- [业务流程](#业务流程)
- [接入点](#接入点)
- [接口列表](#接口列表)
- [场景](#场景)

## 文档说明

本文阅读对象技术架构师、研发工程师、测试工程师、系统运维工程师等。

## 业务流程

步骤1：用户在【商户APP】中选择商品，提交订单，选择BizPay，并向【商户后端】提交数据。

步骤2：【商户后端】收到用户支付单，生成本地订单，调用BizPay统一下单接口。参见[统一下单API](#统一下单API)。

步骤3：统一下单接口返回正常的prepay_id，再按签名规范重新生成签名后，将数据返回给【商户APP】。

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
|开发者/系统服务商应用ID|app_id |是  |string |系统分配的APPID|`7`|
|应用KEY|app_key |是  |string | 系统分配的APPKEY    |`67fbdd4afb875caa2a3001fc021bbfa2`|
|随机字符串|nonce     |是  |string | 随机字符串，最长32字符    |`123`|
|签名|sign     |是  |string | 签名字符串，参见[签名算法](#签名算法)|`7DAF1161A2B66087D511C3D6D1AB054A`|
|商户订单号|out_trade_no     |是  |string |商户应用自身的订单号，每个APPID下唯一，最长32字符 |`1234567`|
|备注|remark     |是  |string | 备注，显示在支付框，最长128字符    |`商品`|
|交易金额|amount|是|double|订单支付的货币数量|`0.003`|
|交易方式|currencies|是|string|用户可选的交易货币，参见[货币列表](#货币列表)|`1,2,3`|
|交易类型|type|是|string|amount的单位，可选值：`native`货币值、`usd`美元值|`native`|
|通知地址|notify_url|是|string|支付回调URL，不可带GET参数，参见 [成功回调数据](#的) |`https://api.my.me/v1/pay/callback`|
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
        "app_id": 1000,
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
