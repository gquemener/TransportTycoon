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
```

## Tests

```
$ bin/behat
```

## Usage

```
$ bin/app time-to-deliver [-vvv] [--debug <directory>] ABBA
```

### Dumping events log and generating a Chrome trace

```
$ bin/app time-to-deliver --debug logs ABBA # will create ./logs/ABBA.log file
$ bin/trace.py ./logs/ABBA.log > ./traces/ABBA.json
```

Open chrome at url `chrome://tracing`, click the `Load` button and select the `./traces/ABBA.json`.
