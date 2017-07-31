# Client Success CRM Integration

This project aims to illustrate the functionality required for a custom Client Success CRM to integrate with SocialSignIn.

## Sample Integration Installation


```bash
docker build -t crm-integration-image .
docker run -e SHARED_SECRET=changeme --rm --name crm-integration crm-integration-image
```

Code should work on a generic-ish PHP 7 Linux server if you wish to deploy it manually. Instructions should be within the Dockerfile. 

It requires a SHARED\_SECRET environment variable to be set.

## Configuration

The SHARED\_SECRET environment variable is used to verify that SocialSignIn made the CRM request, and for SocialSignIn to  verify responses.

The signing works by adding a sha256 hash\_hmac query parameter on all requests (see: http://php.net/hash_hmac )

The CS\_USERNAME environment variable is used to authenticate with client success.

The CS\_PASSWORD environment variable is used to authenticate with client success.

You can choose to ignore these parameters if you so wish.

## Required HTTP Interface

Any third party / custom integration needs to support the following :

## Search 

 * GET request, with signed parameters (see SHARED\_SECRET above)
 * Endpoint is specified by you when adding the integration
 * Parameter 'q' contains the search string.
 * Return json (application/json mimetype).
 * e.g. https://my.integration.com/search?q=bob
   
### Request 
 
 Assuming a shared secret of 'changeme!'
 
 A request from SocialSignIn searching for users matching 'red' might look like :
 
 ```raw
 GET $CustomUrl?q=red&expires=1500472622&sig=7c9a0a55dc2d1542ec736b8021f048da114fcba11ca1fb0219c122dfd789e48c HTTP/1.1
 Host: ....
 Accept: application/json
 

 ```

Where :

 * expires - unix timestamp with a small TTL value added.
 * sig - sha256 hash of GET query (q=red&expires=12345678)
 * q - search term 

 
#### Example request validation 

You **should** check that the expires value in the URL is greater or equal to your current system timestamp. 

You **should** check that the signature is valid.


```php
$our_timestamp = time();

// ... logic to check existance of expires/sig parameters in query string.

$url = parse_str($_SERVER['QUERY_STRING'], $params);
$actual_sig = $params['sig'];
$request_time = $params['expires'];


if($request_time < $our_timestamp) {
   // request from too long ago?
}

unset($params['sig']);
// hash_hmac('sha256', 'q=red&expires=1500472622', 'changeme!');
$expected_sig = hash_hmac('sha256', http_build_query($params) , 'changeme!');

if($expected_sig != $actual_sig) { 
    // handle error 
}

```

### Response
 
```json
{
    "results" : [
        { "id": 1, "name": "Susan Red"} ,
        { "id": 4, "name": "Frank Redford"} 
    ]
}
```
 
## Get Specific User
 
 * GET request, with signed parameters
 * Endpoint is specified by you when adding the integration
 * Returns HTML (iframe content).

### Request 

```raw
GET $CustomUrl/iframe?id=12345&expires=1234567&sig=hashhashhash HTTP/1.1
Host: .....

```

 * You **should** verify the 'sig' URL parameter is correct (see above)
 * You **should** verify the 'expires' URL parameter is greater or equal to the current system time.
 
### Response

HTML to render the user, as determined by your internal requirements.

For example :

````html
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Some user</title>
</head>
<body>
    <h1>Fred Bloggs</h1>
    
    <p>Fred <a href="https://internal.crm/employee?id=12345">crm</a></p>
    
    <p>Email: test@example.com</p>
    <p>Sales (2017): £390.46</p>
    <p>Sales (2016): £39.42</p>
    <h2>Notes</h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce magna magna, convallis quis auctor bibendum, rutrum ut risus. Nulla dictum pulvinar turpis id sodales. Maecenas gravida quam nibh, accumsan egestas nisl mattis ut.</p>
</body>
</html>
````

This is rendered as an iframe within the SSI webapp.