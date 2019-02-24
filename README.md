# Challenge - Distributed System

- I started by create a basic PHP login system that uses ajax to make API requests.
- This API was then adapted to serve as a kafka's producer, sending asynchronous calls to consumer.
- These can be one of three topics: create_user, login or recover_password
- Depending of the incoming topic, the consumer will act accordingly. Using the config/database connection and/or the objects/user model.
- With kafka, in teory, the api folder (producer), the consumer folder (consumer) and the root files (webserver) could all be put in distributed machines so they would be horizontaly scalable.


## Requirements
- Minimum PHP version: 7.1
- Kafka version greater than 0.8 (used 2.1.0)
- The consumer module needs kafka broker version greater than 0.9.0
- Kafka-php client [nmred/kafka-php]( https://github.com/weiboad/kafka-php)
- Working MySQL database and SMTP (if you want the password_recovery to actually send an email)


## Getting started
- Clone this repository
- Run the composer install command (however, in this case, I decided to include the vendor folder)
- Run 'php consumer/config/migration.php' to create the 'challenge' database, the 'users' table and the first record

### Using windows
- Use wamp/xampp or other to quickly have a webserver with mysql running
- Donwload [kafka_2.12-2.1.1.tgz](https://kafka.apache.org/downloads), unzip it and, in the command line, 'cd bin/windows' inside this folder
- Run Zookeper server: 'zookeeper-server-start.bat ../../config/zookeeper.properties'
- Run Kafka server: 'kafka-server-start.bat ../../config/server.properties'

### Create the three topics
- kafka-topics.bat --create --zookeeper localhost:2181 --replication-factor 1 --partitions 1 --topic create_user
- kafka-topics.bat --create --zookeeper localhost:2181 --replication-factor 1 --partitions 1 --topic login
- kafka-topics.bat --create --zookeeper localhost:2181 --replication-factor 1 --partitions 1 --topic password_recovery

### Check it out
- Access your localhost to browse the index, signup and password_recovery pages 
- Run 'php consumer/consumer.php' to see how the consumer is responding to the different calls

## Not working! (Important Considerations)
- The calls are only working one way (webserver -> producer -> consumer). For now, I couldn't figure out how to retrieve information from the consumer to the webserver. This means that create_user and password_recovery are both working (they really just need to push information), the login (which require response if the login is valid or not) is not. But you can check the response in the consumer.
- I am still and always learning :)