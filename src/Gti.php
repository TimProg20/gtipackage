<?php

namespace Shmel\GtiPackage;

class Gti
{
  
  /**
   * Invalid json format
   * 
   * @var string
   */
  private const INVALID_JSON_FORMAT = "Некорректный json";
  

  /**
   * Invalid parameter message
   * 
   * @var string
   */
  private const INVALID_PARAMETER = "Некорректный параметр";

  /**
   * Begin parameter not found
   * 
   * @var string
   */
  private const BEGIN_NOT_FOUND = "Параметр begin не найден";

  /**
   * End parameter not found
   * 
   * @var string
   */
  private const END_NOT_FOUND = "Параметр end не найден";
  
  /**
   * Parameter Not found message
   * 
   * @var string
   */
  private const PARAMETER_NOT_FOUND = "Параметр не найден";

  /**
   * Left criterion is more than right criterion message
   * 
   * @var string
   */
  private const LEFT_MORE_THAN_RIGHT = "Левая граница больше правой";

  /**
   * checks begin and end parameters in json data and user parameter
   *
   * @param string $parameter
   * @param string|NULL $begin
   * @param string|NULL $end
   * @return string|boolean
   */
  private function checkParameter(string $parameter, string|NULL $begin, string|NULL $end) : string|bool
  {
    if ($parameter == 'datetime')
    {
      return self::INVALID_PARAMETER;
    }

    if ($begin === NULL)
    {
      return self::BEGIN_NOT_FOUND;
    }

    if ($end === NULL)
    {
      return self::END_NOT_FOUND;
    }

    return true;
  }

  /**
   * Gets period when parameter is constantly increasing
   *
   * @param string $contentJson
   * @param string $parameter
   * @return array|string
   */
  public function getUpPeriods(string $contentJson, string $parameter) : array|string
  {
    
    $info = json_decode($contentJson, true);

    if ($info === NULL)
    {
      return self::INVALID_JSON_FORMAT;
    }

    $validation = $this->checkParameter($parameter, $info['begin'] ?? NULL, $info['end'] ?? NULL);

    if ($validation !== true) 
    {
      return $validation;
    }

    if (count($info['data']) == 0)
    {
      return [];
    }

    if (!array_key_exists($parameter, $info['data'][0])) 
    {
      return self::PARAMETER_NOT_FOUND;
    }

    $paramValue = $info['data'][0][$parameter];

    $intervals = [];

    $interval = [ 'begin' => $info['begin'] ];
    
    for($i = 1; $i < count($info['data']); $i++) {

      if (!array_key_exists($parameter, $info['data'][$i])) 
      {
        return self::PARAMETER_NOT_FOUND;
      }

      if ($paramValue > $info['data'][$i][$parameter]) 
      {

        $paramValue = $info['data'][$i][$parameter];

        $interval['end'] = $info['data'][$i-1]['datetime'];

        $intervals[] = $interval;

        $interval = [ 'begin' => $info['data'][$i]['datetime'] ];
      } else {

        $paramValue = $info['data'][$i][$parameter];
      }
    }

    $interval['end'] = $info['end'];

    $intervals[] = $interval;

    return $intervals;
  }

  /**
   * Gets period when parameter is constantly decreasing
   *
   * @param string $contentJson
   * @param string $parameter
   * @return array|string
   */
  public function getDownPeriods(string $contentJson, string $parameter) : array|string
  {

    $info =  json_decode($contentJson, true);

    $validation = $this->checkParameter($parameter, $info['begin'] ?? NULL, $info['end'] ?? NULL);

    if ($validation !== true) 
    {
      return $validation;
    }

    if (count($info['data']) == 0)
    {
      return [];
    }

    if (!array_key_exists($parameter, $info['data'][0])) 
    {
      return self::PARAMETER_NOT_FOUND;
    }

    $paramValue = $info['data'][0][$parameter];

    $intervals = [];

    $interval = [ 'begin' => $info['begin'] ];
    
    for($i = 1; $i < count($info['data']); $i++) {

      if (!array_key_exists($parameter, $info['data'][$i])) 
      {
        return self::PARAMETER_NOT_FOUND;
      }

      if ($paramValue < $info['data'][$i][$parameter]) 
      {

        $paramValue = $info['data'][$i][$parameter];

        $interval['end'] = $info['data'][$i-1]['datetime'];

        $intervals[] = $interval;

        $interval = [ 'begin' => $info['data'][$i]['datetime'] ];
      } else {

        $paramValue = $info['data'][$i][$parameter];
      }
    }

    $interval['end'] = $info['end'];

    $intervals[] = $interval;

    return $intervals;
  }

  /**
   * Gets period when parameter is less than the criterion
   *
   * @param string $contentJson
   * @param string $parameter
   * @param integer $criterion
   * @return array|string
   */
  public function getLessThanPeriods(string $contentJson, string $parameter, int $criterion) : array|string
  {

    $info =  json_decode($contentJson, true);

    $validation = $this->checkParameter($parameter, $info['begin'] ?? NULL, $info['end'] ?? NULL);

    if ($validation !== true) 
    {
      return $validation;
    }

    $intervals = [];
    
    $interval = [];

    $isIntervalEmpty = true;

    for($i = 0; $i < count($info['data']); $i++) {

      if (!array_key_exists($parameter, $info['data'][$i])) 
      {
        return self::PARAMETER_NOT_FOUND;
      }

      if ($info['data'][$i][$parameter] < $criterion)
      {

        if ($isIntervalEmpty)
        {
          $interval['begin'] = $info['data'][$i]['datetime'];
          $isIntervalEmpty = false;
        }
      } else {
        
        if (!$isIntervalEmpty)
        {

          $interval['end'] = $info['data'][$i-1]['datetime'];

          $intervals[] = $interval;
          $interval = [];

          $isIntervalEmpty = true;
        }
      }
    }

    if (!$isIntervalEmpty)
    {
  
      $interval['end'] = $info['end'];

      $intervals[] = $interval;
    }

    return $intervals;
  }

  /**
   * Gets period when parameter is more than the criterion
   *
   * @param string $contentJson
   * @param string $parameter
   * @param integer $criterion
   * @return array|string
   */
  public function getMoreThanPeriods(string $contentJson, string $parameter, int $criterion) : array|string
  {

    $info =  json_decode($contentJson, true);

    $validation = $this->checkParameter($parameter, $info['begin'] ?? NULL, $info['end'] ?? NULL);

    if ($validation !== true) 
    {
      return $validation;
    }

    $intervals = [];
    
    $interval = [];

    $isIntervalEmpty = true;

    for($i = 0; $i < count($info['data']); $i++) {

      if (!array_key_exists($parameter, $info['data'][$i])) 
      {
        return self::PARAMETER_NOT_FOUND;
      }

      if ($info['data'][$i][$parameter] > $criterion)
      {

        if ($isIntervalEmpty)
        {
          $interval['begin'] = $info['data'][$i]['datetime'];
          $isIntervalEmpty = false;
        }
      } else {
        
        if (!$isIntervalEmpty)
        {

          $interval['end'] = $info['data'][$i-1]['datetime'];

          $intervals[] = $interval;
          $interval = [];

          $isIntervalEmpty = true;
        }
      }
    }

    if (!$isIntervalEmpty)
    {
  
      $interval['end'] = $info['end'];

      $intervals[] = $interval;
    }

    return $intervals;
  }

  /**
   * Gets period when parameter is between the criteria
   *
   * @param string $contentJson
   * @param string $parameter
   * @param integer $leftCriterion
   * @param integer $rightCriterion
   * @return array|string
   */
  public function getBetweenPeriods(string $contentJson, string $parameter, int $leftCriterion, int $rightCriterion) : array|string
  {

    if ($leftCriterion > $rightCriterion)
    {
      return self::LEFT_MORE_THAN_RIGHT;
    }

    $info =  json_decode($contentJson, true);

    $validation = $this->checkParameter($parameter, $info['begin'] ?? NULL, $info['end'] ?? NULL);

    if ($validation !== true) 
    {
      return $validation;
    }

    $intervals = [];
    
    $interval = [];

    $isIntervalEmpty = true;

    for($i = 0; $i < count($info['data']); $i++) {

      if (!array_key_exists($parameter, $info['data'][$i])) 
      {
        return self::PARAMETER_NOT_FOUND;
      }

      if ($info['data'][$i][$parameter] > $leftCriterion && $info['data'][$i][$parameter] < $rightCriterion)
      {

        if ($isIntervalEmpty)
        {
          $interval['begin'] = $info['data'][$i]['datetime'];
          $isIntervalEmpty = false;
        }
      } else {
        
        if (!$isIntervalEmpty)
        {

          $interval['end'] = $info['data'][$i-1]['datetime'];

          $intervals[] = $interval;
          $interval = [];

          $isIntervalEmpty = true;
        }
      }
    }

    if (!$isIntervalEmpty)
    {
  
      $interval['end'] = $info['end'];

      $intervals[] = $interval;
    }

    return $intervals;
  }
}