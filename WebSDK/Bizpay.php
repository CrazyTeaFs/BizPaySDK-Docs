<?php

namespace App\Libs;

use GuzzleHttp\Client as GuzzleHttp;

/**
 * Bizpay Client
 */
class Bizpay
{
    public $appid = null;
    public $appkey = null;
    public $appsecret = null;

    public $client = null;

    public static $currencies = [
        ['id' => 1, 'name' => 'bitcoin'],
        ['id' => 2, 'name' => 'ethereum'],
        ['id' => 3, 'name' => 'tether'],
        ['id' => 4, 'name' => 'ripple'],
    ];

    /**
     * @param $options array
     */
    public function __construct(array $options = [])
    {
        // set app info
        $this->appid = $options['appid'];
        $this->appkey = $options['appkey'];
        $this->appsecret = $options['appsecret'];

        // GuzzleHttp Options
        $defaultOptions = [
            'base_uri' => 'http://api.51bizpay.com',
            'timeout' => 10,
            'verify' => false,
        ];
        if (isset($options['http']) && is_array($options['http'])) {
            $this->client = new GuzzleHttp(array_merge($defaultOptions, $options['http']));
        } else {
            $this->client = new GuzzleHttp($defaultOptions);
        }
    }

    /**
     * 统一下单
     */
    public function unifiedorder(array $options)
    {
        $defaultOptions = [
            'app_id' => $this->appid,
            'app_key' => $this->appkey,
            // 随机数
            'nonce' => uniqid('bizpay'),
            // 商户订单号
            'out_trade_no' => '',
            // 备注
            'remark' => '',
            // 交易数量，支持8位小数
            'amount' => '',
            // 支持的货币类型，参考货币类型
            'currencies' => '',
            // 交易回调URL
            'notify_url' => '',
            // 商户自定义数据
            'attach' => '',
            // 交易类型，native货币值、usd法币美元
            'type' => 'native',
        ];

        $options = array_merge($defaultOptions, $options);
        // 签名
        $options['sign'] = $this->sign($options);

        $data = $this->request('/api/v1/pay/unifiedorder', $options);
        // 验证签名
        if (!$this->validSign((array)$data)) {
            return null;
        }

        return $data;
    }

    /**
     * 扫码支付
     */
    public function quickpay(array $options)
    {
        $defaultOptions = [
            'app_id' => $this->appid,
            'app_key' => $this->appkey,
            // 随机数
            'nonce' => uniqid('bizpay'),
            // 商户订单号
            'out_trade_no' => '',
            // 备注
            'remark' => '',
            // 交易数量，支持8位小数
            'amount' => '',
            // 支持的货币类型，参考货币类型
            'currencies' => '',
            // 交易回调URL
            'notify_url' => '',
            // 商户自定义数据
            'attach' => '',
            // 交易类型，native货币值、usd法币美元
            'type' => 'native',
            // 用户支付码
            'authcode' => '',
        ];

        $options = array_merge($defaultOptions, $options);
        // 签名
        $options['sign'] = $this->sign($options);

        $data = $this->request('/api/v1/pay/quickpay', $options);
        // 验证签名
        if (!$this->validSign((array)$data)) {
            return null;
        }

        return $data;
    }

    /**
     * 签名
     * @return
     */
    public function sign(array $data, string $secret = '')
    {
    	ksort($data);
        if (empty($secret)) {
            $secret = $this->appsecret;
        }
        return strtoupper(md5($this->buildQuery($data) . "&secret={$secret}"));
    }

    /**
     * 验证签名
     * @return boolean
     */
    public function validSign(array $data, string $secret = '')
    {
    	if (!isset($data['sign'])) return false;
        $sign = $data['sign'];
        unset($data['sign']);
        return strtolower($sign) == strtolower($this->sign($data, $secret));
    }

    /**
     * 构建
     */
    private function buildQuery(array $data)
    {
        $temp = [];
        foreach ($data as $key => $value) {
            $temp[] = "{$key}={$value}";
        }
        return implode($temp, '&');
    }

    /**
     * Request
     */
    public function request($path, $params = [])
    {
        $data = [];
        if ($params) {
            $data['json'] = $params;
        }

        $res = $this->client->post($path, $data);
        $resobj = json_decode($res->getBody());
        if (empty($resobj)) {
            throw new \Exception("HTTP SERVER ERROR", 1);
        }
        if ($resobj->code == 200) {
            return $resobj->data;
        } else {
            throw new \Exception($resobj->msg, $resobj->code);
        }

        return null;
    }
}
