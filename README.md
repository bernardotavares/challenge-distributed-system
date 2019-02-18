# Challenge - Distributed System

## Get Started:

I would like to start by compliment this interesting challenge. Unfortunately, for personal limitations, I did not had enough time this weekend to pull this off. Furthermore, I took to much time experimenting with Apache Kafka which I had no previous experience.
Nevertheless, I assume it would be ok to show off my work so far, so you could get a sense of where I was heading.
I started by trying to understand about the producer – consumer dynamic in Kafka and how to use it with PHP. Then developed a basic PHP login system that uses ajax to make API requests. The last step would be to successfully integrate these two.

## Requirements
- Minimum PHP version: 7.1
- Kafka version greater than 0.8 (used 2.1.0)
- The consumer module needs kafka broker version greater than 0.9.0
- Kafka-php client [nmred/kafka-php]( https://github.com/weiboad/kafka-php)
- MySQL with a “challenge” database and a “users” table

## Structure
•	Producer (api folder) gets called by the ajax API requests
•	Consumer (consumer folder): does the logic, object models (objects folder) and connects with database (config folder)
•	Webserver (root html files)
