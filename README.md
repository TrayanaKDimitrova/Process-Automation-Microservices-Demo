
# Process Automation with Microservices

This project follows the structure and the approaches from [Process Automation with ASP.NET Core Microservices](https://github.com/ivaylokenov/Process-Automation-with-ASP.NET-Core-Microservices), which contains the demo source from [SoftUni course](https://softuni.bg/trainings/3162/process-automation-with-asp-net-core-microservices-october-2020). It's lightweight and doesn't require that much resources.

## How to use/run this project?

### Commands

- `docker-compose build` -- builds the docker images
- `docker-compose push` -- pushes the docker images to docker hub
- `docker-compose up -d` -- runs the docker in a detached mode
- `docker-compose stop` -- stops the docker containers
- `docker-compose rm` -- removes the stopped containers

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

### Deploy to gcloud

1. run kubectl commands
   1. switch to gcloud
      - gcloud container clusters get-credentials car-rental-system-dev --zone <zone> --project <project-id>
   2. make sure you have a clean start by deleting everything
      - kubectl delete all --all
      - kubectl delete pvc --all 
   3. deploy the loadbalancers
      - kubectl apply -f .k8s/loadbalancers/clients/
      - kubectl apply -f .k8s/loadbalancers/services/
   4. configure the environments
      - go to https://console.cloud.google.com/kubernetes/discovery?project=<project-id>
      - wait till the loadbalancers are deployed and their endpoints become available
      - copy the endpoints from the `Services & Ingress` section
      - edit the development.yml or production.yml environment files and set the values accordingly
         ```
         api-url: http://<api-service--endpoint-ip>:5080
         admin-allowed-origins: http://<admin-service--endpoint-ip>:5088
         client-allowed-origins: http://<client-service--endpoint-ip>:5084
         ```
   5. deploy the configurations
      - kubectl apply -f .k8s/.environments/development.yml
   6. deploy the services
      - kubectl apply -f .k8s/web-services/clients
      - kubectl apply -f .k8s/web-services/services
   6. deploy the database
      - kubectl apply -f .k8s/databases

## Jenkins

1. create a new multibranch pipeline project
2. configurations settings
   1. configure your git `branch source`
   2. configure your `docker credentials`
   3. save settings
3. scan repositories if such aren't yet available
4. go to the `dashboard -> manage -> plugins` and install
   * Kubernetes CLI Plugin
   * Email Extension Template Plugin
   * Email Ext Recipients Column Plugin
5. go to the `dashboard -> manage -> configure/settings` and configure the `cloud` settings
   * kubernetes cloud details
      * Name -> `jenkins-robot`
         * `kubectl create serviceaccount jenkins-robot`
         * `kubectl create rolebinding jenkins-robot-binding --clusterrole=cluster-admin --serviceaccount=default:jenkins-robot`
      * Kubernetes URL -> `put your cluster IP here with HTTPS`
      * Kubernetes server certificate key
         * `kubectl describe sa jenkins-robot`
         * `kubectl describe secret {token-secret}`
      * Kubernetes Namespace -> `default`
6. the email notifications are sent to the email address of the Jenkins users running the pipelines

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
