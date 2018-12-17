# BizPaySDK-Docs
### 一．接入SDK

将BizPaySDK.aar文件拖入到项目libs文件夹当中：  
![Image Url](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/AndroidSDK/1.png)   
  
在Project的build.gradle中加入下列代码：  
```
allprojects {
    repositories {
        flatDir {
            dirs 'libs'
        }
    }
}
```

在Module的build.gradle的dependencies中添加下列依赖：  
``` 
implementation name: 'BizPaySDK', ext: 'aar'
```

执行完以上步骤后，同步一下项目，就成功将BizPaySDK导入项目中了。


### 二．发起支付
创建一个Bizpay对象，传入```context,app_key```，使用里面的**toBizpay(prepay\_id,pay\_url)**方法即可发起支付。  
注：```pay_url```非必需。示意图：      
![Image Url](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/AndroidSDK/2.png)   

### 三．支付回调
支付的信息会回调到开发者APP中的一个**Activity**中，该Activity需要实现**BizpayEventAPI**接口，并在对应的**manifest.xml**中申明该Activity。  
申明要求示例如下：
![Image Url](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/AndroidSDK/3.png)   
注：Activity的名字可以随意定义，但需要注意scheme字段为字符串"bizpay"直接拼接上开发者应用的app_key,否则回调将会失败。  
代码：
```
<activity android:name=".activity.BizPayActivity">
            <intent-filter>
                <action android:name="android.intent.action.VIEW" />

                <category android:name="android.intent.category.DEFAULT" />
                <category android:name="android.intent.category.BROWSABLE" />

                <data android:scheme="bizpayapp_key" />
            </intent-filter>
</activity>
```


### 四．处理支付回调 

在回调的Activity中处理支付回调，示例：
![Image Url](https://raw.githubusercontent.com/CrazyTeaFs/BizPaySDK-Docs/master/AndroidSDK/4.png)   
状态码：  
0：支付失败和支付取消  
1：支付成功  
2：空的prepay\_id和url   
3：当前设备没有安装Bizpay APP
