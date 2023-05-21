# gtipackage

Данный пакет позволяет получать на основе массива данных временные интервалы по параметру из ГТИ

## Как подключить
Укажите в файле composer.json своего проекта зависимость от пакета и адрес репозитория:
```
...
"require": {
    ...
    "shmel/gtipackage": "^1.0.0"
},
...
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/TimProg20/gtipackage"
    }
]
...
```
Затем запустите следующую команду, для загрузки внесенных изменений и установки пакета:
```
composer update
```

## Применение

### Класс Gti
В пакете реализован класс Gti. Чтобы его подключить используйте:
```
use Shmel\GtiPackage\Gti;
```
Данный класс реализует 5 методов. Все методы возвращают массив временных интервалов формата:

```
[
    [
        "begin" => "2023-04-06 00:00:00",
        "end" => "2023-04-06 23:59:59"
    ]
]
```

Методы:
- #### getUpPeriods
Данный метод возвращает все непрерывные интервалы вверх (непрерывное увеличение параметра, до первого уменьшения) 
```
    $gti = new Gti();
    $gtiResult = $gti->getUpPeriods($contentJson, 'dol');
```
Параметр 1 - массив данных в формате json  
Параметр 2 - название параметра, по которому будет происходить выборка
- #### getDownPeriods
Данный метод возвращает все непрерывные интервалы вниз (непрерывное уменьшение параметра, до первого увеличения) 
```
    $gti = new Gti();
    $gtiResult = $gti->getDownPeriods($contentJson, 'dol');
```
Параметр 1 - массив данных в формате json  
Параметр 2 - название параметра, по которому будет происходить выборка  
- #### getDownPeriods
Данный метод возвращает все непрерывные интервалы, где параметр меньше заданного значения
```
    $gti = new Gti();
    $gtiResult = $gti->getLessThanPeriods($contentJson, 'dol', 7);
```
Параметр 1 - массив данных в формате json  
Параметр 2 - название параметра, по которому будет происходить выборка  
Параметр 3 - критерий выборки
- #### getLessThanPeriods
Данный метод возвращает все непрерывные интервалы, где параметр меньше заданного значения
```
    $gti = new Gti();
    $gtiResult = $gti->getLessThanPeriods($contentJson, 'dol', 7);
```
Параметр 1 - массив данных в формате json  
Параметр 2 - название параметра, по которому будет происходить выборка  
Параметр 3 - значение для сравнения
- #### getMoreThanPeriods
Данный метод возвращает все непрерывные интервалы, где параметр больше заданного значения
```
    $gti = new Gti();
    $gtiResult = $gti->getMoreThanPeriods($contentJson, 'dol', 7);
```
Параметр 1 - массив данных в формате json  
Параметр 2 - название параметра, по которому будет происходить выборка  
Параметр 3 - значение для сравнения
- #### getBetweenPeriods
Данный метод возвращает все непрерывные интервалы между двумя заданными значениями 
```
    $gti = new Gti();
    $gtiResult = $gti->getBetweenPeriods($contentJson, 'dol', 7, 10);
```
Параметр 1 - массив данных в формате json  
Параметр 2 - название параметра, по которому будет происходить выборка  
Параметр 3 - левая граница для сравнения  
Параметр 4 - правая граница для сравнения

### Формат массива данных
Данные должны храниться в json формате следующего вида:

```
{
    "begin":"2023-04-06 00:00:00",
    "end":"2023-04-06 23:59:59",
    "data":[
        {
            "datetime":"2023-04-06 00:00:00",
            "dol":1500.7,
            "hookload":12.442252124000003,
            "wellbore":3,
            "dol_delta":0,
            "depth_hole":1891.5998,
            "key_torque":0.09951289,
            "tech_stage":6,
            "load_chisel":0,
            "dol_original":1200.1,
            "rotar_torque":0.007123577600000002,
            "depth_hole_original":1993.6004
        },
        {
            "datetime":"2023-04-06 00:00:05",
            "dol":1500.7,
            "hookload":12.6422532424000003,
            "wellbore":4,
            "dol_delta":0,
            "depth_hole":1890.5998,
            "key_torque":0.12451289,
            "tech_stage":4,
            "load_chisel":0,
            "dol_original":1206.4,
            "rotar_torque":0.009123597612000002,
            "depth_hole_original":1992.6014
        }
    ]
}
```
begin - дата и время начала отсчета  
end - дата и время конца отсчета  
date - массив с данными, которые содержат datetime (дата и время фиксирования значений) и другие параметры.

### Возможные ошибки
1. В случае если формат json данных некорректный, методы вернут **"Некорректный json"**
2. Если пользовательский параметр некорректный, методы вернут **"Некорректный параметр"**
3. Если параметр begin не найден, методы вернут **"Параметр begin не найден"**
4. Если параметр end не найден, методы вернут **"Параметр end не найден"**
5. Если пользовательский параметр не найден в массиве данных, методы вернут **"Параметр не найден"**
6. Если для метода getBetweenPeriods указали левую границу, которая больше правой, то метод вернёт **"Левая граница больше правой"**