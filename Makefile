build:
	docker build -t vitalyart/geo-service .

push:
	docker push vitalyart/geo-service

run_dev:
	docker run --rm -it -v `pwd`:/app -p 8080:8080 vitalyart/geo-service rr serve -c /app/.rr-dev.yaml

run_prod:
	docker run -p 8080:8080 vitalyart/geo-service rr serve

bash:
	docker run -v `pwd`:/app --rm -it vitalyart/geo-service bash
