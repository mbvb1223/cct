# Installation

### Without docker
- Checkout "dev" branch and composer install libraries
- Update your .env
- Create the database (`symfony console doctrine:database:create`) + Migrate the database (`console doctrine:migrations:migrate`)
- Go to "code" folder and start your project (`symfony server:start`)

### With docker
- Checkout "dev" branch
- Build and run your app with Docker Compose
- Composer install libraries
- Update your .env
- Create the database (`symfony console doctrine:database:create`) + Migrate the database (`console doctrine:migrations:migrate`)
- Go to localhost:1111 for web-nginx and localhost:1113 to access phpmyadmin

![image](https://github.com/mbvb1223/cct/assets/11681514/d936e995-c412-443a-9a35-14d19fa1e3a5)

# My solution
- Assume that one employee has only one supervisor. One supervisor can have multiple employees
- Assume that Name is unique, we do not have two employees with the same Name (actually, we should call it is EmployeeID instead of Name)
- I want to focus on the main logic, so I did not implement some validations/performance issues/try-catch some special cases...
- Create a table (employee: id, name, parent_id)

### Point 1
- Get data from the POST JSON (POST: domain/employees)
- Get all existing employees by given Names
- Create Supervisor first
- Get ID of the Supervisor => Create employee with **parent_id** is Supervisor's id
- [Code is here](https://github.com/mbvb1223/cct/blob/dev/code/src/Controller/EmployeeController.php#L22)
![image](https://github.com/mbvb1223/cct/assets/11681514/dbb9490b-607e-45d0-b00e-0ab8e130ef93)
![image](https://github.com/mbvb1223/cct/assets/11681514/6aebbf99-6a89-4fbf-8fc9-1c46dd46e18d)


### Point 2
- Load all Employees to an array (_we will ignore a performance issue here_)
- **groupByParentId()** will group them key by **parent_id**
![image](https://github.com/mbvb1223/cct/assets/11681514/3e73da04-97a9-4600-836d-e295e9a8cf39)
- Write a recursive function (**mapEmployeeTree()**) to convert the group above into expected JSON ==> [Code is here](https://github.com/mbvb1223/cct/blob/dev/code/src/Service/EmployeeService.php#L18).
![image](https://github.com/mbvb1223/cct/assets/11681514/f54e0130-8581-41da-a12d-039774c256ce)
![image](https://github.com/mbvb1223/cct/assets/11681514/32c0a1f0-cb54-4677-9551-38254d7af073)


### Point 3
- GET **DOMAIN/employees/{name}?level={hierarchical level}**
- Get an Employee by the Name condition
- Write a recursive function to get supervisors with the Level condition
- Write a recursive function to get Child employees with the Level condition
- After having all of the wanted employees => Do the same point 2 above => Group them key by **parent_id** => Call **mapEmployeeTree()** => return value
![image](https://github.com/mbvb1223/cct/assets/11681514/1703d24c-b8c3-4c79-b96b-a37aa559f807)
- **NOTE**: "I want search by the name of the employee and the number of hierarchical levels to be returned" 
=> Actually, I am not clear about "hierarchical levels". Is it Parent hierarchical levels or child hierarchical levels or both?
So in Controller, level = 2 then it will get 2 supervisors and 2 child employees 
But in Service layer, **`getTreeByNameAndLevel(Employee $employee, int $parentLevel = null, int $childLevel = null): array`** will support definition Parent and Child level ==> [Code is here](https://github.com/mbvb1223/cct/blob/dev/code/src/Service/EmployeeService.php#L32).
![image](https://github.com/mbvb1223/cct/assets/11681514/e3ca7a73-b399-443f-b056-0c9dcc50de12)


### Point 4
- I am not familiar with docker deploying => So please allow me to create docker compose files for local setup => [Code is here](https://github.com/mbvb1223/cct/pull/5).
- For PROD deploying, I used [https://deployer.org/](https://deployer.org/) tool ==> [Code is here](https://github.com/mbvb1223/cct/pull/6)
- I deployed this project to EC2 -> We can test here: 
[https://khien.onthitoeic.net/employees](https://khien.onthitoeic.net/employees) <br />
[https://khien.onthitoeic.net/employees/Employee 4?level=1](https://khien.onthitoeic.net/employees/Employee%204?level=1) <br />
[https://khien.onthitoeic.net/employees/Employee 4?level=2](https://khien.onthitoeic.net/employees/Employee%204?level=2) <br />

# Addition
- The main logic is existing at **EmployeeController** and **EmployeeService** ==> [Code is here](https://github.com/mbvb1223/cct/pull/2/files)
- I used [CircleCI](https://app.circleci.com/pipelines/github/mbvb1223/cct) for continuous integration => [Code is here](https://github.com/mbvb1223/cct/pull/4)
![image](https://github.com/mbvb1223/cct/assets/11681514/d01d6c1f-b946-4038-be25-631f02b7cff9)
- I wrote some unit tests, integration tests and functional tests ==> [Code is here](https://github.com/mbvb1223/cct/pull/3)
![image](https://github.com/mbvb1223/cct/assets/11681514/3f6f55aa-0144-4f30-8494-983caa9314ae)

