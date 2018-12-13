//
//  BizPayRep.h
//  LSPlanet
//
//  Created by xby023 on 2018/12/13.
//  Copyright © 2018年 com.InZiqi. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface BizPayRep : NSObject

/**
 支付会话ID
 */
@property (nonatomic ,copy) NSString *prepay_id;

/**
 支付url
 */
@property (nonatomic ,copy) NSString *pay_url;

@end
