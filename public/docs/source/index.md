---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://localhost/docs/collection.json)

<!-- END_INFO -->

#Controller - Clientes

Funciones de ClientController
<!-- START_92b03f70a309acfe344c1bce06c6e8d8 -->
## clientes/exportar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/clientes/exportar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/exportar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/clientes/exportar" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET clientes/exportar`


<!-- END_92b03f70a309acfe344c1bce06c6e8d8 -->

<!-- START_6d64104845ae0acd28289927a6498557 -->
## clientes/importar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/clientes/importar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/importar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/clientes/importar" 
```



### HTTP Request
`POST clientes/importar`


<!-- END_6d64104845ae0acd28289927a6498557 -->

<!-- START_1af1a947e16afcb5289fad8940c57ec5 -->
## Returns the required ajax data.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/clients", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/clients");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/clients" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/clients`


<!-- END_1af1a947e16afcb5289fad8940c57ec5 -->

<!-- START_80694fbae5e66acf9ab881e3b877e8f0 -->
## Restore the specific item

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/clientes/1/restore", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/1/restore");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/clientes/1/restore" 
```



### HTTP Request
`PATCH clientes/{id}/restore`


<!-- END_80694fbae5e66acf9ab881e3b877e8f0 -->

<!-- START_83555366ce3e1201a2e2a6bfc33a0b90 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/clientes", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/clientes" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET clientes`


<!-- END_83555366ce3e1201a2e2a6bfc33a0b90 -->

<!-- START_1a165c4a73440c4d43a0cb07489d9c6b -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/clientes/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/clientes/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET clientes/create`


<!-- END_1a165c4a73440c4d43a0cb07489d9c6b -->

<!-- START_06e7183aca3b2450f6a4bceb40537cc0 -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/clientes", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/clientes" 
```



### HTTP Request
`POST clientes`


<!-- END_06e7183aca3b2450f6a4bceb40537cc0 -->

<!-- START_488ec509cc3eeccdce3ba7af760d8761 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/clientes/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/clientes/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET clientes/{cliente}`


<!-- END_488ec509cc3eeccdce3ba7af760d8761 -->

<!-- START_b52889892caebd8cbbf2aa53024ad054 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/clientes/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/clientes/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET clientes/{cliente}/edit`


<!-- END_b52889892caebd8cbbf2aa53024ad054 -->

<!-- START_53aba73604508999c660a6b0c488990e -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/clientes/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/clientes/1" 
```



### HTTP Request
`PUT clientes/{cliente}`

`PATCH clientes/{cliente}`


<!-- END_53aba73604508999c660a6b0c488990e -->

<!-- START_553d5e58e38bf7b140475cd806bd91b4 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/clientes/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/clientes/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/clientes/1" 
```



### HTTP Request
`DELETE clientes/{cliente}`


<!-- END_553d5e58e38bf7b140475cd806bd91b4 -->

#Controller - Emails

Funciones de EmailController. Se encarga de recibir los correos electrónicos y registrarlos ya sea en facturas enviadas o en recibidas.
<!-- START_1dd919442d0a5cd278e26614deeef9df -->
## api/email-facturas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/api/email-facturas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/email-facturas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/api/email-facturas" 
```



### HTTP Request
`POST api/email-facturas`


<!-- END_1dd919442d0a5cd278e26614deeef9df -->

#Controller - Empresa

Funciones de CompanyController
<!-- START_46197ad416b05b081edbc94fbc23ac1d -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/editar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/editar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/editar" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/editar`


<!-- END_46197ad416b05b081edbc94fbc23ac1d -->

<!-- START_bad77c7bf14c245ef8a68765e092ff96 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/configuracion", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/configuracion");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/configuracion" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/configuracion`


<!-- END_bad77c7bf14c245ef8a68765e092ff96 -->

<!-- START_fe5a9ef4d415a10b1d024e2246f1f44a -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/certificado", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/certificado");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/certificado" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/certificado`


<!-- END_fe5a9ef4d415a10b1d024e2246f1f44a -->

<!-- START_c2b54ff03fe8f90bb1a8dd04fa3cc3e6 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/equipo", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/equipo");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/equipo" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/equipo`


<!-- END_c2b54ff03fe8f90bb1a8dd04fa3cc3e6 -->

<!-- START_586c50dacd6b5a2452cc78ec4e93d0a7 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/empresas/update/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/update/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/empresas/update/1" 
```



### HTTP Request
`PATCH empresas/update/{id}`


<!-- END_586c50dacd6b5a2452cc78ec4e93d0a7 -->

<!-- START_4e49824ef208f6961859d0b82d48498f -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/empresas/update-configuracion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/update-configuracion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/empresas/update-configuracion/1" 
```



### HTTP Request
`PATCH empresas/update-configuracion/{id}`


<!-- END_4e49824ef208f6961859d0b82d48498f -->

<!-- START_d05aac6d5896fdc0c49857f0d363225f -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/empresas/update-certificado/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/update-certificado/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/empresas/update-certificado/1" 
```



### HTTP Request
`PATCH empresas/update-certificado/{id}`


<!-- END_d05aac6d5896fdc0c49857f0d363225f -->

<!-- START_2b7e4454fde2d03315ca9e541fcdcf84 -->
## empresas/set-prorrata-2018-facturas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/set-prorrata-2018-facturas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/set-prorrata-2018-facturas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/set-prorrata-2018-facturas" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/set-prorrata-2018-facturas`


<!-- END_2b7e4454fde2d03315ca9e541fcdcf84 -->

<!-- START_77f96fc41ad160d7df1e008257ae0d46 -->
## empresas/comprar-facturas-vista
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/comprar-facturas-vista", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/comprar-facturas-vista");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/comprar-facturas-vista" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/comprar-facturas-vista`


<!-- END_77f96fc41ad160d7df1e008257ae0d46 -->

<!-- START_dc5d64b27f0bd3512915e575c4d88585 -->
## empresas/seleccionar-cliente
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/empresas/seleccionar-cliente", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/seleccionar-cliente");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/empresas/seleccionar-cliente" 
```



### HTTP Request
`PATCH empresas/seleccionar-cliente`


<!-- END_dc5d64b27f0bd3512915e575c4d88585 -->

<!-- START_7d34fdc80e7b90e6be05629e19456353 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas`


<!-- END_7d34fdc80e7b90e6be05629e19456353 -->

<!-- START_8676f5796fd864d761a853b89efd9ca4 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/create`


<!-- END_8676f5796fd864d761a853b89efd9ca4 -->

<!-- START_c82e47c4d9fa6b80abfc18517a1094eb -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/empresas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/empresas" 
```



### HTTP Request
`POST empresas`


<!-- END_c82e47c4d9fa6b80abfc18517a1094eb -->

<!-- START_fda59d0b0ca67b6ccaeb2f8193a1db87 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/{empresa}`


<!-- END_fda59d0b0ca67b6ccaeb2f8193a1db87 -->

<!-- START_7256b7d54c42012c4016574799c98268 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/empresas/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/empresas/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET empresas/{empresa}/edit`


<!-- END_7256b7d54c42012c4016574799c98268 -->

<!-- START_27cc9496d2c1070c14db972eaee39043 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/empresas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/empresas/1" 
```



### HTTP Request
`PUT empresas/{empresa}`

`PATCH empresas/{empresa}`


<!-- END_27cc9496d2c1070c14db972eaee39043 -->

<!-- START_9205dc2d282491a88a4470c14166faf9 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/empresas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/empresas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/empresas/1" 
```



### HTTP Request
`DELETE empresas/{empresa}`


<!-- END_9205dc2d282491a88a4470c14166faf9 -->

<!-- START_1cd1ad9cbb61ace230326e18983f1d2e -->
## change-company
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/change-company", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/change-company");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/change-company" 
```



### HTTP Request
`POST change-company`


<!-- END_1cd1ad9cbb61ace230326e18983f1d2e -->

<!-- START_d674f4fc57d4ec5379e08eb139df35e0 -->
## company-deactivate/{token}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/company-deactivate/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/company-deactivate/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/company-deactivate/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET company-deactivate/{token}`


<!-- END_d674f4fc57d4ec5379e08eb139df35e0 -->

#Controller - Facturas de compra

Funciones de BillController
<!-- START_9a3fa5ab323654f64f6f4868ac0e21c4 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/exportar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/exportar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/exportar" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/exportar`


<!-- END_9a3fa5ab323654f64f6f4868ac0e21c4 -->

<!-- START_684d8f0f8f04b389e276bffaec1a9581 -->
## facturas-recibidas/importarExcel
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-recibidas/importarExcel", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/importarExcel");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-recibidas/importarExcel" 
```



### HTTP Request
`POST facturas-recibidas/importarExcel`


<!-- END_684d8f0f8f04b389e276bffaec1a9581 -->

<!-- START_3e1f2aa2955be8e2b1ebc12418b5d88b -->
## facturas-recibidas/importarXML
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-recibidas/importarXML", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/importarXML");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-recibidas/importarXML" 
```



### HTTP Request
`POST facturas-recibidas/importarXML`


<!-- END_3e1f2aa2955be8e2b1ebc12418b5d88b -->

<!-- START_beee9f1385f6ca83282a6002cfc6f394 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/aceptaciones", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/aceptaciones");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/aceptaciones" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/aceptaciones`


<!-- END_beee9f1385f6ca83282a6002cfc6f394 -->

<!-- START_8c2a1447851dd73a84b6928f84f1d313 -->
## Metodo para hacer las aceptaciones

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-recibidas/respuesta-aceptacion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/respuesta-aceptacion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-recibidas/respuesta-aceptacion/1" 
```



### HTTP Request
`PATCH facturas-recibidas/respuesta-aceptacion/{id}`


<!-- END_8c2a1447851dd73a84b6928f84f1d313 -->

<!-- START_53cd52b84eb0d3f62f9e0ded0254fa16 -->
## Despliega las facturas que requieren validación de códigos

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/validaciones", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/validaciones");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/validaciones" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/validaciones`


<!-- END_53cd52b84eb0d3f62f9e0ded0254fa16 -->

<!-- START_fc117f6445094cf8230bc9ceab26875d -->
## facturas-recibidas/confirmar-validacion/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-recibidas/confirmar-validacion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/confirmar-validacion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-recibidas/confirmar-validacion/1" 
```



### HTTP Request
`PATCH facturas-recibidas/confirmar-validacion/{id}`


<!-- END_fc117f6445094cf8230bc9ceab26875d -->

<!-- START_537e7af096e32a182e2e645c2086ea9a -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/autorizaciones", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/autorizaciones");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/autorizaciones" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/autorizaciones`


<!-- END_537e7af096e32a182e2e645c2086ea9a -->

<!-- START_7a92b5ae06bac9edfaa0fc94c8156eed -->
## facturas-recibidas/confirmar-autorizacion/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-recibidas/confirmar-autorizacion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/confirmar-autorizacion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-recibidas/confirmar-autorizacion/1" 
```



### HTTP Request
`PATCH facturas-recibidas/confirmar-autorizacion/{id}`


<!-- END_7a92b5ae06bac9edfaa0fc94c8156eed -->

<!-- START_05fc85e1258c8e1d7a62176a59fe1def -->
## facturas-recibidas/aceptaciones-otros
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/aceptaciones-otros", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/aceptaciones-otros");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/aceptaciones-otros" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/aceptaciones-otros`


<!-- END_05fc85e1258c8e1d7a62176a59fe1def -->

<!-- START_4571f09a5bae89fd32812e30b43b681e -->
## facturas-recibidas/confirmar-aceptacion-otros/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-recibidas/confirmar-aceptacion-otros/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/confirmar-aceptacion-otros/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-recibidas/confirmar-aceptacion-otros/1" 
```



### HTTP Request
`PATCH facturas-recibidas/confirmar-aceptacion-otros/{id}`


<!-- END_4571f09a5bae89fd32812e30b43b681e -->

<!-- START_b88d3308cfffc010f1b84ccf299a6790 -->
## facturas-recibidas/marcar-para-aceptacion/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-recibidas/marcar-para-aceptacion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/marcar-para-aceptacion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-recibidas/marcar-para-aceptacion/1" 
```



### HTTP Request
`PATCH facturas-recibidas/marcar-para-aceptacion/{id}`


<!-- END_b88d3308cfffc010f1b84ccf299a6790 -->

<!-- START_0072a6cec897835e5ea95c3a9a61d21a -->
## facturas-recibidas/validar/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/validar/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/validar/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/validar/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/validar/{id}`


<!-- END_0072a6cec897835e5ea95c3a9a61d21a -->

<!-- START_327b8f42ce25ea413fd76e58380c1927 -->
## facturas-recibidas/guardar-validar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-recibidas/guardar-validar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/guardar-validar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-recibidas/guardar-validar" 
```



### HTTP Request
`POST facturas-recibidas/guardar-validar`


<!-- END_327b8f42ce25ea413fd76e58380c1927 -->

<!-- START_96fc7910f2b5c22563e1521a51239c1f -->
## Index Data
Funcion AJAX para cargar data de las facturas.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/bills", [
    'headers' => [
            "Content-Type" => "application/json",
        ],
    'json' => [
            "filtro" => "perferendis",
        ],
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/bills");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "filtro": "perferendis"
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/bills" \
    -H "Content-Type: application/json" \
    -d '{"filtro":"perferendis"}'

```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/bills`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    filtro | Campo |  optional  | de filtro por tipo de documento. Por defecto es un 01. Si recibe 0 devuelve las eliminadas.

<!-- END_96fc7910f2b5c22563e1521a51239c1f -->

<!-- START_7ff56d686d527114393b6cc08c14c21c -->
## Returns the required ajax data.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/billsAccepts", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/billsAccepts");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/billsAccepts" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/billsAccepts`


<!-- END_7ff56d686d527114393b6cc08c14c21c -->

<!-- START_776543940cab7c8798df538686a14500 -->
## Returns the required ajax data.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/billsAuthorize", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/billsAuthorize");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/billsAuthorize" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/billsAuthorize`


<!-- END_776543940cab7c8798df538686a14500 -->

<!-- START_773e7d2cb86b2e10909cd399e16bb9a3 -->
## Restore the specific item

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-recibidas/1/restore", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/1/restore");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-recibidas/1/restore" 
```



### HTTP Request
`PATCH facturas-recibidas/{id}/restore`


<!-- END_773e7d2cb86b2e10909cd399e16bb9a3 -->

<!-- START_d3d41a29ed49a82ce5cce65e3d4db96f -->
## Index
Index de facturas recibidas. Usa indexData para cargar las facturas con AJAX

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas`


<!-- END_d3d41a29ed49a82ce5cce65e3d4db96f -->

<!-- START_ae2c18591bfe4a93a4f5ffccf9d382db -->
## Crear factura existente
Muestra la pantalla para crear facturas existentes

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/create`


<!-- END_ae2c18591bfe4a93a4f5ffccf9d382db -->

<!-- START_c5aaf5a80d72fad5e4f6dabcdb80a217 -->
## Guardar factura existente
Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-recibidas", [
    'headers' => [
            "Content-Type" => "application/json",
        ],
    'json' => [
            "document_key" => "magnam",
            "document_number" => "quae",
            "sale_condition" => "ex",
            "payment_type" => "et",
            "retention_percent" => "sint",
            "credit_time" => "odit",
            "buy_order" => "est",
            "other_reference" => "veritatis",
            "send_emails" => "officiis",
            "commercial_activity" => "sequi",
            "description" => "optio",
            "currency" => "provident",
            "currency_rate" => "repudiandae",
            "subtotal" => "reiciendis",
            "total" => "qui",
            "iva_amount" => "accusamus",
            "generated_date" => "iure",
            "hora" => "amet",
            "due_date" => "sed",
            "items" => "voluptas",
            "client_id" => "suscipit",
            "tipo_persona" => "quia",
            "id_number" => "aut",
            "code" => "sit",
            "email" => "rerum",
            "billing_emails" => "aliquid",
            "first_name" => "officia",
            "last_name" => "sint",
            "last_name2" => "consequatur",
            "country" => "veniam",
            "state" => "molestiae",
            "city" => "ab",
            "district" => "voluptates",
            "neighborhood" => "voluptatibus",
            "zip" => "repudiandae",
            "address" => "est",
            "phone" => "magni",
            "es_exento" => "qui",
        ],
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "document_key": "magnam",
    "document_number": "quae",
    "sale_condition": "ex",
    "payment_type": "et",
    "retention_percent": "sint",
    "credit_time": "odit",
    "buy_order": "est",
    "other_reference": "veritatis",
    "send_emails": "officiis",
    "commercial_activity": "sequi",
    "description": "optio",
    "currency": "provident",
    "currency_rate": "repudiandae",
    "subtotal": "reiciendis",
    "total": "qui",
    "iva_amount": "accusamus",
    "generated_date": "iure",
    "hora": "amet",
    "due_date": "sed",
    "items": "voluptas",
    "client_id": "suscipit",
    "tipo_persona": "quia",
    "id_number": "aut",
    "code": "sit",
    "email": "rerum",
    "billing_emails": "aliquid",
    "first_name": "officia",
    "last_name": "sint",
    "last_name2": "consequatur",
    "country": "veniam",
    "state": "molestiae",
    "city": "ab",
    "district": "voluptates",
    "neighborhood": "voluptatibus",
    "zip": "repudiandae",
    "address": "est",
    "phone": "magni",
    "es_exento": "qui"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-recibidas" \
    -H "Content-Type: application/json" \
    -d '{"document_key":"magnam","document_number":"quae","sale_condition":"ex","payment_type":"et","retention_percent":"sint","credit_time":"odit","buy_order":"est","other_reference":"veritatis","send_emails":"officiis","commercial_activity":"sequi","description":"optio","currency":"provident","currency_rate":"repudiandae","subtotal":"reiciendis","total":"qui","iva_amount":"accusamus","generated_date":"iure","hora":"amet","due_date":"sed","items":"voluptas","client_id":"suscipit","tipo_persona":"quia","id_number":"aut","code":"sit","email":"rerum","billing_emails":"aliquid","first_name":"officia","last_name":"sint","last_name2":"consequatur","country":"veniam","state":"molestiae","city":"ab","district":"voluptates","neighborhood":"voluptatibus","zip":"repudiandae","address":"est","phone":"magni","es_exento":"qui"}'

```



### HTTP Request
`POST facturas-recibidas`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    document_key | required |  optional  | Clave de documento
    document_number | required |  optional  | Consecutivo de documento
    sale_condition | required |  optional  | Condición de venta de Hacienda
    payment_type | required |  optional  | Método de pago
    retention_percent | required |  optional  | Porcentaje de retención aplicado
    credit_time | required |  optional  | Plazo de crédito
    buy_order | required |  optional  | Órden de compra
    other_reference | required |  optional  | Referencias
    send_emails | required |  optional  | Correos electrónicos separados por coma
    commercial_activity | required |  optional  | Actividad comercial asignada
    description | required |  optional  | Descripción/Notas de la factura
    currency | required |  optional  | Moneda, ejemplo: USD o CRC
    currency_rate | required |  optional  | Tipo de cambio. Por defecto e 1
    subtotal | required |  optional  | Subtotal de la factura
    total | required |  optional  | Total de la factura
    iva_amount | required |  optional  | Monto correspondiente al IVA
    generated_date | required |  optional  | Fecha de generacion
    hora | required |  optional  | Hora de generación
    due_date | required |  optional  | Fecha de vencimiento
    items | required |  optional  | Array con item_number, code, name, product_type, measure_unit, item_count, unit_price, subtotal, total, discount_type, discount, iva_type, iva_percentage, iva_amount, tariff_heading, is_exempt
    client_id | required |  optional  | ID del cliente. Usar -1 si desea crear uno nuevo
    tipo_persona | required |  optional  | No obligatorio. Tipo de persona de proveedor nuevo
    id_number | required |  optional  | No obligatorio. Cédula de proveedor nuevo
    code | required |  optional  | No obligatorio. Código de proveedor nuevo
    email | required |  optional  | No obligatorio. Correo proveedor nuevo
    billing_emails | required |  optional  | No obligatorio. Lista de correos separados por coma de proveedor nuevo
    first_name | required |  optional  | No obligatorio. Primer nombre proveedor nuevo
    last_name | required |  optional  | No obligatorio. Apellido de proveedor nuevo
    last_name2 | required |  optional  | No obligatorio. Segundo apellido de proveedor nuevo
    country | required |  optional  | No obligatorio. País de proveedor nuevo
    state | required |  optional  | No obligatorio. Provincia de proveedor nuevo
    city | required |  optional  | No obligatorio. Cantón de proveedor nuevo
    district | required |  optional  | No obligatorio. Distrito de proveedor nuevo
    neighborhood | required |  optional  | No obligatorio. Barrio de proveedor nuevo
    zip | required |  optional  | No obligatorio. Código postal de proveedor nuevo
    address | required |  optional  | No obligatorio. Dirección de proveedor nuevo
    phone | required |  optional  | No obligatorio. Teléfono de proveedor nuevo
    es_exento | required |  optional  | No obligatorio. Indicar si es exento o no.

<!-- END_c5aaf5a80d72fad5e4f6dabcdb80a217 -->

<!-- START_5920edfde794a2563c6c0565bfcc9cd8 -->
## Mostrar factura existente
Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/{facturas_recibida}`


<!-- END_5920edfde794a2563c6c0565bfcc9cd8 -->

<!-- START_a9ad4d71d636fc5c3634b8efb94711d4 -->
## Editar factura
Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-recibidas/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-recibidas/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-recibidas/{facturas_recibida}/edit`


<!-- END_a9ad4d71d636fc5c3634b8efb94711d4 -->

<!-- START_6d04a7b39f1f7cb83737f4551c44f98b -->
## Actualizar factura
Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/facturas-recibidas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/facturas-recibidas/1" 
```



### HTTP Request
`PUT facturas-recibidas/{facturas_recibida}`

`PATCH facturas-recibidas/{facturas_recibida}`


<!-- END_6d04a7b39f1f7cb83737f4551c44f98b -->

<!-- START_033c67786fa0daadb53213cef3a6c4e4 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/facturas-recibidas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-recibidas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/facturas-recibidas/1" 
```



### HTTP Request
`DELETE facturas-recibidas/{facturas_recibida}`


<!-- END_033c67786fa0daadb53213cef3a6c4e4 -->

#Controller - Facturas de venta

Funciones de InvoiceController
<!-- START_c54c9588280c0fea5eea07d967c2fbb2 -->
## facturas-emitidas/exportar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/exportar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/exportar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/exportar" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/exportar`


<!-- END_c54c9588280c0fea5eea07d967c2fbb2 -->

<!-- START_114edefb81f71d13b22bf466dc9401f7 -->
## facturas-emitidas/importarExcel
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-emitidas/importarExcel", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/importarExcel");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-emitidas/importarExcel" 
```



### HTTP Request
`POST facturas-emitidas/importarExcel`


<!-- END_114edefb81f71d13b22bf466dc9401f7 -->

<!-- START_eccc7710e0c387254a91ce53aebe2b4b -->
## facturas-emitidas/importarXML
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-emitidas/importarXML", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/importarXML");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-emitidas/importarXML" 
```



### HTTP Request
`POST facturas-emitidas/importarXML`


<!-- END_eccc7710e0c387254a91ce53aebe2b4b -->

<!-- START_0157faf291004ee23672f718b0bfe3b7 -->
## Muestra el formulario para emitir facturas

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/emitir-factura/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/emitir-factura/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/emitir-factura/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/emitir-factura/{tipoDocumento}`


<!-- END_0157faf291004ee23672f718b0bfe3b7 -->

<!-- START_63f54d30e6e5320824e9dd84abf14827 -->
## Muestra el formulario para emitir tiquetes electrónicos

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/emitir-sujeto-pasivo", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/emitir-sujeto-pasivo");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/emitir-sujeto-pasivo" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/emitir-sujeto-pasivo`


<!-- END_63f54d30e6e5320824e9dd84abf14827 -->

<!-- START_8f8520ab3ed917d519cea46e5a6c98bc -->
## Muestra el formulario para emitir tiquetes electrónicos

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/emitir-tiquete", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/emitir-tiquete");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/emitir-tiquete" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/emitir-tiquete`


<!-- END_8f8520ab3ed917d519cea46e5a6c98bc -->

<!-- START_7c8ef305cc2153899d0ab3cb26d01329 -->
## Envía la factura electrónica a Hacienda

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-emitidas/enviar-hacienda", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/enviar-hacienda");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-emitidas/enviar-hacienda" 
```



### HTTP Request
`POST facturas-emitidas/enviar-hacienda`


<!-- END_7c8ef305cc2153899d0ab3cb26d01329 -->

<!-- START_ae98956a6758be548305301904ce7df8 -->
## Despliega las facturas que requieren validación de códigos

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/validaciones", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/validaciones");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/validaciones" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/validaciones`


<!-- END_ae98956a6758be548305301904ce7df8 -->

<!-- START_501da2e58e032fe52be293fec1488448 -->
## facturas-emitidas/confirmar-validacion/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-emitidas/confirmar-validacion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/confirmar-validacion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-emitidas/confirmar-validacion/1" 
```



### HTTP Request
`PATCH facturas-emitidas/confirmar-validacion/{id}`


<!-- END_501da2e58e032fe52be293fec1488448 -->

<!-- START_e09d62d1f32848fc59c1dec722955566 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/autorizaciones", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/autorizaciones");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/autorizaciones" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/autorizaciones`


<!-- END_e09d62d1f32848fc59c1dec722955566 -->

<!-- START_9d4e4c947c324795f68c213bfb3e5331 -->
## facturas-emitidas/confirmar-autorizacion/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-emitidas/confirmar-autorizacion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/confirmar-autorizacion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-emitidas/confirmar-autorizacion/1" 
```



### HTTP Request
`PATCH facturas-emitidas/confirmar-autorizacion/{id}`


<!-- END_9d4e4c947c324795f68c213bfb3e5331 -->

<!-- START_b770fcd61386605c534334e831f8946e -->
## Envía la factura electrónica a Hacienda

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-emitidas/send", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/send");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-emitidas/send" 
```



### HTTP Request
`POST facturas-emitidas/send`


<!-- END_b770fcd61386605c534334e831f8946e -->

<!-- START_dede490789a362f84350eb5c21eb481f -->
## facturas-emitidas/anular/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-emitidas/anular/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/anular/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-emitidas/anular/1" 
```



### HTTP Request
`PATCH facturas-emitidas/anular/{id}`


<!-- END_dede490789a362f84350eb5c21eb481f -->

<!-- START_0cab89f6d20a3575336ccdd99305eae3 -->
## facturas-emitidas/download-pdf/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/download-pdf/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/download-pdf/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/download-pdf/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/download-pdf/{id}`


<!-- END_0cab89f6d20a3575336ccdd99305eae3 -->

<!-- START_488594987ffccefc577f0922239ffb35 -->
## facturas-emitidas/download-xml/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/download-xml/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/download-xml/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/download-xml/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/download-xml/{id}`


<!-- END_488594987ffccefc577f0922239ffb35 -->

<!-- START_725370fbb519852305de42d39665a051 -->
## facturas-emitidas/reenviar-email/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/reenviar-email/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/reenviar-email/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/reenviar-email/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/reenviar-email/{id}`


<!-- END_725370fbb519852305de42d39665a051 -->

<!-- START_288a14a3c1e4ffaeaaaea821b9d95467 -->
## facturas-emitidas/consult/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/consult/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/consult/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/consult/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/consult/{id}`


<!-- END_288a14a3c1e4ffaeaaaea821b9d95467 -->

<!-- START_a936f8179039a36fbc0830ca3da48c55 -->
## facturas-emitidas/query-invoice/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/query-invoice/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/query-invoice/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/query-invoice/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/query-invoice/{id}`


<!-- END_a936f8179039a36fbc0830ca3da48c55 -->

<!-- START_2d9b75fd5d1a544525f7ae2cebe69530 -->
## facturas-emitidas/actualizar-categorias
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-emitidas/actualizar-categorias", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/actualizar-categorias");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-emitidas/actualizar-categorias" 
```



### HTTP Request
`POST facturas-emitidas/actualizar-categorias`


<!-- END_2d9b75fd5d1a544525f7ae2cebe69530 -->

<!-- START_6e77697180e70f015dfee414f13ab0d0 -->
## Returns the required ajax data.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/invoices", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/invoices");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/invoices" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/invoices`


<!-- END_6e77697180e70f015dfee414f13ab0d0 -->

<!-- START_335a6f7b4e1b4bcb55a2cbb3630d4eb2 -->
## Returns the required ajax data.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/invoicesAuthorize", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/invoicesAuthorize");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/invoicesAuthorize" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/invoicesAuthorize`


<!-- END_335a6f7b4e1b4bcb55a2cbb3630d4eb2 -->

<!-- START_cedf31d819bd6f773a9129b0c557bbea -->
## Restore the specific item

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/facturas-emitidas/1/restore", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/1/restore");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/facturas-emitidas/1/restore" 
```



### HTTP Request
`PATCH facturas-emitidas/{id}/restore`


<!-- END_cedf31d819bd6f773a9129b0c557bbea -->

<!-- START_32a20ea8249006d7cab133911791df2c -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas`


<!-- END_32a20ea8249006d7cab133911791df2c -->

<!-- START_1d2a0afa4cdea5c48eaa3723b01f369d -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/create`


<!-- END_1d2a0afa4cdea5c48eaa3723b01f369d -->

<!-- START_0b05ca0ebd082ceb5e09fc2ea41213bf -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/facturas-emitidas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/facturas-emitidas" 
```



### HTTP Request
`POST facturas-emitidas`


<!-- END_0b05ca0ebd082ceb5e09fc2ea41213bf -->

<!-- START_a515521783ecae6645598f74dcf43617 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/{facturas_emitida}`


<!-- END_a515521783ecae6645598f74dcf43617 -->

<!-- START_a26aa2fb3581839486a759c7cf801f7a -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/facturas-emitidas/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/facturas-emitidas/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET facturas-emitidas/{facturas_emitida}/edit`


<!-- END_a26aa2fb3581839486a759c7cf801f7a -->

<!-- START_3055d1ce2bfd75654742ce5e4ea324ef -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/facturas-emitidas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/facturas-emitidas/1" 
```



### HTTP Request
`PUT facturas-emitidas/{facturas_emitida}`

`PATCH facturas-emitidas/{facturas_emitida}`


<!-- END_3055d1ce2bfd75654742ce5e4ea324ef -->

<!-- START_28d3c39f4ef0e1060bd3c183c14c9070 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/facturas-emitidas/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/facturas-emitidas/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/facturas-emitidas/1" 
```



### HTTP Request
`DELETE facturas-emitidas/{facturas_emitida}`


<!-- END_28d3c39f4ef0e1060bd3c183c14c9070 -->

#Controller - Libro contable

Funciones de BookController
<!-- START_1165ddf3787531b1a737732c898fb9ec -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/cierres", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/cierres");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/cierres" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET cierres`


<!-- END_1165ddf3787531b1a737732c898fb9ec -->

<!-- START_769927a0e9f5de88a16d4ea83a141e6e -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/cierres/cerrar-mes/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/cierres/cerrar-mes/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/cierres/cerrar-mes/1" 
```



### HTTP Request
`PATCH cierres/cerrar-mes/{id}`


<!-- END_769927a0e9f5de88a16d4ea83a141e6e -->

<!-- START_3996d0fe86d6c33104cdbd3f5a146c62 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/cierres/abrir-rectificacion/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/cierres/abrir-rectificacion/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/cierres/abrir-rectificacion/1" 
```



### HTTP Request
`PATCH cierres/abrir-rectificacion/{id}`


<!-- END_3996d0fe86d6c33104cdbd3f5a146c62 -->

<!-- START_f3e00d141df3a4ef001869e0286addb5 -->
## cierres/retenciones-tarjeta/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/cierres/retenciones-tarjeta/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/cierres/retenciones-tarjeta/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/cierres/retenciones-tarjeta/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET cierres/retenciones-tarjeta/{id}`


<!-- END_f3e00d141df3a4ef001869e0286addb5 -->

<!-- START_8f82bbff262468f79a37d9f68fb69c85 -->
## cierres/actualizar-retencion-tarjeta
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/cierres/actualizar-retencion-tarjeta", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/cierres/actualizar-retencion-tarjeta");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/cierres/actualizar-retencion-tarjeta" 
```



### HTTP Request
`POST cierres/actualizar-retencion-tarjeta`


<!-- END_8f82bbff262468f79a37d9f68fb69c85 -->

#Controller - Métodos de Pago

Funciones de PaymentMethodController.
<!-- START_88e23c00bb16b8172e0356b81bcad407 -->
## payment-methods/payment-method-create-view
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payment-methods/payment-method-create-view", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment-methods/payment-method-create-view");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payment-methods/payment-method-create-view" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET payment-methods/payment-method-create-view`


<!-- END_88e23c00bb16b8172e0356b81bcad407 -->

<!-- START_0b38fce9bf1b48bcd9a858f47dd57eed -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/payment-methods/payment-method-create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment-methods/payment-method-create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/payment-methods/payment-method-create" 
```



### HTTP Request
`POST payment-methods/payment-method-create`


<!-- END_0b38fce9bf1b48bcd9a858f47dd57eed -->

<!-- START_ae82f67977980053c655dfdd28b932d7 -->
## payment-methods/payment-method-token-update-view/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payment-methods/payment-method-token-update-view/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment-methods/payment-method-token-update-view/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payment-methods/payment-method-token-update-view/1" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET payment-methods/payment-method-token-update-view/{id}`


<!-- END_ae82f67977980053c655dfdd28b932d7 -->

<!-- START_c123db48266c5bdfbb615af47dd16241 -->
## payment-methods/payment-method-token-update
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/payment-methods/payment-method-token-update", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment-methods/payment-method-token-update");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/payment-methods/payment-method-token-update" 
```



### HTTP Request
`PATCH payment-methods/payment-method-token-update`


<!-- END_c123db48266c5bdfbb615af47dd16241 -->

<!-- START_970da5345263fb5a392f56e379a9a411 -->
## payment-methods/payment-method-token-delete/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/payment-methods/payment-method-token-delete/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment-methods/payment-method-token-delete/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/payment-methods/payment-method-token-delete/1" 
```



### HTTP Request
`DELETE payment-methods/payment-method-token-delete/{id}`


<!-- END_970da5345263fb5a392f56e379a9a411 -->

<!-- START_4a2a18dfea764bcdc61f9b09a9467815 -->
## payment-methods/payment-method-default-card-change/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/payment-methods/payment-method-default-card-change/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment-methods/payment-method-default-card-change/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/payment-methods/payment-method-default-card-change/1" 
```



### HTTP Request
`PATCH payment-methods/payment-method-default-card-change/{id}`


<!-- END_4a2a18dfea764bcdc61f9b09a9467815 -->

<!-- START_a57a22bb9b940169bc854813c2a2a22d -->
## api/paymentsMethods
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/paymentsMethods", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/paymentsMethods");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/paymentsMethods" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/paymentsMethods`


<!-- END_a57a22bb9b940169bc854813c2a2a22d -->

<!-- START_eb88d7679cdbb2d97de9fb37314a0b85 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payments-methods", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments-methods");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payments-methods" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET payments-methods`


<!-- END_eb88d7679cdbb2d97de9fb37314a0b85 -->

<!-- START_26deb540acf2ee2b53b43d4c7d65d37e -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payments-methods/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments-methods/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payments-methods/create" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET payments-methods/create`


<!-- END_26deb540acf2ee2b53b43d4c7d65d37e -->

<!-- START_cdce78e23d43fd212fc41dfc719799b1 -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/payments-methods", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments-methods");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/payments-methods" 
```



### HTTP Request
`POST payments-methods`


<!-- END_cdce78e23d43fd212fc41dfc719799b1 -->

<!-- START_2042d698e7989d84b2a8be6518788d44 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payments-methods/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments-methods/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payments-methods/1" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET payments-methods/{payments_method}`


<!-- END_2042d698e7989d84b2a8be6518788d44 -->

<!-- START_1161d913f8b7e480e2d557128d434eb7 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payments-methods/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments-methods/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payments-methods/1/edit" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET payments-methods/{payments_method}/edit`


<!-- END_1161d913f8b7e480e2d557128d434eb7 -->

<!-- START_751ca49f27d2e1d8037ccd6caf0cacfe -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/payments-methods/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments-methods/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/payments-methods/1" 
```



### HTTP Request
`PUT payments-methods/{payments_method}`

`PATCH payments-methods/{payments_method}`


<!-- END_751ca49f27d2e1d8037ccd6caf0cacfe -->

<!-- START_c6153fe4ca98e0d5642f0cf27ed19698 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/payments-methods/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments-methods/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/payments-methods/1" 
```



### HTTP Request
`DELETE payments-methods/{payments_method}`


<!-- END_c6153fe4ca98e0d5642f0cf27ed19698 -->

#Controller - Pagos

Funciones de PaymentController. Todos los request de pagos deberían pasar por aquí, pero el pago en sí debería ser en Payment Utils.
<!-- START_a55d364ebfdf923e0e810548301708f9 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payment/payment-crear", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment/payment-crear");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payment/payment-crear" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET payment/payment-crear`


<!-- END_a55d364ebfdf923e0e810548301708f9 -->

<!-- START_4762ba0a95b26c9cec25ab7606309552 -->
## payment/confirm-payment
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/payment/confirm-payment", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment/confirm-payment");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/payment/confirm-payment" 
```



### HTTP Request
`POST payment/confirm-payment`


<!-- END_4762ba0a95b26c9cec25ab7606309552 -->

<!-- START_cd4c5fe4b0ee5f048401f4e44f699a07 -->
## payment/pending-charges
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payment/pending-charges", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment/pending-charges");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payment/pending-charges" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET payment/pending-charges`


<!-- END_cd4c5fe4b0ee5f048401f4e44f699a07 -->

<!-- START_6a96d26e76287d80620e54b78874a501 -->
## payment/comprar-facturas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/payment/comprar-facturas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment/comprar-facturas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/payment/comprar-facturas" 
```



### HTTP Request
`POST payment/comprar-facturas`


<!-- END_6a96d26e76287d80620e54b78874a501 -->

<!-- START_e59031d7d8b87797f40c0b3c35ec693a -->
## payment/comprar-contabilidades
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/payment/comprar-contabilidades", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment/comprar-contabilidades");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/payment/comprar-contabilidades" 
```



### HTTP Request
`POST payment/comprar-contabilidades`


<!-- END_e59031d7d8b87797f40c0b3c35ec693a -->

<!-- START_1131be7971635329e0b879dbfbf284ae -->
## payment/seleccion-empresas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/payment/seleccion-empresas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment/seleccion-empresas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/payment/seleccion-empresas" 
```



### HTTP Request
`POST payment/seleccion-empresas`


<!-- END_1131be7971635329e0b879dbfbf284ae -->

<!-- START_ce8657e1510a9ea5e99cc66d562ee77c -->
## payment/pagar-cargo/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/payment/pagar-cargo/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payment/pagar-cargo/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/payment/pagar-cargo/1" 
```



### HTTP Request
`PATCH payment/pagar-cargo/{id}`


<!-- END_ce8657e1510a9ea5e99cc66d562ee77c -->

<!-- START_d6c74f99b225e5f2201ef212b86d2214 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/payments", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/payments");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/payments" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET api/payments`


<!-- END_d6c74f99b225e5f2201ef212b86d2214 -->

<!-- START_0af9fab88fd7253ac8ccee578f299141 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payments", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payments" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET payments`


<!-- END_0af9fab88fd7253ac8ccee578f299141 -->

<!-- START_486913730c9e1a25de563202cfde3d0d -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/payments/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/payments/1/edit" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET payments/{payment}/edit`


<!-- END_486913730c9e1a25de563202cfde3d0d -->

<!-- START_675c31be071655650d33175d7b59a834 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/payments/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/payments/1" 
```



### HTTP Request
`PUT payments/{payment}`

`PATCH payments/{payment}`


<!-- END_675c31be071655650d33175d7b59a834 -->

<!-- START_48199b0f0eae75aec5cf8f5dfc525b96 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/payments/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/payments/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/payments/1" 
```



### HTTP Request
`DELETE payments/{payment}`


<!-- END_48199b0f0eae75aec5cf8f5dfc525b96 -->

#Controller - Planes de suscripción

Funciones de SubscriptionPlanController.
<!-- START_e931a69e4b7a2a4c1a539a91cb144169 -->
## cambiar-plan
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/cambiar-plan", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/cambiar-plan");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/cambiar-plan" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET cambiar-plan`


<!-- END_e931a69e4b7a2a4c1a539a91cb144169 -->

<!-- START_d233a54807301bd0014007e69497a91d -->
## elegir-plan
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/elegir-plan", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/elegir-plan");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/elegir-plan" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET elegir-plan`


<!-- END_d233a54807301bd0014007e69497a91d -->

<!-- START_d0c372b25d524cd40d9905e68335757f -->
## periodo-pruebas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/periodo-pruebas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/periodo-pruebas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/periodo-pruebas" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET periodo-pruebas`


<!-- END_d0c372b25d524cd40d9905e68335757f -->

<!-- START_833a3abffc8187aa5a34aba7b8fa309a -->
## confirmar-plan
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/confirmar-plan", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/confirmar-plan");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/confirmar-plan" 
```



### HTTP Request
`POST confirmar-plan`


<!-- END_833a3abffc8187aa5a34aba7b8fa309a -->

<!-- START_15808c425afa28f01beb9ed0de331a79 -->
## confirmar-codigo/{codigo}/{precio}/{banco}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/confirmar-codigo/1/1/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/confirmar-codigo/1/1/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/confirmar-codigo/1/1/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET confirmar-codigo/{codigo}/{precio}/{banco}`


<!-- END_15808c425afa28f01beb9ed0de331a79 -->

<!-- START_1eddd7a3584272dde86d5bc63f6912a8 -->
## codigo-contador/{codigo}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/codigo-contador/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/codigo-contador/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/codigo-contador/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET codigo-contador/{codigo}`


<!-- END_1eddd7a3584272dde86d5bc63f6912a8 -->

<!-- START_e7b76f605b9c2965b9c4d1470d54650c -->
## suscripciones/confirmar-pruebas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/suscripciones/confirmar-pruebas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/suscripciones/confirmar-pruebas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/suscripciones/confirmar-pruebas" 
```



### HTTP Request
`POST suscripciones/confirmar-pruebas`


<!-- END_e7b76f605b9c2965b9c4d1470d54650c -->

<!-- START_9f57c8feb2bc8523b8f95298930e882b -->
## private/all
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/private/all", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/private/all");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/private/all" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET private/all`


<!-- END_9f57c8feb2bc8523b8f95298930e882b -->

<!-- START_f49e43c8d9d009e8794ca2084c51c391 -->
## private/exportar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/private/exportar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/private/exportar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/private/exportar" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET private/exportar`


<!-- END_f49e43c8d9d009e8794ca2084c51c391 -->

#Controller - Productos

Funciones de ProductController
<!-- START_24a7c3be4ecf7613afb2d73df29ec9e4 -->
## productos/importar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/productos/importar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos/importar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/productos/importar" 
```



### HTTP Request
`POST productos/importar`


<!-- END_24a7c3be4ecf7613afb2d73df29ec9e4 -->

<!-- START_86e0ac5d4f8ce9853bc22fd08f2a0109 -->
## Returns the required ajax data.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/products", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/products");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/products" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/products`


<!-- END_86e0ac5d4f8ce9853bc22fd08f2a0109 -->

<!-- START_950d48932fe3c31e34a879d16cff7732 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/productos/1/restore", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos/1/restore");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/productos/1/restore" 
```



### HTTP Request
`PATCH productos/{id}/restore`


<!-- END_950d48932fe3c31e34a879d16cff7732 -->

<!-- START_258da7584359f2059b8d3fe0d92b1f36 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/productos", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/productos" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET productos`


<!-- END_258da7584359f2059b8d3fe0d92b1f36 -->

<!-- START_305ae4c2c5e7f6b212e6c8658de65456 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/productos/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/productos/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET productos/create`


<!-- END_305ae4c2c5e7f6b212e6c8658de65456 -->

<!-- START_63f91e86f6b43bbe011af31d9ce6ed29 -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/productos", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/productos" 
```



### HTTP Request
`POST productos`


<!-- END_63f91e86f6b43bbe011af31d9ce6ed29 -->

<!-- START_f7eb4f3b885d844c7709a00260f45462 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/productos/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/productos/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET productos/{producto}`


<!-- END_f7eb4f3b885d844c7709a00260f45462 -->

<!-- START_72ff3e1bd785851307e73b52c2b509fc -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/productos/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/productos/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET productos/{producto}/edit`


<!-- END_72ff3e1bd785851307e73b52c2b509fc -->

<!-- START_939eb4e8965120562384a0b34ae2baf1 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/productos/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/productos/1" 
```



### HTTP Request
`PUT productos/{producto}`

`PATCH productos/{producto}`


<!-- END_939eb4e8965120562384a0b34ae2baf1 -->

<!-- START_1a75748a179d5037e8bf6e7f2d849f38 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/productos/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/productos/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/productos/1" 
```



### HTTP Request
`DELETE productos/{producto}`


<!-- END_1a75748a179d5037e8bf6e7f2d849f38 -->

<!-- START_fcdf2da1997bd4d8d126f782bc06524c -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/products", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/products");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/products" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET products`


<!-- END_fcdf2da1997bd4d8d126f782bc06524c -->

<!-- START_f991d4ee536427e80930fcd66f55be22 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/products/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/products/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/products/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET products/create`


<!-- END_f991d4ee536427e80930fcd66f55be22 -->

<!-- START_e69e3804fa0af1eb523e480d661362b7 -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/products", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/products");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/products" 
```



### HTTP Request
`POST products`


<!-- END_e69e3804fa0af1eb523e480d661362b7 -->

<!-- START_6af8316bb6d4a4dac25704299765b459 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/products/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/products/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/products/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET products/{product}`


<!-- END_6af8316bb6d4a4dac25704299765b459 -->

<!-- START_8c5bdcaf79c3101b1f381b7fe35abe7d -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/products/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/products/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/products/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET products/{product}/edit`


<!-- END_8c5bdcaf79c3101b1f381b7fe35abe7d -->

<!-- START_3d6f3cbb4f154b7da4faac30c3380d51 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/products/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/products/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/products/1" 
```



### HTTP Request
`PUT products/{product}`

`PATCH products/{product}`


<!-- END_3d6f3cbb4f154b7da4faac30c3380d51 -->

<!-- START_9dc19a575e78a6169cad6bda8a2186de -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/products/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/products/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/products/1" 
```



### HTTP Request
`DELETE products/{product}`


<!-- END_9dc19a575e78a6169cad6bda8a2186de -->

<!-- START_82d6b7b21baefcfafb916f718ef53184 -->
## getproduct
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/getproduct", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/getproduct");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/getproduct" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET getproduct`


<!-- END_82d6b7b21baefcfafb916f718ef53184 -->

#Controller - Proveedores

Funciones de ProviderController
<!-- START_4c52ce4ca9be54c1730375faf60fded8 -->
## proveedores/exportar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/proveedores/exportar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/exportar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/proveedores/exportar" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET proveedores/exportar`


<!-- END_4c52ce4ca9be54c1730375faf60fded8 -->

<!-- START_74934d7b9123413c8b10a3d39574663c -->
## proveedores/importar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/proveedores/importar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/importar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/proveedores/importar" 
```



### HTTP Request
`POST proveedores/importar`


<!-- END_74934d7b9123413c8b10a3d39574663c -->

<!-- START_551b157bc4123031bbe399ae98553f60 -->
## Returns the required ajax data.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/api/providers", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/api/providers");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/api/providers" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/providers`


<!-- END_551b157bc4123031bbe399ae98553f60 -->

<!-- START_2184328cc500eceaee9e3db30e14bb8f -->
## Restore the specific item

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/proveedores/1/restore", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/1/restore");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/proveedores/1/restore" 
```



### HTTP Request
`PATCH proveedores/{id}/restore`


<!-- END_2184328cc500eceaee9e3db30e14bb8f -->

<!-- START_d5b1152051f1e9ee30c34aa9fcb13252 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/proveedores", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/proveedores" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET proveedores`


<!-- END_d5b1152051f1e9ee30c34aa9fcb13252 -->

<!-- START_aedda72ae6084b93a38cbfd11cf91b09 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/proveedores/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/proveedores/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET proveedores/create`


<!-- END_aedda72ae6084b93a38cbfd11cf91b09 -->

<!-- START_6f725e1d21f4748120a8fffe644a8b2c -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/proveedores", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/proveedores" 
```



### HTTP Request
`POST proveedores`


<!-- END_6f725e1d21f4748120a8fffe644a8b2c -->

<!-- START_a5de1f34296d6ca98696ab16a07b9275 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/proveedores/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/proveedores/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET proveedores/{proveedore}`


<!-- END_a5de1f34296d6ca98696ab16a07b9275 -->

<!-- START_7ce5baa47189d3563253d942df735a85 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/proveedores/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/proveedores/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET proveedores/{proveedore}/edit`


<!-- END_7ce5baa47189d3563253d942df735a85 -->

<!-- START_096a730e9714a3cc05ebeed4392cd621 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/proveedores/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/proveedores/1" 
```



### HTTP Request
`PUT proveedores/{proveedore}`

`PATCH proveedores/{proveedore}`


<!-- END_096a730e9714a3cc05ebeed4392cd621 -->

<!-- START_0523657b8571c3e321a70d3e4991aa9e -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/proveedores/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/proveedores/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/proveedores/1" 
```



### HTTP Request
`DELETE proveedores/{proveedore}`


<!-- END_0523657b8571c3e321a70d3e4991aa9e -->

#Controller - Reportes

Funciones de ReportsController
<!-- START_53be1e9e10a08458929a2e0ea70ddb86 -->
## /
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("//", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET /`


<!-- END_53be1e9e10a08458929a2e0ea70ddb86 -->

<!-- START_30059a09ef3f0284c40e4d06962ce08d -->
## dashboard
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/dashboard", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/dashboard");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/dashboard" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET dashboard`


<!-- END_30059a09ef3f0284c40e4d06962ce08d -->

<!-- START_569c0f7a5b813880203a763f1bb88645 -->
## reportes
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/reportes", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/reportes" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET reportes`


<!-- END_569c0f7a5b813880203a763f1bb88645 -->

<!-- START_7176d4dff9f7fa75b8c88323a7c6aea5 -->
## reportes/reporte-dashboard
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/reportes/reporte-dashboard", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/reporte-dashboard");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/reportes/reporte-dashboard" 
```



### HTTP Request
`POST reportes/reporte-dashboard`


<!-- END_7176d4dff9f7fa75b8c88323a7c6aea5 -->

<!-- START_45456535cc7cf579767516cc948f7a11 -->
## reportes/cuentas-contables
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/reportes/cuentas-contables", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/cuentas-contables");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/reportes/cuentas-contables" 
```



### HTTP Request
`POST reportes/cuentas-contables`


<!-- END_45456535cc7cf579767516cc948f7a11 -->

<!-- START_f81bbd05fa8cd43f6fb59eee1932c717 -->
## reportes/resumen-ejecutivo
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/reportes/resumen-ejecutivo", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/resumen-ejecutivo");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/reportes/resumen-ejecutivo" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET reportes/resumen-ejecutivo`


<!-- END_f81bbd05fa8cd43f6fb59eee1932c717 -->

<!-- START_83b6307027cf37e80c5433713cbfe2ae -->
## reportes/detalle-debito
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/reportes/detalle-debito", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/detalle-debito");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/reportes/detalle-debito" 
```



### HTTP Request
`POST reportes/detalle-debito`


<!-- END_83b6307027cf37e80c5433713cbfe2ae -->

<!-- START_1573f571ef9001de2c0971c9fe8ca05c -->
## reportes/detalle-credito
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/reportes/detalle-credito", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/detalle-credito");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/reportes/detalle-credito" 
```



### HTTP Request
`POST reportes/detalle-credito`


<!-- END_1573f571ef9001de2c0971c9fe8ca05c -->

<!-- START_0a2c9e50fd88258b245ee1b87da528e5 -->
## reportes/libro-ventas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/reportes/libro-ventas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/libro-ventas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/reportes/libro-ventas" 
```



### HTTP Request
`POST reportes/libro-ventas`


<!-- END_0a2c9e50fd88258b245ee1b87da528e5 -->

<!-- START_6dbff886dbeac91fbd75a354a3dfd97a -->
## reportes/libro-compras
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/reportes/libro-compras", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/libro-compras");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/reportes/libro-compras" 
```



### HTTP Request
`POST reportes/libro-compras`


<!-- END_6dbff886dbeac91fbd75a354a3dfd97a -->

<!-- START_85580034a82c79d892f16b9ca7ab72ee -->
## reportes/borrador-iva
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/reportes/borrador-iva", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/reportes/borrador-iva");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/reportes/borrador-iva" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET reportes/borrador-iva`


<!-- END_85580034a82c79d892f16b9ca7ab72ee -->

#Controller - Usuarios

Funciones de UserController.
<!-- START_d3f8797935443a7224b2b8360d7e9021 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/perfil", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/perfil");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/perfil" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/perfil`


<!-- END_d3f8797935443a7224b2b8360d7e9021 -->

<!-- START_6e4661329c76e5659fbfb16fb7e0d14f -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/usuario/update-perfil", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/update-perfil");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/usuario/update-perfil" 
```



### HTTP Request
`PATCH usuario/update-perfil`


<!-- END_6e4661329c76e5659fbfb16fb7e0d14f -->

<!-- START_70a13b6ebe109eb8aa6a21b3b74aa5cf -->
## usuario/admin-edit/{email}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/admin-edit/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/admin-edit/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/admin-edit/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/admin-edit/{email}`


<!-- END_70a13b6ebe109eb8aa6a21b3b74aa5cf -->

<!-- START_8f23d70a91989ac9b77a944265e5168e -->
## usuario/update-admin/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/usuario/update-admin/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/update-admin/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/usuario/update-admin/1" 
```



### HTTP Request
`PATCH usuario/update-admin/{id}`


<!-- END_8f23d70a91989ac9b77a944265e5168e -->

<!-- START_bc457fe22976998dd0471e105dfd0cc5 -->
## Lleva a formulario para cambiar password

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/seguridad", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/seguridad");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/seguridad" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/seguridad`


<!-- END_bc457fe22976998dd0471e105dfd0cc5 -->

<!-- START_a99698dc961f1ae52ca96dc849e0e2bf -->
## usuario/planes
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/planes", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/planes");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/planes" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/planes`


<!-- END_a99698dc961f1ae52ca96dc849e0e2bf -->

<!-- START_f3aedc3d1fc6875d6b194ab66ce421f0 -->
## usuario/empresas
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/empresas", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/empresas");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/empresas" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/empresas`


<!-- END_f3aedc3d1fc6875d6b194ab66ce421f0 -->

<!-- START_debe49b7553610dfcbcff545e7e6f41c -->
## usuario/usuarios-invitados
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/usuarios-invitados", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/usuarios-invitados");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/usuarios-invitados" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/usuarios-invitados`


<!-- END_debe49b7553610dfcbcff545e7e6f41c -->

<!-- START_a344886056b411ae555e178e6917c28e -->
## Devuelve una llave JWT para ser usada por Zendesk y así validar al usuario.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/zendesk-jwt", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/zendesk-jwt");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/zendesk-jwt" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/zendesk-jwt`


<!-- END_a344886056b411ae555e178e6917c28e -->

<!-- START_ed23069d62fee1cc3112f269da372250 -->
## Actualiza contraseña de usuario logueado

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/usuario/update-password/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/update-password/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/usuario/update-password/1" 
```



### HTTP Request
`PATCH usuario/update-password/{id}`


<!-- END_ed23069d62fee1cc3112f269da372250 -->

<!-- START_a7cc8813045f3f03606cdb6e637baf24 -->
## usuario/update-user-tutorial
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/usuario/update-user-tutorial", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/update-user-tutorial");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/usuario/update-user-tutorial" 
```



### HTTP Request
`POST usuario/update-user-tutorial`


<!-- END_a7cc8813045f3f03606cdb6e637baf24 -->

<!-- START_c56d1584e44f7cd44927162f0de9577f -->
## usuario/cancelar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/cancelar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/cancelar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/cancelar" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/cancelar`


<!-- END_c56d1584e44f7cd44927162f0de9577f -->

<!-- START_c0cae7a2b741e0e613d8ce9682d0dd06 -->
## usuario/update-cancelar
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/usuario/update-cancelar", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/update-cancelar");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/usuario/update-cancelar" 
```



### HTTP Request
`PATCH usuario/update-cancelar`


<!-- END_c0cae7a2b741e0e613d8ce9682d0dd06 -->

<!-- START_535f1cf52477f3261962a2927482d9aa -->
## usuario/compra-contabilidades
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/compra-contabilidades", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/compra-contabilidades");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/compra-contabilidades" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET usuario/compra-contabilidades`


<!-- END_535f1cf52477f3261962a2927482d9aa -->

<!-- START_89966bfb9ab533cc3249b91a9090d3dc -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/users", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/users");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/users" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET users`


<!-- END_89966bfb9ab533cc3249b91a9090d3dc -->

<!-- START_04094f136cb91c117bde084191e6859d -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/users/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/users/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/users/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET users/create`


<!-- END_04094f136cb91c117bde084191e6859d -->

<!-- START_57a8a4ba671355511e22780b1b63690e -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/users", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/users");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/users" 
```



### HTTP Request
`POST users`


<!-- END_57a8a4ba671355511e22780b1b63690e -->

<!-- START_5693ac2f2e21af3ebc471cd5a6244460 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/users/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/users/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/users/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET users/{user}`


<!-- END_5693ac2f2e21af3ebc471cd5a6244460 -->

<!-- START_9c6e6c2d3215b1ba7d13468e7cd95e62 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/users/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/users/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/users/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET users/{user}/edit`


<!-- END_9c6e6c2d3215b1ba7d13468e7cd95e62 -->

<!-- START_7fe085c671e1b3d51e86136538b1d63f -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/users/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/users/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/users/1" 
```



### HTTP Request
`PUT users/{user}`

`PATCH users/{user}`


<!-- END_7fe085c671e1b3d51e86136538b1d63f -->

<!-- START_a948aef61c80bf96137d023464fde21f -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/users/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/users/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/users/1" 
```



### HTTP Request
`DELETE users/{user}`


<!-- END_a948aef61c80bf96137d023464fde21f -->

<!-- START_da7cffb8a9e0f3930d91615614209c02 -->
## admin/impersonate/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/admin/impersonate/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/admin/impersonate/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/admin/impersonate/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET admin/impersonate/{id}`


<!-- END_da7cffb8a9e0f3930d91615614209c02 -->

<!-- START_fefa202dddca95c56a16918a054a8043 -->
## admin/leave
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/admin/leave", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/admin/leave");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/admin/leave" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET admin/leave`


<!-- END_fefa202dddca95c56a16918a054a8043 -->

#Controller - Wizard

Funciones de WizardController.
<!-- START_5547a4603557e20f84ed637976dc7a7e -->
## wizard
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/wizard", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/wizard");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/wizard" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET wizard`


<!-- END_5547a4603557e20f84ed637976dc7a7e -->

<!-- START_309ec914fd170d0e2f0766672e18329a -->
## Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/editar-totales-2018", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/editar-totales-2018");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/editar-totales-2018" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET editar-totales-2018`


<!-- END_309ec914fd170d0e2f0766672e18329a -->

<!-- START_8e072dc6574b28fdc3c5ce10612282c1 -->
## Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/update-totales-2018", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/update-totales-2018");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/update-totales-2018" 
```



### HTTP Request
`POST update-totales-2018`


<!-- END_8e072dc6574b28fdc3c5ce10612282c1 -->

<!-- START_acc771b322be0ae667e040e8e6413cad -->
## Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/update-wizard", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/update-wizard");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/update-wizard" 
```



### HTTP Request
`POST update-wizard`


<!-- END_acc771b322be0ae667e040e8e6413cad -->

<!-- START_200298f689182411c92add6dcb6f533e -->
## Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/store-wizard", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/store-wizard");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/store-wizard" 
```



### HTTP Request
`POST store-wizard`


<!-- END_200298f689182411c92add6dcb6f533e -->

#Deprecados

Funciones de PlanController. Ahora se usa el SubscriptionPlanController
<!-- START_1f59f4e41942dfc42e4c7374332efe8c -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/plans", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/plans" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET plans`


<!-- END_1f59f4e41942dfc42e4c7374332efe8c -->

<!-- START_0191e3663cf3818be1529026e62c30b4 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/plans/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/plans/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET plans/create`


<!-- END_0191e3663cf3818be1529026e62c30b4 -->

<!-- START_71336454462bb18c256676d4b31593f4 -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/plans", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/plans" 
```



### HTTP Request
`POST plans`


<!-- END_71336454462bb18c256676d4b31593f4 -->

<!-- START_1da53ac3909854eb66243dcf8556d21d -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/plans/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/plans/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET plans/{plan}`


<!-- END_1da53ac3909854eb66243dcf8556d21d -->

<!-- START_0e756ea401b3d73a1367849a86725433 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/plans/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/plans/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET plans/{plan}/edit`


<!-- END_0e756ea401b3d73a1367849a86725433 -->

<!-- START_a735e5076d9def1bdb7bb581a4c2b2ca -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/plans/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/plans/1" 
```



### HTTP Request
`PUT plans/{plan}`

`PATCH plans/{plan}`


<!-- END_a735e5076d9def1bdb7bb581a4c2b2ca -->

<!-- START_8de6ed4d24963f2414edacd5ca603fb1 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/plans/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/plans/1" 
```



### HTTP Request
`DELETE plans/{plan}`


<!-- END_8de6ed4d24963f2414edacd5ca603fb1 -->

<!-- START_9e3e864eaa4c79c410a87db2c9532ecd -->
## plans/cancel-plan/{planNo}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch("/plans/cancel-plan/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/cancel-plan/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PATCH",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PATCH "/plans/cancel-plan/1" 
```



### HTTP Request
`PATCH plans/cancel-plan/{planNo}`


<!-- END_9e3e864eaa4c79c410a87db2c9532ecd -->

<!-- START_2f98a1294ea3be5d43b4c178582dc80e -->
## plans/confirm-cancel-plan/{token}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/plans/confirm-cancel-plan/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/confirm-cancel-plan/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/plans/confirm-cancel-plan/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET plans/confirm-cancel-plan/{token}`


<!-- END_2f98a1294ea3be5d43b4c178582dc80e -->

<!-- START_e93fd56fab6df6fded86f5d0834395e3 -->
## show-plans
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/show-plans", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/show-plans");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/show-plans" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET show-plans`


<!-- END_e93fd56fab6df6fded86f5d0834395e3 -->

<!-- START_565fa0cba8625b3907961cbd456a5b7e -->
## purchase
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/purchase", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/purchase");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/purchase" 
```



### HTTP Request
`POST purchase`


<!-- END_565fa0cba8625b3907961cbd456a5b7e -->

<!-- START_8458327246fb55d95150a19db9fc1c8f -->
## plans/switch-plan/{plan}/{newPlan}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/plans/switch-plan/1/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/plans/switch-plan/1/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/plans/switch-plan/1/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET plans/switch-plan/{plan}/{newPlan}`


<!-- END_8458327246fb55d95150a19db9fc1c8f -->

#Deprecados

Funciones de RoleController
<!-- START_de5f4fc289db0f6abcc69bfdae1b0989 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/roles", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/roles");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/roles" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET roles`


<!-- END_de5f4fc289db0f6abcc69bfdae1b0989 -->

<!-- START_ebd39f34dc5264d8b3f5f89531bf4193 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/roles/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/roles/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/roles/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET roles/create`


<!-- END_ebd39f34dc5264d8b3f5f89531bf4193 -->

<!-- START_3e294c23aaeb6a3ca69b8ce11849f5e9 -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/roles", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/roles");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/roles" 
```



### HTTP Request
`POST roles`


<!-- END_3e294c23aaeb6a3ca69b8ce11849f5e9 -->

<!-- START_c4c5f2e255b5472d9806bc0533de5c05 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/roles/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/roles/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/roles/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET roles/{role}`


<!-- END_c4c5f2e255b5472d9806bc0533de5c05 -->

<!-- START_3c827a40c367b7d634287202870ebe68 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/roles/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/roles/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/roles/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET roles/{role}/edit`


<!-- END_3c827a40c367b7d634287202870ebe68 -->

<!-- START_2711d634f18127bafef5f111a2f402d4 -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/roles/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/roles/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/roles/1" 
```



### HTTP Request
`PUT roles/{role}`

`PATCH roles/{role}`


<!-- END_2711d634f18127bafef5f111a2f402d4 -->

<!-- START_990e30ddaebf1e4a496f367b7ceb8dd9 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/roles/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/roles/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/roles/1" 
```



### HTTP Request
`DELETE roles/{role}`


<!-- END_990e30ddaebf1e4a496f367b7ceb8dd9 -->

#Otros
<!-- START_7ebdd0ac8b3cd321e05382d1c06cd0b1 -->
## Get the key performance stats for the dashboard.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/stats", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/stats");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/stats" 
```


> Example response (200):

```json
{
    "jobsPerMinute": 0,
    "processes": 0,
    "queueWithMaxRuntime": null,
    "queueWithMaxThroughput": null,
    "recentlyFailed": 0,
    "recentJobs": 0,
    "status": "inactive",
    "wait": [],
    "periods": {
        "recentJobs": 60,
        "recentlyFailed": 10080
    }
}
```

### HTTP Request
`GET horizon/api/stats`


<!-- END_7ebdd0ac8b3cd321e05382d1c06cd0b1 -->

<!-- START_5abc89804e68469f8260c0ded520f59c -->
## Get the current queue workload for the application.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/workload", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/workload");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/workload" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/workload`


<!-- END_5abc89804e68469f8260c0ded520f59c -->

<!-- START_7d6f8da3e735f9175246fbab4b37610c -->
## Get all of the master supervisors and their underlying supervisors.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/masters", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/masters");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/masters" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/masters`


<!-- END_7d6f8da3e735f9175246fbab4b37610c -->

<!-- START_3a653cb977489e73ed8798e5705defbf -->
## Get all of the monitored tags and their job counts.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/monitoring", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/monitoring");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/monitoring" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/monitoring`


<!-- END_3a653cb977489e73ed8798e5705defbf -->

<!-- START_970935b1e560143fd003dd90a6f0b7b0 -->
## Start monitoring the given tag.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/horizon/api/monitoring", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/monitoring");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/horizon/api/monitoring" 
```



### HTTP Request
`POST horizon/api/monitoring`


<!-- END_970935b1e560143fd003dd90a6f0b7b0 -->

<!-- START_abd3993e15d364e7a2c79c9caa73a862 -->
## Paginate the jobs for a given tag.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/monitoring/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/monitoring/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/monitoring/1" 
```


> Example response (200):

```json
{
    "jobs": [],
    "total": 0
}
```

### HTTP Request
`GET horizon/api/monitoring/{tag}`


<!-- END_abd3993e15d364e7a2c79c9caa73a862 -->

<!-- START_9f62e45bc2a894b92554c1406f487f03 -->
## Stop monitoring the given tag.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/horizon/api/monitoring/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/monitoring/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/horizon/api/monitoring/1" 
```



### HTTP Request
`DELETE horizon/api/monitoring/{tag}`


<!-- END_9f62e45bc2a894b92554c1406f487f03 -->

<!-- START_9808e9d7d776f039d57c72f052e6e8cc -->
## Get all of the measured jobs.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/metrics/jobs", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/metrics/jobs");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/metrics/jobs" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/jobs`


<!-- END_9808e9d7d776f039d57c72f052e6e8cc -->

<!-- START_dbb28dc188d668f7fa836ee5bc43e243 -->
## Get metrics for a given job.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/metrics/jobs/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/metrics/jobs/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/metrics/jobs/1" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/jobs/{id}`


<!-- END_dbb28dc188d668f7fa836ee5bc43e243 -->

<!-- START_ca0a10e3b27a3c5820831f79ab403f78 -->
## Get all of the measured queues.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/metrics/queues", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/metrics/queues");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/metrics/queues" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/queues`


<!-- END_ca0a10e3b27a3c5820831f79ab403f78 -->

<!-- START_7a3c56bda1e4b728cf5a5691ee989766 -->
## Get metrics for a given queue.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/metrics/queues/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/metrics/queues/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/metrics/queues/1" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/queues/{id}`


<!-- END_7a3c56bda1e4b728cf5a5691ee989766 -->

<!-- START_c34fa16bca5eb044bd9b7d7643c3376a -->
## Get all of the recent jobs.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/jobs/recent", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/jobs/recent");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/jobs/recent" 
```


> Example response (200):

```json
{
    "jobs": [],
    "total": 0
}
```

### HTTP Request
`GET horizon/api/jobs/recent`


<!-- END_c34fa16bca5eb044bd9b7d7643c3376a -->

<!-- START_73a5f0771b8fdd710e2b547f24f1b308 -->
## Get all of the failed jobs.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/jobs/failed", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/jobs/failed");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/jobs/failed" 
```


> Example response (200):

```json
{
    "jobs": [],
    "total": 0
}
```

### HTTP Request
`GET horizon/api/jobs/failed`


<!-- END_73a5f0771b8fdd710e2b547f24f1b308 -->

<!-- START_25959bfc2e37e26b5875453cbf717c3f -->
## Get a failed job instance.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/api/jobs/failed/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/jobs/failed/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/api/jobs/failed/1" 
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/jobs/failed/{id}`


<!-- END_25959bfc2e37e26b5875453cbf717c3f -->

<!-- START_b69e44e22af794a2060e89edd04f0600 -->
## Retry a failed job.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/horizon/api/jobs/retry/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/api/jobs/retry/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/horizon/api/jobs/retry/1" 
```



### HTTP Request
`POST horizon/api/jobs/retry/{id}`


<!-- END_b69e44e22af794a2060e89edd04f0600 -->

<!-- START_fb7b7b4614d0392062e423beed14f31f -->
## Single page application catch-all route.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/horizon/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/horizon/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/horizon/1" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET horizon/{view?}`


<!-- END_fb7b7b4614d0392062e423beed14f31f -->

<!-- START_b8dc05d6f509d0ebfee1b9ecb497dd5d -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/mail", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/mail");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/mail" 
```



### HTTP Request
`POST telescope/telescope-api/mail`


<!-- END_b8dc05d6f509d0ebfee1b9ecb497dd5d -->

<!-- START_46cef718b6543d87392ef9d885c32074 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/mail/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/mail/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/mail/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/mail/{telescopeEntryId}`


<!-- END_46cef718b6543d87392ef9d885c32074 -->

<!-- START_f437131ff649585d9e9028707aff6586 -->
## Get the HTML content of the given email.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/mail/1/preview", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/mail/1/preview");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/mail/1/preview" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/mail/{telescopeEntryId}/preview`


<!-- END_f437131ff649585d9e9028707aff6586 -->

<!-- START_e78ffa4a993bd280d8bbcfeece76333c -->
## Download the Eml content of the email.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/mail/1/download", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/mail/1/download");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/mail/1/download" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/mail/{telescopeEntryId}/download`


<!-- END_e78ffa4a993bd280d8bbcfeece76333c -->

<!-- START_6d8be72a837a4251b2068584bf71a0da -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/exceptions", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/exceptions");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/exceptions" 
```



### HTTP Request
`POST telescope/telescope-api/exceptions`


<!-- END_6d8be72a837a4251b2068584bf71a0da -->

<!-- START_28f8ee28d6619d49fbc6f1e955d34728 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/exceptions/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/exceptions/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/exceptions/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/exceptions/{telescopeEntryId}`


<!-- END_28f8ee28d6619d49fbc6f1e955d34728 -->

<!-- START_e08a570bb93827d8b6603d1fb9f2b93d -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/dumps", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/dumps");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/dumps" 
```



### HTTP Request
`POST telescope/telescope-api/dumps`


<!-- END_e08a570bb93827d8b6603d1fb9f2b93d -->

<!-- START_eab4e91ba32edb6580a8ed2632fca255 -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/logs", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/logs");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/logs" 
```



### HTTP Request
`POST telescope/telescope-api/logs`


<!-- END_eab4e91ba32edb6580a8ed2632fca255 -->

<!-- START_2a59c20c5796d5d492133c3e8bcd1d34 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/logs/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/logs/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/logs/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/logs/{telescopeEntryId}`


<!-- END_2a59c20c5796d5d492133c3e8bcd1d34 -->

<!-- START_4ef84633ff5ac0c8df8f542d6d19a6d8 -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/notifications", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/notifications");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/notifications" 
```



### HTTP Request
`POST telescope/telescope-api/notifications`


<!-- END_4ef84633ff5ac0c8df8f542d6d19a6d8 -->

<!-- START_6395c2300a578d2ff312b0a7381521d3 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/notifications/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/notifications/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/notifications/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/notifications/{telescopeEntryId}`


<!-- END_6395c2300a578d2ff312b0a7381521d3 -->

<!-- START_9efcd66e514d1f19224e0b2e27468db9 -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/jobs", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/jobs");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/jobs" 
```



### HTTP Request
`POST telescope/telescope-api/jobs`


<!-- END_9efcd66e514d1f19224e0b2e27468db9 -->

<!-- START_d61020a49164fb68d91b4970072570bc -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/jobs/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/jobs/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/jobs/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/jobs/{telescopeEntryId}`


<!-- END_d61020a49164fb68d91b4970072570bc -->

<!-- START_15f74a948a5d813e235ca5d2ff32c892 -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/events", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/events");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/events" 
```



### HTTP Request
`POST telescope/telescope-api/events`


<!-- END_15f74a948a5d813e235ca5d2ff32c892 -->

<!-- START_cb367d2f23d97116a97d78faaae81e1f -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/events/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/events/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/events/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/events/{telescopeEntryId}`


<!-- END_cb367d2f23d97116a97d78faaae81e1f -->

<!-- START_7ec6f1ddc70142ea1f60a799307f4f4a -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/gates", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/gates");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/gates" 
```



### HTTP Request
`POST telescope/telescope-api/gates`


<!-- END_7ec6f1ddc70142ea1f60a799307f4f4a -->

<!-- START_f7261ef8c2d8def355b1be63596b9455 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/gates/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/gates/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/gates/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/gates/{telescopeEntryId}`


<!-- END_f7261ef8c2d8def355b1be63596b9455 -->

<!-- START_104836e7045badb7761960560308f50c -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/cache", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/cache");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/cache" 
```



### HTTP Request
`POST telescope/telescope-api/cache`


<!-- END_104836e7045badb7761960560308f50c -->

<!-- START_a9794a212e7bc0d3bf15e26acdf7685e -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/cache/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/cache/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/cache/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/cache/{telescopeEntryId}`


<!-- END_a9794a212e7bc0d3bf15e26acdf7685e -->

<!-- START_f5bd8c7cfbf94e2facea5e422716e828 -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/queries", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/queries");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/queries" 
```



### HTTP Request
`POST telescope/telescope-api/queries`


<!-- END_f5bd8c7cfbf94e2facea5e422716e828 -->

<!-- START_126e06b566849f9991002afb0b34dd11 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/queries/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/queries/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/queries/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/queries/{telescopeEntryId}`


<!-- END_126e06b566849f9991002afb0b34dd11 -->

<!-- START_2a0be87abbf498979bd57a79b0dbf8f5 -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/models", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/models");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/models" 
```



### HTTP Request
`POST telescope/telescope-api/models`


<!-- END_2a0be87abbf498979bd57a79b0dbf8f5 -->

<!-- START_b9f7de78bc63ac228648c9086ebad48c -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/models/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/models/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/models/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/models/{telescopeEntryId}`


<!-- END_b9f7de78bc63ac228648c9086ebad48c -->

<!-- START_07a4603df131c6daad826b2f7f2b009c -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/requests", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/requests");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/requests" 
```



### HTTP Request
`POST telescope/telescope-api/requests`


<!-- END_07a4603df131c6daad826b2f7f2b009c -->

<!-- START_deedcbe0d49ee78b6b0211b6382a6ad3 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/requests/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/requests/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/requests/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/requests/{telescopeEntryId}`


<!-- END_deedcbe0d49ee78b6b0211b6382a6ad3 -->

<!-- START_1b00812e02a8ebcab44bee37f710e21a -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/commands", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/commands");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/commands" 
```



### HTTP Request
`POST telescope/telescope-api/commands`


<!-- END_1b00812e02a8ebcab44bee37f710e21a -->

<!-- START_630361bcb61ef50767ab0e87078233ed -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/commands/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/commands/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/commands/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/commands/{telescopeEntryId}`


<!-- END_630361bcb61ef50767ab0e87078233ed -->

<!-- START_98a5c94ff64c824fc9202e5025af17b8 -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/schedule", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/schedule");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/schedule" 
```



### HTTP Request
`POST telescope/telescope-api/schedule`


<!-- END_98a5c94ff64c824fc9202e5025af17b8 -->

<!-- START_1411a7b24b36bd9e8208b47212d346e4 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/schedule/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/schedule/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/schedule/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/schedule/{telescopeEntryId}`


<!-- END_1411a7b24b36bd9e8208b47212d346e4 -->

<!-- START_f25acbb3e06a57e8411a14e4694c75fb -->
## List the entries of the given type.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/redis", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/redis");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/redis" 
```



### HTTP Request
`POST telescope/telescope-api/redis`


<!-- END_f25acbb3e06a57e8411a14e4694c75fb -->

<!-- START_3670bdc683457b53a7c2b118c10905d0 -->
## Get an entry with the given ID.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/redis/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/redis/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/redis/1" 
```


> Example response (404):

```json
{
    "message": "No query results for model [Laravel\\Telescope\\Storage\\EntryModel]."
}
```

### HTTP Request
`GET telescope/telescope-api/redis/{telescopeEntryId}`


<!-- END_3670bdc683457b53a7c2b118c10905d0 -->

<!-- START_af458f9f0b35f66bde272f4b973c6637 -->
## Get all of the tags being monitored.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/telescope-api/monitored-tags", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/monitored-tags");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/telescope-api/monitored-tags" 
```


> Example response (200):

```json
{
    "tags": []
}
```

### HTTP Request
`GET telescope/telescope-api/monitored-tags`


<!-- END_af458f9f0b35f66bde272f4b973c6637 -->

<!-- START_b7f76aeac39a9c3f089e2da6d4b6b38a -->
## Begin monitoring the given tag.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/monitored-tags", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/monitored-tags");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/monitored-tags" 
```



### HTTP Request
`POST telescope/telescope-api/monitored-tags`


<!-- END_b7f76aeac39a9c3f089e2da6d4b6b38a -->

<!-- START_bdcab0f8e9b0350c8661f5c890333e8a -->
## Stop monitoring the given tag.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/monitored-tags/delete", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/monitored-tags/delete");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/monitored-tags/delete" 
```



### HTTP Request
`POST telescope/telescope-api/monitored-tags/delete`


<!-- END_bdcab0f8e9b0350c8661f5c890333e8a -->

<!-- START_420018fbba86099da7b9c8db6d9bdc8d -->
## Toggle recording.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/telescope/telescope-api/toggle-recording", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/telescope-api/toggle-recording");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/telescope/telescope-api/toggle-recording" 
```



### HTTP Request
`POST telescope/telescope-api/toggle-recording`


<!-- END_420018fbba86099da7b9c8db6d9bdc8d -->

<!-- START_4030988e35daa5fb1ec401a4d536dc96 -->
## Display the Telescope view.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/telescope/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/telescope/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/telescope/1" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET telescope/{view?}`


<!-- END_4030988e35daa5fb1ec401a4d536dc96 -->

<!-- START_66e08d3cc8222573018fed49e121e96d -->
## Show the application&#039;s login form.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/login", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/login");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/login" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET login`


<!-- END_66e08d3cc8222573018fed49e121e96d -->

<!-- START_ba35aa39474cb98cfb31829e70eb8b74 -->
## Handle a login request to the application.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/login", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/login");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/login" 
```



### HTTP Request
`POST login`


<!-- END_ba35aa39474cb98cfb31829e70eb8b74 -->

<!-- START_e65925f23b9bc6b93d9356895f29f80c -->
## Log the user out of the application.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/logout", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/logout");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/logout" 
```



### HTTP Request
`POST logout`


<!-- END_e65925f23b9bc6b93d9356895f29f80c -->

<!-- START_ff38dfb1bd1bb7e1aa24b4e1792a9768 -->
## Show the application registration form.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/register", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/register");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/register" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET register`


<!-- END_ff38dfb1bd1bb7e1aa24b4e1792a9768 -->

<!-- START_d7aad7b5ac127700500280d511a3db01 -->
## Handle a registration request for the application.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/register", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/register");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/register" 
```



### HTTP Request
`POST register`


<!-- END_d7aad7b5ac127700500280d511a3db01 -->

<!-- START_d72797bae6d0b1f3a341ebb1f8900441 -->
## Display the form to request a password reset link.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/password/reset", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/password/reset");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/password/reset" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET password/reset`


<!-- END_d72797bae6d0b1f3a341ebb1f8900441 -->

<!-- START_feb40f06a93c80d742181b6ffb6b734e -->
## Send a reset link to the given user.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/password/email", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/password/email");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/password/email" 
```



### HTTP Request
`POST password/email`


<!-- END_feb40f06a93c80d742181b6ffb6b734e -->

<!-- START_e1605a6e5ceee9d1aeb7729216635fd7 -->
## Display the password reset view for the given token.

If no token is present, display the link request form.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/password/reset/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/password/reset/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/password/reset/1" 
```


> Example response (200):

```json
null
```

### HTTP Request
`GET password/reset/{token}`


<!-- END_e1605a6e5ceee9d1aeb7729216635fd7 -->

<!-- START_cafb407b7a846b31491f97719bb15aef -->
## Reset the given user&#039;s password.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/password/reset", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/password/reset");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/password/reset" 
```



### HTTP Request
`POST password/reset`


<!-- END_cafb407b7a846b31491f97719bb15aef -->

<!-- START_f2053329808ba0187dbccdf16155f31a -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/usuario/wallet", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/wallet");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/usuario/wallet" 
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET usuario/wallet`


<!-- END_f2053329808ba0187dbccdf16155f31a -->

<!-- START_097340c2b877afc8ca68450224c783ed -->
## usuario/add-retiro
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/usuario/add-retiro", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/usuario/add-retiro");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/usuario/add-retiro" 
```



### HTTP Request
`POST usuario/add-retiro`


<!-- END_097340c2b877afc8ca68450224c783ed -->

<!-- START_4474f8fc2233dc1ed39e14a70b326972 -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/permissions", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/permissions");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/permissions" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET permissions`


<!-- END_4474f8fc2233dc1ed39e14a70b326972 -->

<!-- START_83f315bc2983d7d8cf54d00d05fa078f -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/permissions/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/permissions/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/permissions/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET permissions/create`


<!-- END_83f315bc2983d7d8cf54d00d05fa078f -->

<!-- START_0ff5b55b8896ca9dae04f57bf917646d -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/permissions", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/permissions");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/permissions" 
```



### HTTP Request
`POST permissions`


<!-- END_0ff5b55b8896ca9dae04f57bf917646d -->

<!-- START_76b81f2dafe7c8380863ec7a30bf0d76 -->
## Display the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/permissions/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/permissions/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/permissions/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET permissions/{permission}`


<!-- END_76b81f2dafe7c8380863ec7a30bf0d76 -->

<!-- START_cb16ad31dbf3e4c1c90ad446402dcb10 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/permissions/1/edit", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/permissions/1/edit");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/permissions/1/edit" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET permissions/{permission}/edit`


<!-- END_cb16ad31dbf3e4c1c90ad446402dcb10 -->

<!-- START_9325b6759cf5b3f1d516140b53acf3ba -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/permissions/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/permissions/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/permissions/1" 
```



### HTTP Request
`PUT permissions/{permission}`

`PATCH permissions/{permission}`


<!-- END_9325b6759cf5b3f1d516140b53acf3ba -->

<!-- START_361d82c3a2a3a9bdcb8fe9a3a716b2f1 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/permissions/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/permissions/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/permissions/1" 
```



### HTTP Request
`DELETE permissions/{permission}`


<!-- END_361d82c3a2a3a9bdcb8fe9a3a716b2f1 -->

<!-- START_50bc1ec7ad747cb5a92b20f1e3a28abf -->
## Display a listing of the resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies`


<!-- END_50bc1ec7ad747cb5a92b20f1e3a28abf -->

<!-- START_a71d71bcd2f3568fd0442cbaf967a379 -->
## Show the form for creating a new resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies/create", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/create");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies/create" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies/create`


<!-- END_a71d71bcd2f3568fd0442cbaf967a379 -->

<!-- START_0d395af2d7c07499bc3e0e5c6ad36693 -->
## Store a newly created resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/companies/teams12", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/teams12");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/companies/teams12" 
```



### HTTP Request
`POST companies/teams12`


<!-- END_0d395af2d7c07499bc3e0e5c6ad36693 -->

<!-- START_5fdb8eea1823663de1bf0bc6ee952491 -->
## Show the form for editing the specified resource.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies/edit/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/edit/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies/edit/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies/edit/{id}`


<!-- END_5fdb8eea1823663de1bf0bc6ee952491 -->

<!-- START_8cb71cb29cc3d4115108ec89d6f655bc -->
## Update the specified resource in storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->put("/companies/edit/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/edit/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X PUT "/companies/edit/1" 
```



### HTTP Request
`PUT companies/edit/{id}`


<!-- END_8cb71cb29cc3d4115108ec89d6f655bc -->

<!-- START_489b67661d7a4fee060a0acc219ddd1e -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/companies/destroy/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/destroy/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/companies/destroy/1" 
```



### HTTP Request
`DELETE companies/destroy/{id}`


<!-- END_489b67661d7a4fee060a0acc219ddd1e -->

<!-- START_cfa705324215871f1a9e5db484a03f44 -->
## Switch to the given team.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies/switch/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/switch/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies/switch/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies/switch/{id}`


<!-- END_cfa705324215871f1a9e5db484a03f44 -->

<!-- START_a6ab4895c979cee327e0db7112acfa1d -->
## Show the members of the given team.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies/members/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/members/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies/members/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies/members/{id}`


<!-- END_a6ab4895c979cee327e0db7112acfa1d -->

<!-- START_77cb381ed3551b4f4210b67c6aae428d -->
## Resend an invitation mail.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies/members/resend/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/members/resend/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies/members/resend/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies/members/resend/{invite_id}`


<!-- END_77cb381ed3551b4f4210b67c6aae428d -->

<!-- START_7ce9737adcb20f717c03ece815320fa4 -->
## companies/members/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/companies/members/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/members/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/companies/members/1" 
```



### HTTP Request
`POST companies/members/{id}`


<!-- END_7ce9737adcb20f717c03ece815320fa4 -->

<!-- START_cd6b4dda84f2880001cab7ec6b1fdda8 -->
## Remove the specified resource from storage.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/companies/members/1/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/members/1/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/companies/members/1/1" 
```



### HTTP Request
`DELETE companies/members/{id}/{user_id}`


<!-- END_cd6b4dda84f2880001cab7ec6b1fdda8 -->

<!-- START_5a5a9a632ed7a11bd86bb3825248c29b -->
## Accept the given invite

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies/accept/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/accept/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies/accept/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies/accept/{token}`


<!-- END_5a5a9a632ed7a11bd86bb3825248c29b -->

<!-- START_77fd7e97ec9ee14317d2b93540fc4dbe -->
## companies/permissions/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/companies/permissions/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/permissions/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/companies/permissions/1" 
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET companies/permissions/{id}`


<!-- END_77fd7e97ec9ee14317d2b93540fc4dbe -->

<!-- START_398d664463067d1449814dc4cfdd71a9 -->
## companies/permissions/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post("/companies/permissions/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/companies/permissions/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X POST "/companies/permissions/1" 
```



### HTTP Request
`POST companies/permissions/{id}`


<!-- END_398d664463067d1449814dc4cfdd71a9 -->

<!-- START_5f5cfbda48ef1d60b8d1d2a1baba8b5e -->
## Show the application dashboard.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get("/invite/register/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/invite/register/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X GET -G "/invite/register/1" 
```


> Example response (302):

```json
null
```

### HTTP Request
`GET invite/register/{token}`


<!-- END_5f5cfbda48ef1d60b8d1d2a1baba8b5e -->

<!-- START_a7299ec7f9ff0bb6c730e9f36a81c49a -->
## invite/delete/{id}
> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete("/invite/delete/1", [
]);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


```javascript
const url = new URL("/invite/delete/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```bash
curl -X DELETE "/invite/delete/1" 
```



### HTTP Request
`DELETE invite/delete/{id}`


<!-- END_a7299ec7f9ff0bb6c730e9f36a81c49a -->


