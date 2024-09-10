# This Dockerfile is used to watch css file in dev
FROM node:20-alpine

WORKDIR /app

RUN mkdir -p /app/output && \
    mkdir -p /app/input && \
    npm install sass@^1.77.2 -g

CMD [ "npx", "sass", "--watch", "--no-source-map", "/app/input:/app/output" ]