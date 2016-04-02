#!/bin/bash
export SYMFONY_ENV=prod
sleep 15 # don't really like it but we need to wait for mysql and rmq to complete
php apps/lending/bin/console cache:clear
php apps/lending/bin/console rabbitmq:setup-fabric