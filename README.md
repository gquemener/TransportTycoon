## Statement
https://github.com/Softwarepark/exercises/blob/master/transport-tycoon.md

## Event Storming
https://miro.com/welcomeonboard/MtjvR60cUsVfwMSGWhbsOtrxgMOlSeXjFYc1U2M8033aIuIQdb9ID72hbcNNsfi8

## Solution

Given the program, the results are:

| Input        | Output |
| ------------ | ------ |
| A            | 5      |
| AB           | 5      |
| BB           | 5      |
| ABB          | 7      |
| AABABBAB     | 29     |
| ABBBABAAABBB | 41     |

## Install

```
$ composer install
$ docker-compose up -d
```

# Tests

```
$ bin/behat
```
