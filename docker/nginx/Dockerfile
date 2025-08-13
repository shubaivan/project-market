# Use Alpine Linux
FROM nginx:stable-alpine

RUN apk add --update --no-cache nginx

# Timezone
ENV TIMEZONE Europe/Kiev

# Let's roll
RUN	apk update && \
	apk upgrade && \
	apk add --update openssl nginx && \
	apk add --update tzdata && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	mkdir /etc/nginx/certificates && \
	openssl req \
		-x509 \
		-newkey rsa:2048 \
		-keyout /etc/nginx/certificates/key.pem \
		-out /etc/nginx/certificates/cert.pem \
		-days 365 \
		-nodes \
		-subj /CN=localhost && \
	mkdir /www && \
	apk del tzdata && \
	rm -rf /var/cache/apk/*

COPY ./nginx/default.conf /etc/nginx/conf.d/
COPY ./nginx/default.conf /etc/nginx/sites-enable/

COPY ./nginx/defaulthttps.conf /etc/nginx/conf.d/
COPY ./nginx/defaulthttps.conf /etc/nginx/sites-enable/

RUN adduser -D -g '' -G www-data www-data
RUN mkdir -p /run/nginx


#CMD ["nginx"]
CMD [ "nginx", "-g", "daemon off;" ]


EXPOSE 80
EXPOSE 443
