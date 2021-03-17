<?php


namespace Pudongping\Weather;

use GuzzleHttp\Client;
use Pudongping\Weather\Exceptions\HttpException;
use Pudongping\Weather\Exceptions\InvalidArgumentException;


class Weather
{

    protected $key;
    protected $guzzleOptions = [];

    protected $baseUrl = 'https://restapi.amap.com/v3/weather/weatherInfo';
    protected $allowType = ['base', 'all'];
    protected $allowFormat = ['xml', 'json'];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * 获取实时天气
     *
     * @param string $city  城市名称或者城市的 adcode
     * @param string $format  返回格式： json 、 xml
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLiveWeather(string $city, string $format = 'json')
    {
        return $this->getWeather($city, 'base', $format);
    }

    /**
     * 获取天气预报
     *
     * @param string $city  城市名称或者城市的 adcode
     * @param string $format  返回格式： json 、 xml
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getForecastsWeather(string $city, string $format = 'json')
    {
        return $this->getWeather($city, 'all', $format);
    }

    /**
     * 获取最近 4 天的天气预报和当天的实时天气
     *
     * @param string $city  城市名称或者城市的 adcode
     * @param string $type 气象类型：base => 实况天气，all => 天气预报
     * @param string $format  返回格式： json 、 xml
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWeather(string $city, string $type = 'base', string $format = 'json')
    {

        if (! in_array(strtolower($format), $this->allowFormat)) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (! in_array(strtolower($type), $this->allowType)) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type
        ]);

        try {
            $response = $this->getHttpClient()->get($this->baseUrl, [
                'query' => $query
            ])->getBody()->getContents();

            return ('json' === $format) ? json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

}