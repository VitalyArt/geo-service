build:
	docker build -t geo .

run_dev:
	docker run --rm -it -v `pwd`:/app -p 8080:8080 geo rr serve

run_prod:
	docker run -p 8080:8080 geo rr serve

bash:
	docker run -v `pwd`:/app --rm -it geo bash
