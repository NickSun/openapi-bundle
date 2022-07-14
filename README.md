Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require nicksun/openapi-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require nicksun/openapi-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    NickSun\OpenApi\OpenApiBundle::class => ['all' => true],
];
```

Usage
----------------------------------

### Enable Routing

```yaml
// config/routes/open_api.yaml

_open_api:
  resource: '@OpenApiBundle/Resources/config/routing.yaml'
```

After that check `/api/doc` endpoint.

### Update bundle parameters (optional)

Check [https://cdnjs.com/libraries/swagger-ui](https://cdnjs.com/libraries/swagger-ui) for the latest swagger ui version.

```yaml
// config/packages/open_api.yaml

open_api:
  definitions_dir: openapi
  swagger_ui_version: 3.46.0
  title: My Site
```

### Create definitions dir in the config folder and place your yaml files there.

Use [OpenAPI Specification](https://swagger.io/specification/) for defining your API endpoints.
Organize your folder structure wisely.

```bash
openapi
├── anchor
│   ├── response.yaml
│   └── schemas.yaml
├── book
│   ├── paths.yaml
│   └── schemas.yaml
├── openapi.yaml
└── user
    ├── paths.yaml
    └── schemas.yaml

```

### Limitations

You can use yaml anchors defined in the different files only if they defined on the top root document level (without indentation).

```yaml
// openapi/anchor/schemas.yaml

components:
  schemas:
    ApiProblemValidation:
      type: object
      description: Validation error
      properties:
        invalid-params:
          type: array
          items:
            type: object
            properties:
              name:
                type: string
                description: Error name
                example: email
              reason:
                type: string
                description: Error reason
                example: Invalid email
        type:
          type: string
          example: https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        title:
          type: string
          example: Validation error
        status:
          type: integer
          format: int64
          description: HTTP status code
          example: 400
        detail:
          type: string
          example: Validation error
        code:
          type: integer
          format: int64
          description: Error code
          example: 12
        instance:
          type: string
          example: /users
```
```yaml
// openapi/anchor/response.yaml

400Validation: &400Validation
  400:
    description: Bad request
    content:
      application/json:
        schema:
          $ref: '#/components/schemas/ApiProblemValidation'
```
```yaml
// openapi/user/paths.yaml

paths:
  /users:
    post:
      tags:
        - user
      description: Create new user.
      summary: Create new user.
      requestBody:
        description: User to add to the system.
        required: true
        content:
          'application/json':
            schema:
              $ref: '#/components/schemas/UserCreate'
      security:
        - Bearer: []
      responses:
        201:
          description: User created.
        <<: *400Validation
```
