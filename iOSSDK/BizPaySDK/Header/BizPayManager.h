//
//  BizPayManager.h
//  LSPlanet
//
//  Created by xby023 on 2018/12/13.
//  Copyright © 2018年 com.InZiqi. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "BizPayRep.h"


typedef NS_ENUM(NSUInteger ,BizPayResult){
    BizPayResultSuccess,
    BizPayResultFailure
};

@interface BizPayManager : NSObject

/**
 初始化BizPayManager单利

 @return 返回BizPayManager;
 */
+ (BizPayManager *)shareManager;

/**
 发起支付

 @param rep 参数模型
 @param result 返回发起支付是否成功结果
 */
- (void)sendRep:(BizPayRep *)rep Result:(void(^ __nullable)(BOOL success))result;


/**
 支付是否成功回来调
 */
@property (nonatomic ,copy) void(^payResultBlock)(BizPayResult result);


/**
 处理回调

 @param url 回调url
 @return 返回结果
 */
- (BOOL)handleOpenURL:(NSURL *)url;


@end
