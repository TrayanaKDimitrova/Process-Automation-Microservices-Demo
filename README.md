# Process Automation with Microservices

This project follows the structure and the approaches from [Process Automation with ASP.NET Core Microservices](https://github.com/ivaylokenov/Process-Automation-with-ASP.NET-Core-Microservices), which contains the demo source from [SoftUni course](https://softuni.bg/trainings/3162/process-automation-with-asp-net-core-microservices-october-2020). It's lightweight and doesn't require that much resources.

## How to use/run this project?

### Commands

- `docker-compose build` -- builds the docker images
- `docker-compose up -d` || runs the docker in a detached mode
- `docker-compose stop` || stops the docker containers
- `docker-compose rm` || removes the stopped containers

### Standard start of this project with docker-compose

1. run docker containers from your terminal
   - docker-compose build
   - docker-compose up -d
2. go to the Web Admin at http://localhost:5088 and click on Run Migrations
   - this will force the API service to connect to the MySQL container and create the DB tables
3. go to the Web Client at http://localhost:5084

### Known issues on localhost -- when using MySQL 8.0.23

Sometimes the API service throws error 500 when trying to connect to the database. In this case you have to:

- stop the docker containers
  - `docker-composer stop`
- remove the containers
  - `docker-composer rm`
- remove the MySQL volume
  - `docker volume rm kubernetes-demo-project_softuni_kubernetes_mysqldata`
- start over ...

### Start project withkubectl

1. run kubectl commands
   - kubectl delete all --all
   - kubectl delete pvc --all
   - kubectl apply -f .k8s/.environments/local.yml
   - kubectl apply -f .k8s/databases/
   - kubectl apply -f .k8s/web-services/
   - kubectl apply -f .k8s/clients/
2. go to the Web Admin at http://localhost:5088 and click on Run Migrations
   - this will force the API service to connect to the MySQL container and create the DB tables
3. go to the Web Client at http://localhost:5084

## Application Architecture

![Application Architecture](https://github.com/TrayanaKDimitrova/Process-Automation-Microservices-Demo/blob/main/Resources/ApplicationArchitecture.jpg)

### API Service

POST http://localhost:5080/api/db/migrate
Runs the `php artisan migrate` command, that creates the database tables and runs the migrations, on the server.
Use this route only if new DB migrations are available or if setting up the project with a brand new database with no tables created.

```
# payload
<empty>

# response
true
```

GET http://localhost:5080/api/cars
Returns a list with all cars

```
# response
[
    {
        "created_at": datetime string,
        "derivative": string,
        "fuel_type": string,
        "id": int,
        "model": string,
        "price": string,
        "transmission": string,
        "updated_at": datetime string
    },
	...
]
```

POST http://localhost:5080/api/car
Adds a new car record

```
# payload
{
	"derivative": string,
	"model": string,
	"transmission": string,
	"fuel_type": string,
	"price": string
}

# response
{
    "derivative": string
    "model": string,
    "transmission": string,
    "fuel_type": string,
    "price": string,
    "updated_at": datetime string,
    "created_at": datetime string,
    "id": int
}
```

### Web Client

Url: http://localhost:5084

![Web Client](https://github.com/TrayanaKDimitrova/Process-Automation-Microservices-Demo/blob/main/Resources/WebClient.png)

### Web Admin

Url: http://localhost:5084

![Web Admin](https://github.com/TrayanaKDimitrova/Process-Automation-Microservices-Demo/blob/main/Resources/WebAdmin.png)
