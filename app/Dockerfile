FROM node:latest
WORKDIR /usr/src/app
COPY package*.json app.js ./
RUN npm install
COPY . .
EXPOSE 8038
CMD ["node", "app.js"]
