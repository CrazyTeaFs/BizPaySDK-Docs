# BizPaySDK-Docs
### 一．接入SDK

直接将BizPaySDK文件拖入到项目当中。
BizPaySDK包含两个文件 **BizPaySDK.a** 和 **Header**

![](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/iOSSDK/1.png)


### 二．配置白名单和URL Types

#### 1.配置白名单:
在plist文件添加**LSApplicationQueriesSchemes **字段，然后添加一个item,item为**“bizpay”**。（注意：如果写错将调起支付失败）

![](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/iOSSDK/2.png)

#### 2.配置URL Types:
在TARGETS  ->  Info  -> URL Types下添加一条新的URL Schemes. URL Schemes为**“BizPay + App Key”**，Identifier为 **“51BIZPAY”**。（注意：Identifier为写死成**“51BIZPAY”**）

![](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/iOSSDK/3.png)


### 三．初始化SDK

在AppDelegate添加方法

``` objc
  - (BOOL )application:(UIApplication *)application  openURL:(nonnull NSURL *)url options:(nonnull NSDictionary<UIApplicationOpenURLOptionsKey,id> *)options{
    return [[BizPayManager shareManager] handleOpenURL:url];
}
```

![](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/iOSSDK/4.png)


### 四．调起支付

在获取服务器返回支付数据后开始发起支付

``` objc
  //配置支付数据
    BizPayRep *rep = [[BizPayRep alloc]init];
    rep.prepay_id = prepay_id;
    rep.pay_url = pay_url;
                    
  //发起支付
    [[BizPayManager shareManager] sendRep:rep Result:^(BOOL success) {
       [LSManager shareManager].isPay = success;
    }];
```

![](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/iOSSDK/5.png)


### 五．处理支付回调 

在调起支付后获取支付状态

``` objc
  [BizPayManager shareManager].payResultBlock = ^(BizPayResult     result) {
        if (result == BizPayResultSuccess) {
            //支付成功
        }else{
            //支付失败
        }
  };
```

![](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/iOSSDK/6.png)



