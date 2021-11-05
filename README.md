# Access log analyzer

This program analyzes access log and gives timestamps with increased system failure rates

## Requirements

For Linux, Windows and MacOS — installed [Docker](https://docs.docker.com/get-docker/)

Also you need to start docker daemon:

```
$ sudo service docker start
```

## Command line usage

```
$ git clone https://github.com/Patr1arch/Access-log-analyzer.git
$ cd Access-log-analyzer/
$ sudo docker build -t analyzer . 
$ sudo docker run -ti --rm -v path/to/log/access.log:/access.log analyzer /bin/bash
```
where path/to/log — path to directory where access log is stored. By default example access log stores in cloned directory, so you can define path/to/log as $(pwd)

After that you should use:

```
# cat access.log | php analyzer.php -u arg1 -t argv2
```
where arg1 — minimun availability rate(in percents) and arg2 — accaptable time of respond(in milliseconds)

## Testing

Also you can run tests for this analyzer. To do this, it's necessary to run following command after running docker container:

```
# phpunit AnalyzerTest.php
```
