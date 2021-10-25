# Access log analyzer

This program analyzes access log and gives timestamps with increased system failure rates

## Requirements

For Linux, Windows and MacOS -- installed [Docker](https://docs.docker.com/get-docker/)

## Command line usage

```
$ sudo docker build -t analyzer . && sudo docker run -ti -v path/to/log/access.log:/access.log analyzer /bin/sh:
```
where path/to/log -- path to directory where access log is stored

After that you should use:

```
cat access.log | php ./analyzer.php -u arg1 -t argv2
```
where arg1 -- minimun availability rate and arg2 -- accaptable time of respond

