FROM node:12.11.1-alpine

MAINTAINER Peter Lai <alk03073135@gmail.com>

RUN npm install -g ganache-cli

EXPOSE 8545

CMD ganache-cli -g 0 -l 6000000 --hostname=0.0.0.0