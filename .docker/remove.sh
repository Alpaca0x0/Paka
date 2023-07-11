#!/bin/bash
docker rm paka-app paka-db
docker rmi alpaca/paka
sudo rm ./mysql -r
